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

        $womenKeywords = ['pelecehan', 'catcalling', 'intimidasi', 'kekerasan', 'asusila', 'begal', 'gangguan'];

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
                    'womens_zone' => [
                        'score' => null,
                        'level' => 'data_belum_cukup',
                        'is_data_sufficient' => false,
                        'status_label' => 'Data keamanan belum mencukupi',
                        'badge_label' => 'Data keamanan belum mencukupi',
                        'is_pink_recommendation' => false,
                        'route_color' => '#94a3b8',
                        'route_color_name' => 'abu-abu',
                        'risk_breakdown' => [
                            'harassment_incidents' => 0,
                            'general_incidents' => 0,
                            'danger_zones' => 0,
                            'street_lights' => 0,
                            'cctv_active' => 0,
                            'security_posts' => 0,
                        ],
                    ],
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
            $womenDangerCount = 0;
            foreach ($dangerZones as $dz) {
                if ($this->isNearRoute($dz->latitude, $dz->longitude, $normalizedPoints, 0.004)) {
                    $dangerCount++;
                    $dzText = strtolower($dz->name.' '.$dz->category.' '.($dz->description ?? ''));
                    foreach ($womenKeywords as $kw) {
                        if (str_contains($dzText, $kw)) {
                            $womenDangerCount++;
                            break;
                        }
                    }
                    if ($dz->danger_level === 'tinggi') {
                        $womenDangerCount++;
                    }
                }
            }

            $incidentCount = 0;
            $womenIncidentCount = 0;
            $generalIncidentCount = 0;
            foreach ($recentReports as $report) {
                if ($report->latitude && $report->longitude && $this->isNearRoute((float) $report->latitude, (float) $report->longitude, $normalizedPoints, 0.0035)) {
                    $incidentCount++;
                    $repText = strtolower($report->category.' '.$report->description);
                    $isWomenReport = false;
                    foreach ($womenKeywords as $kw) {
                        if (str_contains($repText, $kw)) {
                            $isWomenReport = true;
                            break;
                        }
                    }
                    if ($isWomenReport) {
                        $womenIncidentCount++;
                    } else {
                        $generalIncidentCount++;
                    }
                }
            }

            // Skor umum sub-faktor (0 - 100)
            $cctvScore = min(100, max(50, 65 + ($cctvCount * 9)));
            $lightScore = min(100, max(50, 65 + ($lightCount * 8)));
            $postScore = min(100, max(50, 65 + ($postCount * 12)));
            $incidentScore = max(20, min(100, 100 - ($dangerCount * 18) - ($incidentCount * 12)));

            // Bobot skor total umum
            $overallScore = round(
                ($cctvScore * 0.25) +
                ($lightScore * 0.25) +
                ($postScore * 0.25) +
                ($incidentScore * 0.25)
            );

            if ($overallScore >= 80) {
                $level = 'aman';
            } elseif ($overallScore >= 60) {
                $level = 'sedang';
            } else {
                $level = 'rawan';
            }

            // ===== PERHITUNGAN WOMEN'S SAFETY SCORE =====
            // Cek kecukupan data telemetri di jalur rute
            $isDataSufficient = ($cctvCount + $lightCount + $postCount + $dangerCount + $incidentCount) > 0;

            if (! $isDataSufficient) {
                $womensScore = null;
                $womensLevel = 'data_belum_cukup';
                $womensStatusLabel = 'Data keamanan belum mencukupi';
            } else {
                // Bobot khusus Women's Zone:
                // Prioritas tinggi pada penerangan jalan & penalti keras untuk catcalling/pelecehan/intimidasi
                $wLightBonus = min(22, $lightCount * 7);
                $wCctvBonus = min(15, $cctvCount * 5);
                $wPostBonus = min(15, $postCount * 8);

                $wHarassPenalty = $womenIncidentCount * 26; // Penalti berat untuk pelecehan/catcalling
                $wDangerPenalty = $womenDangerCount * 18;
                $wOtherPenalty = ($generalIncidentCount * 8) + (max(0, $dangerCount - $womenDangerCount) * 10);

                $wRaw = 62 + $wLightBonus + $wCctvBonus + $wPostBonus - $wHarassPenalty - $wDangerPenalty - $wOtherPenalty;
                $womensScore = (int) round(max(15, min(100, $wRaw)));

                if ($womensScore >= 80) {
                    $womensLevel = 'aman';
                    $womensStatusLabel = 'Sangat Aman untuk Perempuan';
                } elseif ($womensScore >= 60) {
                    $womensLevel = 'sedang';
                    $womensStatusLabel = 'Tingkat Keamanan Sedang';
                } else {
                    $womensLevel = 'rawan';
                    $womensStatusLabel = 'Risiko Tinggi / Minim Pengawasan';
                }
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
                'womens_zone' => [
                    'score' => $womensScore,
                    'level' => $womensLevel,
                    'is_data_sufficient' => $isDataSufficient,
                    'status_label' => $womensStatusLabel,
                    'badge_label' => $womensStatusLabel,
                    'is_pink_recommendation' => false,
                    'route_color' => '#2563eb',
                    'route_color_name' => 'biru',
                    'risk_breakdown' => [
                        'harassment_incidents' => $womenIncidentCount,
                        'general_incidents' => $generalIncidentCount,
                        'danger_zones' => $dangerCount,
                        'street_lights' => $lightCount,
                        'cctv_active' => $cctvCount,
                        'security_posts' => $postCount,
                    ],
                ],
            ];
        }

        // ===== SELEKSI WARNA & REKOMENDASI WOMEN'S ZONE =====
        // Rute dengan Women's Safety Score tertinggi diberi warna PINK & label "Paling Aman untuk Perempuan"
        $validRanks = [];
        foreach ($results as $idx => $item) {
            if ($item['womens_zone']['is_data_sufficient'] && $item['womens_zone']['score'] !== null) {
                $validRanks[] = [
                    'index' => $idx,
                    'score' => $item['womens_zone']['score'],
                ];
            }
        }

        // Urutkan murni berdasarkan Women's Safety Score tertinggi (bukan jarak/waktu)
        usort($validRanks, fn ($a, $b) => $b['score'] <=> $a['score']);

        foreach ($results as $idx => &$item) {
            if (! $item['womens_zone']['is_data_sufficient'] || $item['womens_zone']['score'] === null) {
                $item['womens_zone']['is_pink_recommendation'] = false;
                $item['womens_zone']['route_color'] = '#94a3b8';
                $item['womens_zone']['route_color_name'] = 'abu-abu';
                $item['womens_zone']['badge_label'] = 'Data keamanan belum mencukupi';
                continue;
            }

            $rankPosition = -1;
            foreach ($validRanks as $rPos => $vr) {
                if ($vr['index'] === $idx) {
                    $rankPosition = $rPos;
                    break;
                }
            }

            // Peringkat 1 -> PINK ("Paling Aman untuk Perempuan")
            if ($rankPosition === 0 && $item['womens_zone']['score'] >= 60) {
                $item['womens_zone']['is_pink_recommendation'] = true;
                $item['womens_zone']['route_color'] = '#ec4899'; // PINK
                $item['womens_zone']['route_color_name'] = 'pink';
                $item['womens_zone']['badge_label'] = 'Paling Aman untuk Perempuan';
            } elseif ($rankPosition === 1 || ($rankPosition === 0 && $item['womens_zone']['score'] < 60)) {
                // Alternatif kedua -> BIRU
                $item['womens_zone']['is_pink_recommendation'] = false;
                $item['womens_zone']['route_color'] = '#2563eb'; // BIRU
                $item['womens_zone']['route_color_name'] = 'biru';
                $item['womens_zone']['badge_label'] = 'Alternatif Cukup Aman';
            } else {
                // Alternatif ketiga / risiko lebih tinggi -> ORANYE
                $item['womens_zone']['is_pink_recommendation'] = false;
                $item['womens_zone']['route_color'] = '#f97316'; // ORANYE
                $item['womens_zone']['route_color_name'] = 'oranye';
                $item['womens_zone']['badge_label'] = 'Jalur Berisiko / Kurang Penerangan';
            }
        }
        unset($item);

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
