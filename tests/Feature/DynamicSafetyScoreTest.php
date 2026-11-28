<?php

namespace Tests\Feature;

use App\Enums\InspectionStatus;
use App\Enums\ReportStatus;
use App\Models\Rute;
use App\Models\SafetyFacility;
use App\Models\SecurityReport;
use App\Models\User;
use App\Services\RouteSafetyService;
use App\Services\SafetyScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DynamicSafetyScoreTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $keamanan;

    private User $masyarakat;

    private User $perempuan;

    private SafetyScoreService $scoreService;

    private RouteSafetyService $routeService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->scoreService = app(SafetyScoreService::class);
        $this->routeService = app(RouteSafetyService::class);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@saferoad.test',
        ]);

        $this->keamanan = User::factory()->create([
            'role' => 'keamanan',
            'email' => 'keamanan@saferoad.test',
        ]);

        $this->masyarakat = User::factory()->create([
            'role' => 'masyarakat',
            'email' => 'masyarakat@saferoad.test',
            'gender' => 'laki-laki',
        ]);

        $this->perempuan = User::factory()->create([
            'role' => 'masyarakat',
            'email' => 'perempuan@saferoad.test',
            'gender' => 'perempuan',
            'identity_status' => 'verified',
        ]);

        // Siapkan fasilitas dasar di area uji (-6.9175, 107.6191)
        SafetyFacility::create([
            'name' => 'Lampu Jalan Uji 1',
            'type' => 'street_light',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
            'status' => 'active',
            'condition' => 'aktif',
        ]);

        SafetyFacility::create([
            'name' => 'Lampu Jalan Uji 2',
            'type' => 'street_light',
            'latitude' => -6.9176,
            'longitude' => 107.6192,
            'status' => 'active',
            'condition' => 'aktif',
        ]);

        SafetyFacility::create([
            'name' => 'CCTV Uji 1',
            'type' => 'cctv',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
            'status' => 'active',
            'condition' => 'aktif',
        ]);
    }

    /**
     * Skenario 1: Laporan baru masuk, skor area berubah sesuai bobot laporan belum terverifikasi.
     */
    public function test_skenario_1_new_report_reduces_score_with_unverified_weight(): void
    {
        $lat = -6.9175;
        $lng = 107.6191;

        // Skor awal sebelum ada laporan
        $initial = $this->scoreService->calculateAreaScore($lat, $lng, 0.50);
        $this->assertGreaterThanOrEqual(80, $initial['score']);

        // Masyarakat mengirim laporan baru dengan foto
        $photo = UploadedFile::fake()->image('bukti.jpg');
        $response = $this->actingAs($this->masyarakat)->postJson('/api/reports', [
            'category' => 'Pencurian',
            'description' => 'Pencurian sepeda motor di depan minimarket',
            'address' => 'Jl. Pengujian No. 1',
            'latitude' => $lat,
            'longitude' => $lng,
            'incident_at' => now()->toIso8601String(),
            'photo' => $photo,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', ReportStatus::PENDING_VALIDATION->value)
            ->assertJsonPath('data.inspection_status', InspectionStatus::WAITING_VERIFICATION->value);

        // Skor setelah laporan baru harus turun karena indikasi risiko (bobot 0.35)
        $afterNewReport = $this->scoreService->calculateAreaScore($lat, $lng, 0.50);
        $this->assertLessThan($initial['score'], $afterNewReport['score'], 'Skor harus turun setelah laporan baru masuk');

        // Pastikan audit log tercatat
        $this->assertDatabaseHas('safety_score_logs', [
            'trigger_event' => 'new_report',
            'actor_role' => 'masyarakat',
        ]);
    }

    /**
     * Skenario 2: Petugas memverifikasi kejadian benar, skor dihitung ulang dan turun lebih dalam.
     */
    public function test_skenario_2_officer_verified_true_recalculates_score_lower(): void
    {
        $lat = -6.9175;
        $lng = 107.6191;

        $report = SecurityReport::create([
            'report_code' => 'SR-261128-TEST',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pencurian',
            'description' => 'Pencurian motor',
            'address' => 'Jl. Pengujian',
            'latitude' => $lat,
            'longitude' => $lng,
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'inspection_status' => InspectionStatus::WAITING_VERIFICATION->value,
            'severity' => 'tinggi',
        ]);

        $unverifiedScore = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];

        // Petugas memverifikasi kejadian benar
        $response = $this->actingAs($this->keamanan)->postJson("/api/keamanan/reports/{$report->id}/status", [
            'status' => 'verified_true',
            'inspection_note' => 'Petugas telah ke TKP dan rekaman CCTV mengonfirmasi adanya pencurian.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.inspection_status', InspectionStatus::VERIFIED_TRUE->value);

        $verifiedScore = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];

        // Skor terverifikasi harus lebih rendah daripada skor belum terverifikasi
        $this->assertLessThan($unverifiedScore, $verifiedScore, 'Skor terverifikasi harus lebih rendah dari saat pending');

        // Pastikan tercatat di safety_score_logs
        $this->assertDatabaseHas('safety_score_logs', [
            'security_report_id' => $report->id,
            'trigger_event' => 'status_verified_true',
            'actor_role' => 'keamanan',
        ]);
    }

    /**
     * Skenario 3: Petugas menyelesaikan penanganan dan memastikan kondisi aman, skor meningkat.
     */
    public function test_skenario_3_handled_and_safe_condition_increases_score(): void
    {
        $lat = -6.9175;
        $lng = 107.6191;

        $report = SecurityReport::create([
            'report_code' => 'SR-261128-HANDLED',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pencurian',
            'description' => 'Pencurian barang',
            'address' => 'Jl. Pengujian',
            'latitude' => $lat,
            'longitude' => $lng,
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'inspection_status' => InspectionStatus::VERIFIED_TRUE->value,
            'severity' => 'tinggi',
        ]);

        $verifiedScore = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];

        // Petugas menyelesaikan penanganan
        $response = $this->actingAs($this->keamanan)->postJson("/api/keamanan/reports/{$report->id}/status", [
            'status' => 'handled',
            'inspection_note' => 'Pelaku telah diamankan dan patroli rutin ditingkatkan.',
            'needs_action' => false,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.inspection_status', InspectionStatus::HANDLED->value);

        $handledScore = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];

        // Skor setelah ditangani harus meningkat dibanding saat kejadian terverifikasi
        $this->assertGreaterThan($verifiedScore, $handledScore, 'Skor setelah penanganan harus naik');

        // Lanjut ke kondisi aman
        $this->actingAs($this->keamanan)->postJson("/api/keamanan/reports/{$report->id}/status", [
            'status' => 'safe_condition',
            'inspection_note' => 'Lokasi dipastikan kondusif dan aman sepenuhnya.',
        ]);

        $safeScore = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];
        $this->assertGreaterThanOrEqual($handledScore, $safeScore);
    }

    /**
     * Skenario 4: Laporan dinyatakan tidak terbukti, kontribusi risiko laporan dihapus.
     */
    public function test_skenario_4_unproven_report_removes_risk_penalty(): void
    {
        $lat = -6.9175;
        $lng = 107.6191;

        $initialScore = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];

        // Laporan baru masuk
        $report = SecurityReport::create([
            'report_code' => 'SR-261128-FAKE',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pencurian',
            'description' => 'Laporan palsu tidak ada kejadian',
            'address' => 'Jl. Pengujian',
            'latitude' => $lat,
            'longitude' => $lng,
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'inspection_status' => InspectionStatus::WAITING_VERIFICATION->value,
        ]);

        $scoreWithReport = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];
        $this->assertLessThan($initialScore, $scoreWithReport);

        // Petugas menyatakan tidak terbukti
        $response = $this->actingAs($this->keamanan)->postJson("/api/keamanan/reports/{$report->id}/status", [
            'status' => 'unproven',
            'inspection_note' => 'Setelah dilakukan olah TKP, tidak ditemukan kejadian apapun. Laporan tidak terbukti.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.inspection_status', InspectionStatus::UNPROVEN->value);

        $scoreAfterUnproven = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];

        // Skor harus pulih ke skor awal
        $this->assertEquals($initialScore, $scoreAfterUnproven, 'Kontribusi risiko harus hilang sepenuhnya saat tidak terbukti');
    }

    /**
     * Skenario 5: Terdapat beberapa laporan pada lokasi yang sama, sistem tidak menghitung ganda.
     */
    public function test_skenario_5_multiple_reports_at_same_location_do_not_double_count(): void
    {
        $lat = -6.9175;
        $lng = 107.6191;

        // 1 Laporan
        $report1 = SecurityReport::create([
            'report_code' => 'SR-261128-001',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pencurian',
            'description' => 'Kejadian motor hilang',
            'address' => 'Jl. Pengujian',
            'latitude' => $lat,
            'longitude' => $lng,
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'inspection_status' => InspectionStatus::VERIFIED_TRUE->value,
        ]);

        $scoreSingle = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];

        // Tambah 3 laporan lagi pada koordinat yang persis sama
        for ($i = 2; $i <= 4; $i++) {
            SecurityReport::create([
                'report_code' => "SR-261128-00{$i}",
                'user_id' => $this->masyarakat->id,
                'category' => 'Pencurian',
                'description' => "Laporan duplikat kejadian yang sama ke-{$i}",
                'address' => 'Jl. Pengujian',
                'latitude' => $lat,
                'longitude' => $lng,
                'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
                'inspection_status' => InspectionStatus::VERIFIED_TRUE->value,
            ]);
        }

        $scoreClustered = $this->scoreService->calculateAreaScore($lat, $lng, 0.50)['score'];

        // Sistem meredam laporan klaster koordinat yang sama sehingga tidak langsung anjlok ke 0 secara linier
        $this->assertGreaterThan(0, $scoreClustered, 'Skor tidak boleh jatuh ke 0 karena peredaman klaster ganda');
    }

    /**
     * Skenario 6: Ada laporan baru pada salah satu alternatif rute, perbandingan rute ikut diperbarui.
     */
    public function test_skenario_6_new_report_on_route_updates_route_comparison(): void
    {
        // Rute 1 melewati titik A (-6.9175, 107.6191)
        // Rute 2 melewati titik B (-6.9300, 107.6300) yang jauh dari titik A
        $routeA = [
            'id' => 'Route_A',
            'duration' => 600,
            'coordinates' => [
                [-6.9170, 107.6180],
                [-6.9175, 107.6191],
                [-6.9180, 107.6200],
            ],
        ];

        $routeB = [
            'id' => 'Route_B',
            'duration' => 650,
            'coordinates' => [
                [-6.9300, 107.6300],
                [-6.9310, 107.6310],
                [-6.9320, 107.6320],
            ],
        ];

        // Hitung rute awal
        $initialRoutes = $this->routeService->calculateScores([$routeA, $routeB], 'general');
        $scoreA_initial = $initialRoutes[0]['score'];

        // Tambah laporan insiden begal/perampokan di Rute A
        SecurityReport::create([
            'report_code' => 'SR-261128-BEGAL',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pencurian',
            'description' => 'Begal motor bersenjata tajam',
            'address' => 'Jl. Titik A',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'inspection_status' => InspectionStatus::VERIFIED_TRUE->value,
            'severity' => 'tinggi',
        ]);

        // Hitung ulang rute
        $updatedRoutes = $this->routeService->calculateScores([$routeA, $routeB], 'general');
        $scoreA_updated = $updatedRoutes[0]['score'];
        $scoreB_updated = $updatedRoutes[1]['score'];

        // Skor Rute A harus turun
        $this->assertLessThan($scoreA_initial, $scoreA_updated, 'Skor Rute A harus menurun setelah ada insiden di jalurnya');

        // Rekomendasi rute teraman berpindah jika Rute B lebih aman
        $this->assertTrue(
            $updatedRoutes[0]['general_comparison']['is_recommended'] || $updatedRoutes[1]['general_comparison']['is_recommended']
        );
    }

    /**
     * Skenario 7: Perubahan skor tidak memengaruhi area yang tidak berkaitan.
     */
    public function test_skenario_7_report_does_not_affect_unrelated_distant_areas(): void
    {
        $areaAlat = -6.9175;
        $areaAlng = 107.6191;

        // Area B berjarak ~10 km di (-6.8200, 107.6191)
        $areaBlat = -6.8200;
        $areaBlng = 107.6191;

        $initialScoreB = $this->scoreService->calculateAreaScore($areaBlat, $areaBlng, 0.50)['score'];

        // Laporan hanya ada di Area A
        SecurityReport::create([
            'report_code' => 'SR-261128-LOCAL',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pencurian',
            'description' => 'Pencurian lokal Area A',
            'address' => 'Area A',
            'latitude' => $areaAlat,
            'longitude' => $areaAlng,
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'inspection_status' => InspectionStatus::VERIFIED_TRUE->value,
            'severity' => 'tinggi',
        ]);

        $scoreBAfter = $this->scoreService->calculateAreaScore($areaBlat, $areaBlng, 0.50)['score'];

        // Area B tidak boleh terpengaruh sama sekali
        $this->assertEquals($initialScoreB, $scoreBAfter, 'Skor Area B yang jauh tidak boleh berubah');
    }

    /**
     * Pengujian API: GET /api/safety-score, GET /api/safety-score/factors, GET /api/safety-score/reports, GET /api/safety-score/logs
     */
    public function test_safety_score_api_endpoints(): void
    {
        $lat = -6.9175;
        $lng = 107.6191;

        // 1. GET /api/safety-score
        $resScore = $this->getJson("/api/safety-score?latitude={$lat}&longitude={$lng}");
        $resScore->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'score',
                    'level',
                    'components',
                    'factors',
                ],
            ]);

        // 2. GET /api/safety-score/factors
        $resFactors = $this->getJson("/api/safety-score/factors?latitude={$lat}&longitude={$lng}");
        $resFactors->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'score',
                    'components',
                    'factors',
                    'facilities_summary',
                ],
            ]);

        // 3. GET /api/safety-score/reports
        $resReports = $this->getJson("/api/safety-score/reports?latitude={$lat}&longitude={$lng}");
        $resReports->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'count',
                'data',
            ]);

        // 4. GET /api/safety-score/logs
        $resLogs = $this->getJson('/api/safety-score/logs');
        $resLogs->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination',
            ]);
    }
}
