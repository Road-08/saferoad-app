<?php

namespace App\Services;

use App\Enums\InspectionStatus;
use App\Models\DangerZone;
use App\Models\EnvironmentalCondition;
use App\Models\SafetyFacility;
use App\Models\SecurityReport;

class RouteSafetyService
{
    /**
     * Menghitung skor keselamatan rute umum dan rute Women's Zone dari koordinat rute.
     *
     * Pembobotan Sistem Skor:
     * - Safety Score Umum:
     *   1. Laporan Kriminalitas Umum : 50%
     *   2. Penerangan Jalan          : 20%
     *   3. Fasilitas Keamanan        : 15%
     *   4. Aktivitas Lingkungan      : 15%
     *
     * - Women's Safety Score:
     *   1. Laporan Risiko Perempuan  : 50%
     *   2. Penerangan Jalan          : 20%
     *   3. Fasilitas Keamanan        : 15%
     *   4. Aktivitas Lingkungan      : 15%
     *
     * @param  array<int, array{id: string, coordinates: array<int, array<int, float>>}>  $routes
     * @param  string  $mode  'general' | 'women' | 'all'
     * @return array<int, array<string, mixed>>
     */
    public function calculateScores(array $routes, string $mode = 'all'): array
    {
        $allFacilities = SafetyFacility::all();
        $dangerZones = DangerZone::active()->get();
        // Laporan yang ditolak (rejected) tidak dihitung sama sekali
        $reports = SecurityReport::where('status', '!=', 'rejected')
            ->where('validation_status', '!=', 'rejected')
            ->get();
        $envConditions = EnvironmentalCondition::verified()->get();

        $results = [];

        foreach ($routes as $route) {
            $routeId = (string) ($route['id'] ?? 'unknown');
            $coordinates = $route['coordinates'] ?? [];
            $normalizedPoints = $this->normalizeCoordinates($coordinates);

            if (empty($normalizedPoints)) {
                $results[] = $this->buildEmptyScoreResult($routeId);
                continue;
            }

            // 1. FASILITAS KEAMANAN (Tanpa hitung ganda & perhatikan kondisi fisik)
            $countedFacilityIds = [];
            $cctvActiveCount = 0;
            $lightActiveCount = 0;
            $lightDamagedCount = 0;
            $postActiveCount = 0;

            foreach ($allFacilities as $facility) {
                if (in_array($facility->id, $countedFacilityIds, true)) {
                    continue;
                }

                $distKm = $this->findDistanceToRoute((float) $facility->latitude, (float) $facility->longitude, $normalizedPoints);
                if ($distKm <= 0.35) { // Radius 350 meter dari jalur
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

            // 2. KONDISI LINGKUNGAN (Aktivitas & Penerangan Observasi)
            $countedEnvIds = [];
            $activityScores = [];
            $envLightingScores = [];

            foreach ($envConditions as $env) {
                if (in_array($env->id, $countedEnvIds, true)) {
                    continue;
                }

                $distKm = $this->findDistanceToRoute((float) $env->latitude, (float) $env->longitude, $normalizedPoints);
                if ($distKm <= 0.40) {
                    $countedEnvIds[] = $env->id;

                    // Skor Aktivitas Lingkungan (ramai=95, sedang=70, sepi=30)
                    if ($env->activity_level === 'ramai') {
                        $activityScores[] = 95;
                    } elseif ($env->activity_level === 'sedang') {
                        $activityScores[] = 70;
                    } else {
                        $activityScores[] = 30;
                    }

                    // Observasi Pencahayaan Lingkungan
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
            $countedDangerIds = [];
            $dangerCount = 0;
            $highDangerCount = 0;
            $womenDangerCount = 0;

            foreach ($dangerZones as $dz) {
                if (in_array($dz->id, $countedDangerIds, true)) {
                    continue;
                }

                $distKm = $this->findDistanceToRoute((float) $dz->latitude, (float) $dz->longitude, $normalizedPoints);
                if ($distKm <= 0.40) {
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
                    if ($dz->danger_level === 'tinggi') {
                        $highDangerCount++;
                    }
                }
            }

            // 4. LAPORAN KEJADIAN MASYARAKAT (Dipisahkan Laporan Umum vs Khusus Perempuan)
            $countedReportIds = [];

            // Metrik Umum
            $theftCount = 0;
            $robberyCount = 0;
            $generalOtherCrimeCount = 0;
            $generalVerifiedCount = 0;
            $generalPendingCount = 0;
            $generalWeightedPenalty = 0.0;

            // Metrik Khusus Perempuan
            $catcallingCount = 0;
            $sexualHarassmentCount = 0;
            $intimidationViolenceCount = 0;
            $womenVerifiedCount = 0;
            $womenPendingCount = 0;
            $womenWeightedPenalty = 0.0;

            $locationClusters = [];

            foreach ($reports as $report) {
                if (in_array($report->id, $countedReportIds, true)) {
                    continue; // Tanpa hitung ganda ID laporan
                }

                if (! $report->latitude || ! $report->longitude) {
                    continue;
                }

                $distKm = $this->findDistanceToRoute((float) $report->latitude, (float) $report->longitude, $normalizedPoints);
                if ($distKm <= 0.38) {
                    $countedReportIds[] = $report->id;

                    // Evaluasi Status Pemeriksaan Dinamis
                    $inspectionStatus = InspectionStatus::normalize($report->inspection_status);
                    if ($report->inspection_status === null) {
                        if ($report->status === 'handled') {
                            $inspectionStatus = InspectionStatus::HANDLED;
                        } elseif ($report->validation_status === 'verified' || in_array($report->status, ['officer_check', 'map_updated'], true)) {
                            $inspectionStatus = InspectionStatus::VERIFIED_TRUE;
                        } elseif ($report->status === 'rejected' || $report->validation_status === 'rejected') {
                            $inspectionStatus = InspectionStatus::UNPROVEN;
                        } else {
                            $inspectionStatus = InspectionStatus::WAITING_VERIFICATION;
                        }
                    }

                    $statusRiskWeight = $inspectionStatus->riskWeight();

                    // Jika laporan dinyatakan tidak terbukti atau kondisi aman, tidak ada penalti
                    if ($statusRiskWeight <= 0.0) {
                        continue;
                    }

                    $isValidated = ($inspectionStatus === InspectionStatus::VERIFIED_TRUE);

                    // Bobot Jarak Laporan dari Jalur (semakin dekat semakin besar dampaknya)
                    $distanceWeight = $distKm <= 0.15 ? 1.0 : ($distKm <= 0.25 ? 0.85 : 0.70);

                    // Bobot Usia Laporan (Recency)
                    $reportDate = $report->incident_at ?: $report->created_at;
                    $daysAgo = $reportDate ? $reportDate->diffInDays(now()) : 0;
                    $recencyWeight = ($daysAgo <= 7) ? 1.0 : (($daysAgo <= 30) ? 0.8 : 0.6);

                    // Bobot Tingkat Keparahan Laporan
                    $sev = strtolower((string) ($report->severity ?? 'sedang'));
                    $severityMultiplier = ($sev === 'tinggi') ? 1.5 : (($sev === 'rendah') ? 0.6 : 1.0);

                    $repText = strtolower($report->category.' '.$report->description);

                    // Deteksi Jenis Laporan Khusus Perempuan
                    $isCatcalling = str_contains($repText, 'catcalling') || str_contains($repText, 'suit') || str_contains($repText, 'siulan');
                    $isHarassment = str_contains($repText, 'pelecehan') || str_contains($repText, 'asusila') || str_contains($repText, 'cabul') || str_contains($repText, 'raba');
                    $isIntimidation = str_contains($repText, 'intimidasi') || str_contains($repText, 'kekerasan') || str_contains($repText, 'ancam') || str_contains($repText, 'aniaya');
                    $isWomenIncident = $isCatcalling || $isHarassment || $isIntimidation;

                    // Deteksi Jenis Laporan Umum
                    $isTheft = str_contains($repText, 'curi') || str_contains($repText, 'pencurian') || str_contains($repText, 'maling') || str_contains($repText, 'copet');
                    $isRobbery = str_contains($repText, 'rampok') || str_contains($repText, 'perampokan') || str_contains($repText, 'begal') || str_contains($repText, 'penodongan');

                    // Anti Double Counting untuk klaster koordinat yang persis sama
                    $locKey = round((float) $report->latitude, 4).','.round((float) $report->longitude, 4);
                    $clusterRepeatFactor = 1.0;
                    if (isset($locationClusters[$locKey])) {
                        $locationClusters[$locKey]++;
                        $clusterRepeatFactor = 0.30; // Laporan berikutnya di titik yang sama teredam
                    } else {
                        $locationClusters[$locKey] = 1;
                    }

                    if ($isWomenIncident) {
                        if ($isCatcalling) {
                            $catcallingCount++;
                        }
                        if ($isHarassment) {
                            $sexualHarassmentCount++;
                        }
                        if ($isIntimidation) {
                            $intimidationViolenceCount++;
                        }

                        if ($isValidated) {
                            $womenVerifiedCount++;
                        } else {
                            $womenPendingCount++;
                        }

                        $typeFactor = $isHarassment ? 28 : ($isIntimidation ? 24 : 18);
                        $womenWeightedPenalty += ($typeFactor * $severityMultiplier * $recencyWeight * $distanceWeight * $statusRiskWeight * $clusterRepeatFactor);
                    } else {
                        // Kriminal Umum
                        if ($isTheft) {
                            $theftCount++;
                        } elseif ($isRobbery) {
                            $robberyCount++;
                        } else {
                            $generalOtherCrimeCount++;
                        }

                        if ($isValidated) {
                            $generalVerifiedCount++;
                        } else {
                            $generalPendingCount++;
                        }

                        $typeFactor = $isRobbery ? 26 : ($isTheft ? 18 : 14);
                        $generalWeightedPenalty += ($typeFactor * $severityMultiplier * $recencyWeight * $distanceWeight * $statusRiskWeight * $clusterRepeatFactor);
                    }
                }
            }

            // ==========================================
            // KALKULASI 4 KOMPONEN TERPISAH (0 - 100)
            // ==========================================

            // 1. Komponen Penerangan Jalan (Bobot 20%)
            $lightingCondition = ($lightActiveCount >= 3)
                ? 'Terang (Optimal)'
                : (($lightActiveCount >= 1) ? 'Penerangan Cukup' : 'Minim Penerangan / Gelap');

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

            // 2. Komponen Fasilitas Keamanan (Bobot 15%)
            $facilityPoints = ($cctvActiveCount * 40) + ($postActiveCount * 45);
            $componentFacility = min(100, max(20, 20 + $facilityPoints));

            // 3. Komponen Aktivitas Lingkungan (Bobot 15%)
            if (! empty($activityScores)) {
                $componentActivity = (int) round(array_sum($activityScores) / count($activityScores));
            } else {
                $componentActivity = 60; // Nilai netral jika belum ada titik observasi
            }

            // 4. Komponen Kriminalitas Umum (Bobot 50%)
            $componentGeneralCrime = max(0, min(100, (int) round(100 - $generalWeightedPenalty - ($dangerCount * 15))));

            // 4. Komponen Risiko Terhadap Perempuan (Bobot 50%)
            $componentWomenRisk = max(0, min(100, (int) round(100 - $womenWeightedPenalty - ($womenDangerCount * 18))));

            // ==========================================
            // EVALUASI KECUKUPAN DATA
            // ==========================================
            $totalTelemetryPoints = count($countedFacilityIds) + count($countedDangerIds) + count($countedReportIds) + count($countedEnvIds);
            $isDataSufficientGeneral = $totalTelemetryPoints >= 1;
            $isDataSufficientWomen = $totalTelemetryPoints >= 1;

            // ==========================================
            // HASIL SCORE UMUM: 50% + 20% + 15% + 15%
            // ==========================================
            if (! $isDataSufficientGeneral) {
                $generalScore = null;
                $generalRiskLevel = 'Data keamanan belum mencukupi';
            } else {
                $rawGeneral = ($componentGeneralCrime * 0.50) +
                    ($componentLighting * 0.20) +
                    ($componentFacility * 0.15) +
                    ($componentActivity * 0.15);

                $generalScore = (int) round(max(10, min(100, $rawGeneral)));
                if ($generalScore >= 80) {
                    $generalRiskLevel = 'Rendah (Aman)';
                } elseif ($generalScore >= 60) {
                    $generalRiskLevel = 'Sedang';
                } else {
                    $generalRiskLevel = 'Tinggi (Rawan)';
                }
            }

            // ==========================================
            // HASIL WOMEN'S SCORE: 50% + 20% + 15% + 15%
            // ==========================================
            if (! $isDataSufficientWomen) {
                $womensScore = null;
                $womensRiskLevel = 'Data keamanan belum mencukupi';
            } else {
                $rawWomen = ($componentWomenRisk * 0.50) +
                    ($componentLighting * 0.20) +
                    ($componentFacility * 0.15) +
                    ($componentActivity * 0.15);

                $womensScore = (int) round(max(10, min(100, $rawWomen)));
                if ($womensScore >= 80) {
                    $womensRiskLevel = 'Rendah (Paling Aman)';
                } elseif ($womensScore >= 60) {
                    $womensRiskLevel = 'Sedang';
                } else {
                    $womensRiskLevel = 'Tinggi (Rawan)';
                }
            }

            $totalCrimes = $theftCount + $robberyCount + $generalOtherCrimeCount;
            $totalWomenIncidents = $catcallingCount + $sexualHarassmentCount + $intimidationViolenceCount;

            $generalFactors = [
                [
                    'key' => 'general_crimes',
                    'label' => 'Kriminalitas Umum (Bobot 50%)',
                    'score' => $componentGeneralCrime,
                    'available' => true,
                    'note' => "{$totalCrimes} insiden ({$generalVerifiedCount} terverifikasi, {$generalPendingCount} menunggu validasi)",
                ],
                [
                    'key' => 'lighting',
                    'label' => 'Penerangan Jalan (Bobot 20%)',
                    'score' => $componentLighting,
                    'available' => true,
                    'note' => "{$lightActiveCount} lampu aktif, {$lightDamagedCount} rusak ({$lightingCondition})",
                ],
                [
                    'key' => 'security_facilities',
                    'label' => 'Fasilitas Keamanan (Bobot 15%)',
                    'score' => $componentFacility,
                    'available' => true,
                    'note' => "{$cctvActiveCount} CCTV & {$postActiveCount} pos keamanan aktif",
                ],
                [
                    'key' => 'environmental_activity',
                    'label' => 'Aktivitas Lingkungan (Bobot 15%)',
                    'score' => $componentActivity,
                    'available' => true,
                    'note' => ! empty($activityScores) ? 'Data observasi keramaian koridor tersedia' : 'Aktivitas rata-rata lingkungan',
                ],
            ];

            $results[] = [
                'id' => $routeId,
                'duration' => (float) ($route['duration'] ?? ($route['duration_s'] ?? 0)),
                'score' => $generalScore,
                'level' => ($generalScore >= 80) ? 'aman' : (($generalScore >= 60) ? 'sedang' : (($generalScore === null) ? 'data_belum_cukup' : 'rawan')),
                'factors' => $generalFactors,

                // Metrik Khusus Perbandingan Rute Umum
                'general_comparison' => [
                    'safety_score' => $generalScore,
                    'risk_level' => $generalRiskLevel,
                    'is_data_sufficient' => $isDataSufficientGeneral,
                    'badge_label' => 'Alternatif Rute',
                    'route_color' => '#2563eb',
                    'route_color_name' => 'biru',
                    'is_recommended' => false,
                    'component_breakdown' => [
                        'crime_score_50' => $componentGeneralCrime,
                        'lighting_score_20' => $componentLighting,
                        'facility_score_15' => $componentFacility,
                        'activity_score_15' => $componentActivity,
                    ],
                    'reports' => [
                        'theft_count' => $theftCount,
                        'robbery_count' => $robberyCount,
                        'other_crimes_count' => $generalOtherCrimeCount,
                        'total_crimes_count' => $totalCrimes,
                        'verified_count' => $generalVerifiedCount,
                        'unverified_count' => $generalPendingCount,
                    ],
                    'facilities' => [
                        'street_lights_count' => $lightActiveCount,
                        'street_lights_damaged' => $lightDamagedCount,
                        'lighting_condition' => $lightingCondition,
                        'cctv_count' => $cctvActiveCount,
                        'security_post_count' => $postActiveCount,
                    ],
                ],

                // Metrik Khusus Perbandingan Rute Perempuan (Women Zone)
                'women_comparison' => [
                    'womens_safety_score' => $womensScore,
                    'risk_level' => $womensRiskLevel,
                    'is_data_sufficient' => $isDataSufficientWomen,
                    'badge_label' => 'Alternatif Perempuan',
                    'route_color' => '#2563eb',
                    'route_color_name' => 'biru',
                    'is_pink_recommendation' => false,
                    'component_breakdown' => [
                        'women_risk_score_50' => $componentWomenRisk,
                        'lighting_score_20' => $componentLighting,
                        'facility_score_15' => $componentFacility,
                        'activity_score_15' => $componentActivity,
                    ],
                    'reports' => [
                        'catcalling_count' => $catcallingCount,
                        'sexual_harassment_count' => $sexualHarassmentCount,
                        'intimidation_violence_count' => $intimidationViolenceCount,
                        'total_women_incidents' => $totalWomenIncidents,
                        'verified_count' => $womenVerifiedCount,
                        'unverified_count' => $womenPendingCount,
                    ],
                    'facilities' => [
                        'street_lights_count' => $lightActiveCount,
                        'lighting_condition' => $lightingCondition,
                        'cctv_count' => $cctvActiveCount,
                        'shelter_post_count' => $postActiveCount,
                    ],
                ],

                // Backward-compatible womens_zone field
                'womens_zone' => [
                    'score' => $womensScore,
                    'level' => ($womensScore >= 80) ? 'aman' : (($womensScore >= 60) ? 'sedang' : (($womensScore === null) ? 'data_belum_cukup' : 'rawan')),
                    'is_data_sufficient' => $isDataSufficientWomen,
                    'status_label' => $womensRiskLevel,
                    'badge_label' => 'Alternatif Perempuan',
                    'is_pink_recommendation' => false,
                    'route_color' => '#2563eb',
                    'route_color_name' => 'biru',
                    'risk_breakdown' => [
                        'harassment_incidents' => $totalWomenIncidents,
                        'general_incidents' => $totalCrimes,
                        'danger_zones' => $dangerCount,
                        'street_lights' => $lightActiveCount,
                        'cctv_active' => $cctvActiveCount,
                        'security_posts' => $postActiveCount,
                    ],
                ],
            ];
        }

        // SELEKSI & RANKING RUTE UMUM
        $this->rankGeneralRoutes($results);

        // SELEKSI & RANKING RUTE PEREMPUAN (PINK)
        $this->rankWomenRoutes($results);

        return $results;
    }

    private function rankGeneralRoutes(array &$results): void
    {
        $validRanks = [];
        foreach ($results as $idx => $r) {
            if ($r['general_comparison']['is_data_sufficient'] && $r['general_comparison']['safety_score'] !== null) {
                $validRanks[] = [
                    'index' => $idx,
                    'score' => $r['general_comparison']['safety_score'],
                ];
            }
        }

        usort($validRanks, fn ($a, $b) => $b['score'] <=> $a['score']);

        foreach ($results as $idx => &$r) {
            if (! $r['general_comparison']['is_data_sufficient'] || $r['general_comparison']['safety_score'] === null) {
                $r['general_comparison']['badge_label'] = 'Data keamanan belum mencukupi';
                $r['general_comparison']['route_color'] = '#94a3b8';
                $r['general_comparison']['route_color_name'] = 'abu-abu';
                continue;
            }

            $pos = -1;
            foreach ($validRanks as $p => $vr) {
                if ($vr['index'] === $idx) {
                    $pos = $p;
                    break;
                }
            }

            if ($pos === 0) {
                $r['general_comparison']['is_recommended'] = true;
                $r['general_comparison']['badge_label'] = 'Rute Paling Aman';
                $r['general_comparison']['route_color'] = '#16a34a'; // HIJAU
                $r['general_comparison']['route_color_name'] = 'hijau';
            } elseif ($pos === 1) {
                $r['general_comparison']['is_recommended'] = false;
                $r['general_comparison']['badge_label'] = 'Alternatif Cukup Aman';
                $r['general_comparison']['route_color'] = '#2563eb'; // BIRU
                $r['general_comparison']['route_color_name'] = 'biru';
            } else {
                $r['general_comparison']['is_recommended'] = false;
                $r['general_comparison']['badge_label'] = 'Alternatif Berisiko';
                $r['general_comparison']['route_color'] = '#d97706'; // ORANYE
                $r['general_comparison']['route_color_name'] = 'oranye';
            }
        }
        unset($r);
    }

    private function rankWomenRoutes(array &$results): void
    {
        $validRanks = [];
        foreach ($results as $idx => $r) {
            if ($r['women_comparison']['is_data_sufficient'] && $r['women_comparison']['womens_safety_score'] !== null) {
                $validRanks[] = [
                    'index' => $idx,
                    'score' => $r['women_comparison']['womens_safety_score'],
                    'duration' => (float) ($r['duration'] ?? 0),
                ];
            }
        }

        // Tepat satu rute teraman: skor perempuan tertinggi, jika seri durasi terpendek
        usort($validRanks, function ($a, $b) {
            if ($b['score'] !== $a['score']) {
                return $b['score'] <=> $a['score'];
            }
            if ($a['duration'] > 0 && $b['duration'] > 0) {
                return $a['duration'] <=> $b['duration'];
            }
            return 0;
        });

        foreach ($results as $idx => &$r) {
            if (! $r['women_comparison']['is_data_sufficient'] || $r['women_comparison']['womens_safety_score'] === null) {
                $r['women_comparison']['is_pink_recommendation'] = false;
                $r['women_comparison']['route_color'] = '#94a3b8';
                $r['women_comparison']['route_color_name'] = 'abu-abu';
                $r['women_comparison']['badge_label'] = 'Data keamanan belum mencukupi';

                $r['womens_zone']['is_pink_recommendation'] = false;
                $r['womens_zone']['route_color'] = '#94a3b8';
                $r['womens_zone']['route_color_name'] = 'abu-abu';
                $r['womens_zone']['badge_label'] = 'Data keamanan belum mencukupi';
                continue;
            }

            $pos = -1;
            foreach ($validRanks as $p => $vr) {
                if ($vr['index'] === $idx) {
                    $pos = $p;
                    break;
                }
            }

            if ($pos === 0) {
                $r['women_comparison']['is_pink_recommendation'] = true;
                $r['women_comparison']['route_color'] = '#ec4899'; // PINK
                $r['women_comparison']['route_color_name'] = 'pink';
                $r['women_comparison']['badge_label'] = 'Paling Aman untuk Perempuan';

                $r['womens_zone']['is_pink_recommendation'] = true;
                $r['womens_zone']['route_color'] = '#ec4899';
                $r['womens_zone']['route_color_name'] = 'pink';
                $r['womens_zone']['badge_label'] = 'Paling Aman untuk Perempuan';
            } elseif ($pos === 1) {
                $r['women_comparison']['is_pink_recommendation'] = false;
                $r['women_comparison']['route_color'] = '#2563eb'; // BIRU
                $r['women_comparison']['route_color_name'] = 'biru';
                $r['women_comparison']['badge_label'] = 'Alternatif Cukup Aman';

                $r['womens_zone']['is_pink_recommendation'] = false;
                $r['womens_zone']['route_color'] = '#2563eb';
                $r['womens_zone']['route_color_name'] = 'biru';
                $r['womens_zone']['badge_label'] = 'Alternatif Cukup Aman';
            } else {
                $r['women_comparison']['is_pink_recommendation'] = false;
                $r['women_comparison']['route_color'] = '#f97316'; // ORANYE
                $r['women_comparison']['route_color_name'] = 'oranye';
                $r['women_comparison']['badge_label'] = 'Jalur Berisiko / Kurang Penerangan';

                $r['womens_zone']['is_pink_recommendation'] = false;
                $r['womens_zone']['route_color'] = '#f97316';
                $r['womens_zone']['route_color_name'] = 'oranye';
                $r['womens_zone']['badge_label'] = 'Jalur Berisiko / Kurang Penerangan';
            }
        }
        unset($r);
    }

    private function buildEmptyScoreResult(string $routeId): array
    {
        return [
            'id' => $routeId,
            'score' => null,
            'level' => 'data_belum_cukup',
            'factors' => [],
            'general_comparison' => [
                'safety_score' => null,
                'risk_level' => 'Data keamanan belum mencukupi',
                'is_data_sufficient' => false,
                'badge_label' => 'Data keamanan belum mencukupi',
                'route_color' => '#94a3b8',
                'route_color_name' => 'abu-abu',
                'is_recommended' => false,
                'reports' => [
                    'theft_count' => 0,
                    'robbery_count' => 0,
                    'other_crimes_count' => 0,
                    'total_crimes_count' => 0,
                    'verified_count' => 0,
                    'unverified_count' => 0,
                ],
                'facilities' => [
                    'street_lights_count' => 0,
                    'street_lights_damaged' => 0,
                    'lighting_condition' => 'Tidak Ada Data',
                    'cctv_count' => 0,
                    'security_post_count' => 0,
                ],
            ],
            'women_comparison' => [
                'womens_safety_score' => null,
                'risk_level' => 'Data keamanan belum mencukupi',
                'is_data_sufficient' => false,
                'badge_label' => 'Data keamanan belum mencukupi',
                'route_color' => '#94a3b8',
                'route_color_name' => 'abu-abu',
                'is_pink_recommendation' => false,
                'reports' => [
                    'catcalling_count' => 0,
                    'sexual_harassment_count' => 0,
                    'intimidation_violence_count' => 0,
                    'total_women_incidents' => 0,
                    'verified_count' => 0,
                    'unverified_count' => 0,
                ],
                'facilities' => [
                    'street_lights_count' => 0,
                    'lighting_condition' => 'Tidak Ada Data',
                    'cctv_count' => 0,
                    'shelter_post_count' => 0,
                ],
            ],
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
     * Menghitung jarak minimum titik ke jalur dalam kilometer menggunakan rumus Haversine.
     *
     * @param  array<int, array{lat: float, lng: float}>  $routePoints
     */
    private function findDistanceToRoute(float $targetLat, float $targetLng, array $routePoints): float
    {
        $minKm = 9999.0;
        foreach ($routePoints as $pt) {
            $km = $this->haversineKm($targetLat, $targetLng, $pt['lat'], $pt['lng']);
            if ($km < $minKm) {
                $minKm = $km;
            }
        }

        return $minKm;
    }

    /**
     * Menghitung jarak haversine antara dua koordinat lat/lng (km).
     */
    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
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
