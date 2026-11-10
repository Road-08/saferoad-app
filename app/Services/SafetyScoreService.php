<?php

namespace App\Services;

use App\Models\Report;
use App\Models\SecurityFacility;
use App\Services\Contracts\CrowdLevelProvider;
use App\Services\Geometry\Haversine;
use App\Services\Geometry\RouteSampler;
use Illuminate\Database\Eloquent\Builder;

class SafetyScoreService
{
    public const DISCLAIMER = 'Skor ini merupakan estimasi berdasarkan data yang tersedia dan bukan jaminan kondisi keamanan di lapangan.';

    private CrowdLevelProvider $crowdLevelProvider;

    public function __construct(
        private SafetyParameters $parameters,
        private RouteSampler $routeSampler,
        private Haversine $haversine,
        ?CrowdLevelProvider $crowdLevelProvider = null,
    ) {
        $this->crowdLevelProvider = $crowdLevelProvider ?? new NullCrowdLevelProvider;
    }

    /**
     * Score multiple routes without persisting them.
     *
     * @param  array<int, array{id: string|null, coordinates: array<int, array{0: float|int, 1: float|int}>}>  $routes
     * @return array<int, array{id: string|null, score: int|null, level: string, level_label: string, note: string|null, factors: array<int, array{key: string, label: string, score: float|null, weight: float, available: bool, note: string|null}>}>
     */
    public function score(array $routes): array
    {
        $results = [];

        foreach ($routes as $route) {
            $result = $this->scoreRoute($route['coordinates'] ?? []);
            $results[] = ['id' => $route['id'] ?? null] + $result;
        }

        return $results;
    }

    /**
     * @param  array<int, array{0: float|int, 1: float|int}>  $coordinates
     * @return array{score: int|null, level: string, level_label: string, note: string|null, factors: array<int, array{key: string, label: string, score: float|null, weight: float, available: bool, note: string|null}>}
     */
    private function scoreRoute(array $coordinates): array
    {
        $sampling = $this->routeSampler->sample(
            $coordinates,
            (float) $this->parameters->get('sample_spacing_m', 25),
            (int) $this->parameters->get('max_samples', 400),
        );
        $samples = $sampling['samples'];

        if (count($samples) < 2) {
            return $this->result(null, 'data_belum_cukup', $this->emptyFactors('Rute tidak memiliki cukup titik untuk dinilai.'), 'Data keamanan di sekitar rute ini belum cukup.');
        }

        $bounds = $this->expandedBounds(
            $samples,
            max(
                (float) $this->parameters->get('bbox_margin_m', 150),
                (float) $this->parameters->get('radii_meters.hazard', 75),
            ),
        );
        $postBounds = $this->expandedBounds(
            $samples,
            (float) $this->parameters->get('radii_meters.security_post_max_distance', 500),
        );

        // Keep each facility type query bounded to one eager-loaded query per route.
        $lightingFacilities = $this->facilitiesOfType('penerangan', $bounds);
        $cctvFacilities = $this->facilitiesOfType('cctv', $bounds);
        $securityPosts = $this->facilitiesOfType('pos_keamanan', $postBounds);
        $reports = $this->reportsInBounds($bounds);

        $hasActiveFacilitiesInArea = SecurityFacility::query()
            ->where('status', 'aktif')
            ->whereHas('location', fn (Builder $query) => $this->applyBounds($query, $postBounds))
            ->exists();
        $hasAnyReportsInArea = $reports->isNotEmpty();
        $hasAreaData = $hasActiveFacilitiesInArea || $hasAnyReportsInArea;
        $forwardedReports = $reports->where('status', 'diteruskan_ke_keamanan')->values();
        $hazardReportsInRadius = $this->reportsWithinRadius($forwardedReports, $samples);
        $crowdScore = $this->crowdScore($sampling['coordinates'], $samples);

        $factors = [
            $this->factor(
                'hazard',
                'Titik rawan',
                $hasAreaData ? $this->hazardScore($hazardReportsInRadius, $samples) : null,
                $hasAreaData,
                $hasAreaData && $hazardReportsInRadius->isEmpty() ? 'Tidak ada laporan diteruskan ke keamanan dalam radius rute.' : null,
            ),
            $this->factor(
                'lighting',
                'Penerangan',
                $this->facilityCoverage($lightingFacilities, $samples, (float) $this->parameters->get('radii_meters.lighting', 40)),
                $lightingFacilities->isNotEmpty(),
                $lightingFacilities->isEmpty() ? 'Tidak ada fasilitas penerangan aktif di sekitar rute.' : null,
            ),
            $this->factor(
                'crowd',
                'Keramaian',
                $crowdScore,
                $crowdScore !== null,
                $crowdScore === null ? 'Data keramaian tidak tersedia.' : null,
            ),
            $this->factor(
                'cctv',
                'CCTV',
                $this->facilityCoverage($cctvFacilities, $samples, (float) $this->parameters->get('radii_meters.cctv', 50)),
                $cctvFacilities->isNotEmpty(),
                $cctvFacilities->isEmpty() ? 'Tidak ada CCTV aktif di sekitar rute.' : null,
            ),
            $this->factor(
                'security_post',
                'Pos keamanan',
                $this->securityPostScore($securityPosts, $samples),
                $securityPosts->isNotEmpty(),
                $securityPosts->isEmpty() ? 'Tidak ada pos keamanan aktif di sekitar rute.' : null,
            ),
        ];

        $factors = $this->normalizeFactorWeights($factors);
        $availableCoreFactors = count(array_filter(
            $factors,
            fn (array $factor): bool => $factor['available'] && $factor['key'] !== 'crowd',
        ));

        if ($availableCoreFactors < (int) $this->parameters->get('min_available_factors', 3)) {
            foreach ($factors as &$factor) {
                $factor['note'] ??= 'Data keamanan di sekitar rute ini belum cukup.';
            }
            unset($factor);

            return $this->result(null, 'data_belum_cukup', $factors, 'Data keamanan di sekitar rute ini belum cukup.');
        }

        $scoreTotal = 0.0;
        foreach ($factors as $factor) {
            if ($factor['available'] && $factor['weight'] > 0 && $factor['score'] !== null) {
                $scoreTotal += $factor['score'] * $factor['weight'];
            }
        }

        $score = (int) round($scoreTotal);

        return $this->result($score, $this->levelFor($score), $factors);
    }

    /** @param  array{0: float, 1: float, 2: float, 3: float}  $bounds
     *  @return \Illuminate\Database\Eloquent\Collection<int, SecurityFacility>
     */
    private function facilitiesOfType(string $type, array $bounds)
    {
        return SecurityFacility::query()
            ->select(['facility_id', 'location_id', 'facility_type', 'status'])
            ->where('status', 'aktif')
            ->where('facility_type', $type)
            ->whereHas('location', fn (Builder $query) => $this->applyBounds($query, $bounds))
            ->with(['location:location_id,longitude,latitude'])
            ->get();
    }

    /** @param  array{0: float, 1: float, 2: float, 3: float}  $bounds
     *  @return \Illuminate\Database\Eloquent\Collection<int, Report>
     */
    private function reportsInBounds(array $bounds)
    {
        return Report::query()
            ->select(['report_id', 'location_id', 'category', 'event_time', 'status'])
            ->whereHas('location', fn (Builder $query) => $this->applyBounds($query, $bounds))
            ->with(['location:location_id,longitude,latitude'])
            ->get();
    }

    /** @param  iterable<int, Report>  $reports
     *  @param  array<int, array{0: float, 1: float}>  $samples
     *  @return \Illuminate\Support\Collection<int, Report>
     */
    private function reportsWithinRadius(iterable $reports, array $samples)
    {
        $radius = max(0, (float) $this->parameters->get('radii_meters.hazard', 75));

        return collect($reports)->filter(function (Report $report) use ($samples, $radius): bool {
            $location = $report->location;

            return $location !== null
                && $this->nearestDistance([$location->longitude, $location->latitude], $samples) <= $radius;
        })->values();
    }

    /** @param  iterable<int, Report>  $reports
     *  @param  array<int, array{0: float, 1: float}>  $samples
     */
    private function hazardScore(iterable $reports, array $samples): float
    {
        if (collect($reports)->isEmpty()) {
            return 100.0;
        }

        $hazardWeights = $this->parameters->get('hazard_weights', []);
        $halfLifeDays = max(1, (float) $this->parameters->get('recency_half_life_days', 180));
        $minimumFactor = min(1, max(0, (float) $this->parameters->get('recency_min_factor', 0.2)));
        $totalHazardUnits = 0.0;
        $now = now();

        foreach ($reports as $report) {
            $categoryWeight = (float) ($hazardWeights[$report->category] ?? $hazardWeights['default'] ?? 1);
            $ageDays = max(0, $report->event_time->diffInDays($now, false));
            $recencyFactor = max($minimumFactor, 0.5 ** ($ageDays / $halfLifeDays));
            $totalHazardUnits += max(0, $categoryWeight) * $recencyFactor;
        }

        $penalty = max(0, (float) $this->parameters->get('hazard_penalty_per_unit', 25));

        return min(100, max(0, 100 - ($totalHazardUnits / count($samples)) * $penalty));
    }

    /** @param  iterable<int, SecurityFacility>  $facilities
     *  @param  array<int, array{0: float, 1: float}>  $samples
     */
    private function facilityCoverage(iterable $facilities, array $samples, float $radius): ?float
    {
        $locations = [];
        foreach ($facilities as $facility) {
            if ($facility->location !== null) {
                $locations[] = [$facility->location->longitude, $facility->location->latitude];
            }
        }

        if ($locations === []) {
            return null;
        }

        $covered = 0;
        foreach ($samples as $sample) {
            foreach ($locations as $location) {
                if ($this->haversine->distanceMeters($sample, $location) <= $radius) {
                    $covered++;
                    break;
                }
            }
        }

        return 100 * $covered / count($samples);
    }

    /** @param  iterable<int, SecurityFacility>  $facilities
     *  @param  array<int, array{0: float, 1: float}>  $samples
     */
    private function securityPostScore(iterable $facilities, array $samples): ?float
    {
        $locations = [];
        foreach ($facilities as $facility) {
            if ($facility->location !== null) {
                $locations[] = [$facility->location->longitude, $facility->location->latitude];
            }
        }

        if ($locations === []) {
            return null;
        }

        $maximumDistance = max(1, (float) $this->parameters->get('radii_meters.security_post_max_distance', 500));
        $scores = [];

        foreach ($samples as $sample) {
            $nearestDistance = INF;
            foreach ($locations as $location) {
                $nearestDistance = min($nearestDistance, $this->haversine->distanceMeters($sample, $location));
            }

            $scores[] = max(0, 100 - ($nearestDistance / $maximumDistance * 100));
        }

        return array_sum($scores) / count($scores);
    }

    /** @param  array<int, array{0: float, 1: float}>  $routeCoordinates
     *  @param  array<int, array{0: float, 1: float}>  $samples
     */
    private function crowdScore(array $routeCoordinates, array $samples): ?float
    {
        $score = $this->crowdLevelProvider->scoreForRoute(
            $routeCoordinates,
            $samples,
            (int) $this->parameters->get('crowd.min_samples', 30),
        );

        return $score === null || ! is_finite($score) ? null : min(100, max(0, $score));
    }

    /** @param  array<int, array{key: string, label: string, score: float|null, weight: float, available: bool, note: string|null}>  $factors
     *  @return array<int, array{key: string, label: string, score: float|null, weight: float, available: bool, note: string|null}>
     */
    private function normalizeFactorWeights(array $factors): array
    {
        $weights = $this->parameters->get('weights', []);
        $availableWeightTotal = 0.0;

        foreach ($factors as $factor) {
            $weight = $weights[$factor['key']] ?? 0;
            if ($factor['available'] && is_numeric($weight) && (float) $weight > 0) {
                $availableWeightTotal += (float) $weight;
            }
        }

        foreach ($factors as &$factor) {
            $weight = $weights[$factor['key']] ?? 0;
            $factor['weight'] = $factor['available'] && $availableWeightTotal > 0 && is_numeric($weight) && (float) $weight > 0
                ? (float) $weight / $availableWeightTotal
                : 0.0;
        }
        unset($factor);

        return $factors;
    }

    /** @return array{key: string, label: string, score: float|null, weight: float, available: bool, note: string|null} */
    private function factor(string $key, string $label, ?float $score, bool $available, ?string $note = null): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'score' => $available ? $score : null,
            'weight' => 0.0,
            'available' => $available && $score !== null,
            'note' => $note,
        ];
    }

    /** @return array<int, array{key: string, label: string, score: float|null, weight: float, available: bool, note: string|null}> */
    private function emptyFactors(string $note): array
    {
        return [
            $this->factor('hazard', 'Titik rawan', null, false, $note),
            $this->factor('lighting', 'Penerangan', null, false, $note),
            $this->factor('crowd', 'Keramaian', null, false, 'Data keramaian tidak tersedia.'),
            $this->factor('cctv', 'CCTV', null, false, $note),
            $this->factor('security_post', 'Pos keamanan', null, false, $note),
        ];
    }

    /** @param  array<int, array{key: string, label: string, score: float|null, weight: float, available: bool, note: string|null}>  $factors
     *  @return array{score: int|null, level: string, level_label: string, note: string|null, factors: array<int, array{key: string, label: string, score: float|null, weight: float, available: bool, note: string|null}>}
     */
    private function result(?int $score, string $level, array $factors, ?string $note = null): array
    {
        return [
            'score' => $score,
            'level' => $level,
            'level_label' => match ($level) {
                'aman' => 'Aman',
                'sedang' => 'Sedang',
                'rawan' => 'Rawan',
                default => 'Data belum cukup',
            },
            'note' => $note,
            'factors' => $factors,
        ];
    }

    /** @param  array<int, array{0: float, 1: float}>  $samples */
    private function nearestDistance(array $coordinate, array $samples): float
    {
        $nearest = INF;
        foreach ($samples as $sample) {
            $nearest = min($nearest, $this->haversine->distanceMeters($coordinate, $sample));
        }

        return $nearest;
    }

    /** @param  array<int, array{0: float, 1: float}>  $samples
     *  @return array{0: float, 1: float, 2: float, 3: float}
     */
    private function expandedBounds(array $samples, float $marginMeters): array
    {
        $longitudes = array_column($samples, 0);
        $latitudes = array_column($samples, 1);
        $marginMeters = max(0, $marginMeters);
        $latitudeMargin = $marginMeters / 111_000;
        $middleLatitude = (min($latitudes) + max($latitudes)) / 2;
        $longitudeScale = max(0.01, cos(deg2rad($middleLatitude)));
        $longitudeMargin = $marginMeters / (111_000 * $longitudeScale);

        return [
            max(-180, min($longitudes) - $longitudeMargin),
            max(-90, min($latitudes) - $latitudeMargin),
            min(180, max($longitudes) + $longitudeMargin),
            min(90, max($latitudes) + $latitudeMargin),
        ];
    }

    /** @param  array{0: float, 1: float, 2: float, 3: float}  $bounds */
    private function applyBounds(Builder $query, array $bounds): void
    {
        [$minLongitude, $minLatitude, $maxLongitude, $maxLatitude] = $bounds;
        $query->whereBetween('longitude', [$minLongitude, $maxLongitude])
            ->whereBetween('latitude', [$minLatitude, $maxLatitude]);
    }

    private function levelFor(int $score): string
    {
        if ($score >= (int) $this->parameters->get('thresholds.aman', 80)) {
            return 'aman';
        }

        if ($score >= (int) $this->parameters->get('thresholds.sedang', 60)) {
            return 'sedang';
        }

        return 'rawan';
    }
}
