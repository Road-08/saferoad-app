<?php

namespace App\Services;

use App\Models\DangerZone;
use App\Models\SafetyFacility;
use App\Models\SecurityReport;

class RouteSafetyService
{
    /**
     * Menghitung skor keselamatan untuk kumpulan rute dari frontend.
     *
     * @param  array<int, array{id: string, coordinates: array<int, array<int, float>>}>  $routes
     * @return array<int, array<string, mixed>>
     */
    public function calculateScores(array $routes): array
    {
        $facilities = SafetyFacility::active()->get();
        $dangerZones = DangerZone::active()->get();
        $recentReports = SecurityReport::activeOnMap()->get();

        $results = [];

        foreach ($routes as $route) {
            $routeId = (string) ($route['id'] ?? 'unknown');
            $coordinates = $route['coordinates'] ?? [];

            $normalizedPoints = $this->normalizeCoordinates($coordinates);

            if (empty($normalizedPoints)) {
                $results[] = [
                    'id' => $routeId,
                    'score' => null,
                    'level' => 'data_belum_cukup',
                    'factors' => [],
                ];
                continue;
            }

            // Hitung kedekatan fasilitas
            $cctvCount = 0;
            $lightCount = 0;
            $postCount = 0;

            foreach ($facilities as $facility) {
                if ($this->isNearRoute($facility->latitude, $facility->longitude, $normalizedPoints, 0.0035)) { // ~350m
                    if ($facility->type === 'cctv') {
                        $cctvCount++;
                    } elseif ($facility->type === 'street_light') {
                        $lightCount++;
                    } elseif ($facility->type === 'security_post') {
                        $postCount++;
                    }
                }
            }

            // Hitung titik rawan & insiden
            $dangerCount = 0;
            foreach ($dangerZones as $dz) {
                if ($this->isNearRoute($dz->latitude, $dz->longitude, $normalizedPoints, 0.004)) {
                    $dangerCount++;
                }
            }

            $incidentCount = 0;
            foreach ($recentReports as $report) {
                if ($report->latitude && $report->longitude && $this->isNearRoute((float) $report->latitude, (float) $report->longitude, $normalizedPoints, 0.0035)) {
                    $incidentCount++;
                }
            }

            // Skor sub-faktor (0 - 100)
            $cctvScore = min(100, max(50, 65 + ($cctvCount * 9)));
            $lightScore = min(100, max(50, 65 + ($lightCount * 8)));
            $postScore = min(100, max(50, 65 + ($postCount * 12)));
            $incidentScore = max(20, min(100, 100 - ($dangerCount * 18) - ($incidentCount * 12)));

            // Bobot skor total
            $overallScore = round(
                ($cctvScore * 0.25) +
                ($lightScore * 0.25) +
                ($postScore * 0.25) +
                ($incidentScore * 0.25)
            );

            // Tingkat keamanan
            if ($overallScore >= 80) {
                $level = 'aman';
            } elseif ($overallScore >= 60) {
                $level = 'sedang';
            } else {
                $level = 'rawan';
            }

            $results[] = [
                'id' => $routeId,
                'score' => $overallScore,
                'level' => $level,
                'factors' => [
                    [
                        'key' => 'cctv',
                        'label' => 'Cakupan CCTV',
                        'score' => $cctvScore,
                        'available' => true,
                        'note' => $cctvCount > 0 ? "Terpantau {$cctvCount} titik kamera pengawas aktif" : 'Minim pantauan CCTV di rute ini',
                    ],
                    [
                        'key' => 'lighting',
                        'label' => 'Penerangan Jalan',
                        'score' => $lightScore,
                        'available' => true,
                        'note' => $lightCount > 0 ? "Tersedia {$lightCount} titik penerangan jalan terpantau" : 'Penerangan perlu diwaspadai di malam hari',
                    ],
                    [
                        'key' => 'security_post',
                        'label' => 'Kedekatan Pos Aman',
                        'score' => $postScore,
                        'available' => true,
                        'note' => $postCount > 0 ? "Terdapat {$postCount} pos jaga / polsek di sekitar rute" : 'Pos penjagaan terdekat cukup jauh',
                    ],
                    [
                        'key' => 'incidents',
                        'label' => 'Laporan Kejadian',
                        'score' => $incidentScore,
                        'available' => true,
                        'note' => ($dangerCount + $incidentCount) === 0 ? 'Rute bersih dari laporan insiden baru-baru ini' : "Ada {$dangerCount} titik rawan dan {$incidentCount} insiden terpantau",
                    ],
                ],
            ];
        }

        return $results;
    }

    /**
     * Menormalkan format titik koordinat [lat, lng].
     *
     * @param  array<int, array<int, float>>  $coordinates
     * @return array<int, array{lat: float, lng: float}>
     */
    private function normalizeCoordinates(array $coordinates): array
    {
        $points = [];

        foreach ($coordinates as $point) {
            if (! is_array($point) || count($point) < 2) {
                continue;
            }

            $a = (float) $point[0];
            $b = (float) $point[1];

            // Jika GeoJSON format [lng, lat]
            if (abs($a) > 90 || (abs($b) <= 90 && abs($a) <= 180 && $a > 50)) {
                $lat = $b;
                $lng = $a;
            } else {
                $lat = $a;
                $lng = $b;
            }

            $points[] = ['lat' => $lat, 'lng' => $lng];
        }

        return $points;
    }

    /**
     * Memeriksa apakah suatu koordinat berada di dekat jalur rute (pendekatan bounding distance).
     *
     * @param  array<int, array{lat: float, lng: float}>  $routePoints
     */
    private function isNearRoute(float $targetLat, float $targetLng, array $routePoints, float $thresholdDeg): bool
    {
        foreach ($routePoints as $point) {
            $dLat = abs($point['lat'] - $targetLat);
            $dLng = abs($point['lng'] - $targetLng);

            if (($dLat * $dLat + $dLng * $dLng) <= ($thresholdDeg * $thresholdDeg)) {
                return true;
            }
        }

        return false;
    }
}
