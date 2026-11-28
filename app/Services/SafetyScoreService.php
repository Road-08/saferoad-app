<?php

namespace App\Services;

use App\Enums\InspectionStatus;
use App\Models\DangerZone;
use App\Models\EnvironmentalCondition;
use App\Models\Rute;
use App\Models\SafetyFacility;
use App\Models\SafetyScoreLog;
use App\Models\SecurityReport;
use Illuminate\Support\Collection;

class SafetyScoreService
{
    /**
     * Hitung Safety Score untuk area titik pusat tertentu (latitude, longitude, radius).
     *
     * @return array{
     *     score: int,
     *     level: string,
     *     mode: string,
     *     center: array{latitude: float, longitude: float},
     *     radius_km: float,
     *     components: array<string, int>,
     *     factors: array<int, array<string, mixed>>,
     *     active_reports: array<int, mixed>,
     *     facilities_summary: array<string, int>
     * }
     */
    public function calculateAreaScore(
        float $latitude,
        float $longitude,
        float $radiusKm = 0.50,
        string $mode = 'general'
    ): array {
        $facilities = SafetyFacility::all();
        $dangerZones = DangerZone::active()->get();
        $reports = SecurityReport::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', '!=', 'rejected')
            ->where('validation_status', '!=', 'rejected')
            ->get();
        $envConditions = EnvironmentalCondition::verified()->get();

        // 1. FASILITAS KEAMANAN DALAM RADIUS
        $cctvActiveCount = 0;
        $lightActiveCount = 0;
        $lightDamagedCount = 0;
        $postActiveCount = 0;
        $countedFacilityIds = [];

        foreach ($facilities as $facility) {
            if (in_array($facility->id, $countedFacilityIds, true)) {
                continue;
            }

            $distKm = $this->haversineKm($latitude, $longitude, (float) $facility->latitude, (float) $facility->longitude);
            if ($distKm <= $radiusKm) {
                $countedFacilityIds[] = $facility->id;
                $isOperational = ($facility->status === 'active' && $facility->condition !== 'rusak' && $facility->condition !== 'tidak_aktif');

                if ($facility->type === 'street_light') {
                    if ($isOperational) {
                        $lightActiveCount++;
                    } else {
                        $lightDamagedCount++;
                    }
                } elseif ($facility->type === 'cctv') {
                    if ($isOperational) {
                        $cctvActiveCount++;
                    }
                } elseif ($facility->type === 'security_post') {
                    if ($isOperational) {
                        $postActiveCount++;
                    }
                }
            }
        }

        // 2. KONDISI LINGKUNGAN OBSERVASI
        $activityScores = [];
        $envLightingScores = [];
        $countedEnvIds = [];

        foreach ($envConditions as $env) {
            if (in_array($env->id, $countedEnvIds, true)) {
                continue;
            }

            $distKm = $this->haversineKm($latitude, $longitude, (float) $env->latitude, (float) $env->longitude);
            if ($distKm <= $radiusKm) {
                $countedEnvIds[] = $env->id;

                if ($env->activity_level === 'ramai') {
                    $activityScores[] = 95;
                } elseif ($env->activity_level === 'sedang') {
                    $activityScores[] = 70;
                } else {
                    $activityScores[] = 30;
                }

                if ($env->lighting_condition === 'terang') {
                    $envLightingScores[] = 100;
                } elseif ($env->lighting_condition === 'cukup') {
                    $envLightingScores[] = 75;
                } elseif ($env->lighting_condition === 'minim') {
                    $envLightingScores[] = 40;
                } else {
                    $envLightingScores[] = 15;
                }
            }
        }

        // 3. TITIK RAWAN (Danger Zones)
        $dangerCount = 0;
        $womenDangerCount = 0;
        $countedDangerIds = [];

        foreach ($dangerZones as $dz) {
            if (in_array($dz->id, $countedDangerIds, true)) {
                continue;
            }

            $distKm = $this->haversineKm($latitude, $longitude, (float) $dz->latitude, (float) $dz->longitude);
            if ($distKm <= $radiusKm) {
                $countedDangerIds[] = $dz->id;
                $dangerCount++;

                $dzText = strtolower($dz->name.' '.$dz->category.' '.($dz->description ?? ''));
                $isWomenRelated = str_contains($dzText, 'pelecehan')
                    || str_contains($dzText, 'catcalling')
                    || str_contains($dzText, 'intimidasi')
                    || str_contains($dzText, 'kekerasan')
                    || str_contains($dzText, 'perempuan');

                if ($isWomenRelated || $dz->danger_level === 'tinggi') {
                    $womenDangerCount++;
                }
            }
        }

        // 4. LAPORAN KEJADIAN MASYARAKAT (Dynamic Weighting & Anti Double Counting)
        $countedReportIds = [];
        $locationClusters = []; // Untuk pencegahan akumulasi berlebihan pada titik identik
        $activeReportsSummary = [];

        $generalWeightedPenalty = 0.0;
        $womenWeightedPenalty = 0.0;
        $totalReportsInArea = 0;
        $verifiedCount = 0;
        $unverifiedCount = 0;
        $handledCount = 0;

        foreach ($reports as $report) {
            if (in_array($report->id, $countedReportIds, true)) {
                continue;
            }

            $distKm = $this->haversineKm($latitude, $longitude, (float) $report->latitude, (float) $report->longitude);
            if ($distKm <= $radiusKm) {
                $countedReportIds[] = $report->id;
                $totalReportsInArea++;

                $inspectionStatus = InspectionStatus::normalize($report->inspection_status);
                $statusRiskWeight = $inspectionStatus->riskWeight();

                // Jika laporan tidak terbukti atau kondisi aman, risiko diabaikan (bobot 0)
                if ($statusRiskWeight <= 0.0) {
                    continue;
                }

                if ($inspectionStatus === InspectionStatus::VERIFIED_TRUE) {
                    $verifiedCount++;
                } elseif ($inspectionStatus === InspectionStatus::HANDLED) {
                    $handledCount++;
                } else {
                    $unverifiedCount++;
                }

                // Bobot Jarak Laporan dari Pusat Area
                $distanceWeight = ($distKm <= 0.15) ? 1.0 : (($distKm <= 0.30) ? 0.85 : 0.65);

                // Bobot Usia Laporan (Recency Decay)
                $reportDate = $report->incident_at ?: $report->created_at;
                $daysAgo = $reportDate ? $reportDate->diffInDays(now()) : 0;
                $recencyWeight = ($daysAgo <= 7) ? 1.0 : (($daysAgo <= 30) ? 0.80 : 0.50);

                // Bobot Tingkat Keparahan Laporan
                $sev = strtolower((string) ($report->severity ?? 'sedang'));
                $severityMultiplier = ($sev === 'tinggi') ? 1.5 : (($sev === 'rendah') ? 0.6 : 1.0);

                // Base Penalty berdasarkan kategori
                $repText = strtolower($report->category.' '.$report->description);
                $isCatcalling = str_contains($repText, 'catcalling') || str_contains($repText, 'suit') || str_contains($repText, 'siulan');
                $isHarassment = str_contains($repText, 'pelecehan') || str_contains($repText, 'asusila') || str_contains($repText, 'cabul') || str_contains($repText, 'raba');
                $isIntimidation = str_contains($repText, 'intimidasi') || str_contains($repText, 'kekerasan') || str_contains($repText, 'ancam') || str_contains($repText, 'aniaya');
                $isWomenIncident = $isCatcalling || $isHarassment || $isIntimidation;

                $isTheft = str_contains($repText, 'curi') || str_contains($repText, 'pencurian') || str_contains($repText, 'maling') || str_contains($repText, 'copet');
                $isRobbery = str_contains($repText, 'rampok') || str_contains($repText, 'perampokan') || str_contains($repText, 'begal') || str_contains($repText, 'penodongan');

                $basePenalty = $isWomenIncident
                    ? ($isHarassment ? 28 : ($isIntimidation ? 24 : 18))
                    : ($isRobbery ? 26 : ($isTheft ? 18 : 14));

                // Anti Double Counting: Cek klaster lokasi identik (radius < 25m)
                $locKey = round((float) $report->latitude, 4).','.round((float) $report->longitude, 4);
                $clusterRepeatFactor = 1.0;
                if (isset($locationClusters[$locKey])) {
                    $locationClusters[$locKey]++;
                    // Laporan ke-2 dan seterusnya pada titik identik teredam (30% bobot pengurang)
                    $clusterRepeatFactor = 0.30;
                } else {
                    $locationClusters[$locKey] = 1;
                }

                $calculatedPenalty = $basePenalty * $severityMultiplier * $recencyWeight * $distanceWeight * $statusRiskWeight * $clusterRepeatFactor;

                if ($isWomenIncident) {
                    $womenWeightedPenalty += $calculatedPenalty;
                } else {
                    $generalWeightedPenalty += $calculatedPenalty;
                }

                $activeReportsSummary[] = [
                    'id' => $report->id,
                    'code' => $report->report_code,
                    'category' => $report->category,
                    'status' => $report->status,
                    'inspection_status' => $inspectionStatus->value,
                    'inspection_status_label' => $inspectionStatus->label(),
                    'risk_weight' => $statusRiskWeight,
                    'penalty_contribution' => round($calculatedPenalty, 2),
                    'distance_km' => round($distKm, 3),
                ];
            }
        }

        // ==========================================
        // KALKULASI 4 KOMPONEN
        // ==========================================

        // 1. Penerangan Jalan (Bobot 20%)
        $baseLightingScore = ($lightActiveCount >= 3)
            ? 100
            : (($lightActiveCount == 2) ? 80 : (($lightActiveCount == 1) ? 60 : 25));

        if (! empty($envLightingScores)) {
            $avgEnvLight = array_sum($envLightingScores) / count($envLightingScores);
            $componentLighting = (int) round(($baseLightingScore * 0.6) + ($avgEnvLight * 0.4));
        } else {
            $componentLighting = $baseLightingScore;
        }
        if ($lightDamagedCount > 0) {
            $componentLighting = max(10, $componentLighting - ($lightDamagedCount * 12));
        }

        // 2. Fasilitas Keamanan (Bobot 15%)
        $facilityPoints = ($cctvActiveCount * 40) + ($postActiveCount * 45);
        $componentFacility = min(100, max(20, 20 + $facilityPoints));

        // 3. Aktivitas Lingkungan (Bobot 15%)
        if (! empty($activityScores)) {
            $componentActivity = (int) round(array_sum($activityScores) / count($activityScores));
        } else {
            $componentActivity = 60; // Netral jika belum ada titik observasi
        }

        // 4. Kriminalitas & Risiko Kejadian (Bobot 50%)
        if ($mode === 'women') {
            $componentCrime = max(0, min(100, (int) round(100 - $womenWeightedPenalty - ($womenDangerCount * 18))));
        } else {
            $componentCrime = max(0, min(100, (int) round(100 - $generalWeightedPenalty - ($dangerCount * 15))));
        }

        // Skor Akhir: 50% + 20% + 15% + 15%
        $rawScore = ($componentCrime * 0.50) +
            ($componentLighting * 0.20) +
            ($componentFacility * 0.15) +
            ($componentActivity * 0.15);

        $finalScore = (int) round(max(0, min(100, $rawScore)));

        $level = ($finalScore >= 80)
            ? 'aman'
            : (($finalScore >= 60) ? 'sedang' : 'rawan');

        $levelLabel = ($finalScore >= 80)
            ? ($mode === 'women' ? 'Rendah (Paling Aman)' : 'Rendah (Aman)')
            : (($finalScore >= 60) ? 'Sedang' : 'Tinggi (Rawan)');

        $factors = [
            [
                'key' => 'crime_risk',
                'label' => $mode === 'women' ? 'Risiko Keamanan Perempuan (50%)' : 'Kriminalitas & Risiko Kejadian (50%)',
                'score' => $componentCrime,
                'weight' => 0.50,
                'note' => "{$totalReportsInArea} insiden terdata ({$verifiedCount} terverifikasi, {$unverifiedCount} belum verifikasi, {$handledCount} tertangani)",
            ],
            [
                'key' => 'lighting',
                'label' => 'Penerangan Jalan (20%)',
                'score' => $componentLighting,
                'weight' => 0.20,
                'note' => "{$lightActiveCount} lampu jalan aktif, {$lightDamagedCount} rusak",
            ],
            [
                'key' => 'facilities',
                'label' => 'Fasilitas Keamanan (15%)',
                'score' => $componentFacility,
                'weight' => 0.15,
                'note' => "{$cctvActiveCount} CCTV aktif & {$postActiveCount} pos pengamanan",
            ],
            [
                'key' => 'activity',
                'label' => 'Aktivitas Lingkungan (15%)',
                'score' => $componentActivity,
                'weight' => 0.15,
                'note' => ! empty($activityScores) ? 'Data observasi aktivitas warga tercatat' : 'Estimasi rata-rata aktivitas',
            ],
        ];

        return [
            'score' => $finalScore,
            'level' => $level,
            'level_label' => $levelLabel,
            'mode' => $mode,
            'center' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
            'radius_km' => $radiusKm,
            'components' => [
                'crime_score_50' => $componentCrime,
                'lighting_score_20' => $componentLighting,
                'facility_score_15' => $componentFacility,
                'activity_score_15' => $componentActivity,
            ],
            'factors' => $factors,
            'active_reports' => $activeReportsSummary,
            'facilities_summary' => [
                'cctv_active' => $cctvActiveCount,
                'lights_active' => $lightActiveCount,
                'lights_damaged' => $lightDamagedCount,
                'posts_active' => $postActiveCount,
                'danger_zones' => $dangerCount,
            ],
        ];
    }

    /**
     * Catat riwayat audit perubahan skor.
     */
    public function recordScoreChange(
        string $targetType,
        string $targetIdentifier,
        float $scoreBefore,
        float $scoreAfter,
        string $triggerEvent,
        ?int $securityReportId = null,
        ?int $actorId = null,
        ?string $actorRole = null,
        array $details = [],
        ?float $latitude = null,
        ?float $longitude = null,
        float $radiusKm = 0.50
    ): SafetyScoreLog {
        return SafetyScoreLog::create([
            'target_type' => $targetType,
            'target_identifier' => $targetIdentifier,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius_km' => $radiusKm,
            'score_before' => $scoreBefore,
            'score_after' => $scoreAfter,
            'score_change' => round($scoreAfter - $scoreBefore, 2),
            'trigger_event' => $triggerEvent,
            'security_report_id' => $securityReportId,
            'actor_id' => $actorId,
            'actor_role' => $actorRole,
            'details' => $details,
        ]);
    }

    /**
     * Memicu perhitungan ulang skor untuk area laporan yang terdampak.
     */
    public function recalculateAreaForReport(
        SecurityReport $report,
        string $triggerEvent,
        ?int $actorId = null,
        ?string $actorRole = null,
        ?float $previousScore = null
    ): ?SafetyScoreLog {
        if (! $report->latitude || ! $report->longitude) {
            return null;
        }

        $lat = (float) $report->latitude;
        $lng = (float) $report->longitude;
        $radiusKm = (float) config('safetyscore.default_area_radius_km', 0.50);

        // Jika score before tidak diberikan, ambil log sebelumnya atau estimasi
        if ($previousScore === null) {
            $lastLog = SafetyScoreLog::forArea($lat, $lng, $radiusKm)->latest()->first();
            $scoreBefore = $lastLog ? (float) $lastLog->score_after : 80.0;
        } else {
            $scoreBefore = $previousScore;
        }

        $newCalculation = $this->calculateAreaScore($lat, $lng, $radiusKm, 'general');
        $scoreAfter = (float) $newCalculation['score'];

        $log = $this->recordScoreChange(
            targetType: 'area',
            targetIdentifier: "{$lat},{$lng}",
            scoreBefore: $scoreBefore,
            scoreAfter: $scoreAfter,
            triggerEvent: $triggerEvent,
            securityReportId: $report->id,
            actorId: $actorId,
            actorRole: $actorRole,
            details: [
                'components' => $newCalculation['components'],
                'report_code' => $report->report_code,
                'category' => $report->category,
                'inspection_status' => $report->inspection_status,
                'status' => $report->status,
                'level' => $newCalculation['level'],
            ],
            latitude: $lat,
            longitude: $lng,
            radiusKm: $radiusKm
        );

        // Perbarui juga data rute pada tabel routes jika melewati area ini
        $this->updateRoutesNearLocation($lat, $lng, $radiusKm);

        return $log;
    }

    /**
     * Perbarui skor pada rute di database yang berdekatan dengan lokasi tertentu.
     */
    public function updateRoutesNearLocation(float $latitude, float $longitude, float $radiusKm = 0.50): void
    {
        $routes = Rute::all();
        if ($routes->isEmpty()) {
            return;
        }

        foreach ($routes as $route) {
            // Hitung ulang skor dengan RouteSafetyService jika rute memiliki koordinat
            // atau perbarui security_score mendekati skor area
            $areaScore = $this->calculateAreaScore($latitude, $longitude, $radiusKm, 'general');
            $route->update([
                'security_score' => $areaScore['score'],
                'security_level' => $areaScore['level'],
            ]);
        }
    }

    /**
     * Ambil daftar laporan aktif dalam radius area tertentu.
     */
    public function getAreaReports(float $latitude, float $longitude, float $radiusKm = 0.50, ?string $status = null): Collection
    {
        $query = SecurityReport::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['user:id,name', 'inspectedBy:id,name']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get()->filter(function ($report) use ($latitude, $longitude, $radiusKm) {
            $dist = $this->haversineKm($latitude, $longitude, (float) $report->latitude, (float) $report->longitude);

            return $dist <= $radiusKm;
        })->values();
    }

    /**
     * Menghitung jarak haversine antara dua titik koordinat (km).
     */
    public function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
