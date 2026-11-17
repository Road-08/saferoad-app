<?php

namespace Tests\Feature;

use App\Models\DangerZone;
use App\Models\Rute;
use App\Models\SafetyFacility;
use App\Models\SecurityReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SafeRoadBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration_defaults_to_masyarakat_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Warga',
            'email' => 'budi@warga.test',
            'password' => 'SafeRoad123',
            'password_confirmation' => 'SafeRoad123',
            'nik' => '3201011505950001',
        ]);

        $response->assertRedirect(route('rute'));
        $user = User::where('email', 'budi@warga.test')->firstOrFail();
        $this->assertEquals('masyarakat', $user->role);
        $this->assertEquals('verified', $user->identity_status);
        $this->assertEquals('laki-laki', $user->gender);
    }

    public function test_role_based_access_control_protects_admin_and_keamanan_routes(): void
    {
        $masyarakat = User::factory()->create(['role' => 'masyarakat']);
        $keamanan = User::factory()->create(['role' => 'keamanan']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Masyarakat dilarang masuk admin & keamanan
        $this->actingAs($masyarakat)->getJson('/api/admin/stats')->assertStatus(403);
        $this->actingAs($masyarakat)->getJson('/api/keamanan/reports')->assertStatus(403);

        // Keamanan bisa akses laporan keamanan tapi dilarang admin
        $this->actingAs($keamanan)->getJson('/api/keamanan/reports')->assertStatus(200);
        $this->actingAs($keamanan)->getJson('/api/admin/stats')->assertStatus(403);

        // Admin bisa akses admin dan keamanan
        $this->actingAs($admin)->getJson('/api/admin/stats')->assertStatus(200);
        $this->actingAs($admin)->getJson('/api/keamanan/reports')->assertStatus(200);
    }

    public function test_route_safety_score_calculation_endpoint(): void
    {
        SafetyFacility::create([
            'name' => 'CCTV Test Point',
            'type' => 'cctv',
            'latitude' => -7.3150,
            'longitude' => 112.7280,
            'status' => 'active',
        ]);

        SafetyFacility::create([
            'name' => 'Lampu Test Point',
            'type' => 'street_light',
            'latitude' => -7.3140,
            'longitude' => 112.7270,
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/routes/score', [
            'routes' => [
                [
                    'id' => 'rute_a',
                    'coordinates' => [
                        [112.7290, -7.3160],
                        [112.7280, -7.3150],
                        [112.7270, -7.3140],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', 'rute_a');

        $this->assertArrayHasKey('score', $response->json('data.0'));
        $this->assertArrayHasKey('factors', $response->json('data.0'));
        $this->assertCount(4, $response->json('data.0.factors'));
    }

    public function test_womens_zone_route_selection_assigns_pink_to_safest_women_route(): void
    {
        $user = User::factory()->create();

        // Fasilitas penerangan & CCTV di koridor Rute B
        SafetyFacility::create([
            'name' => 'Lampu LED Terang Ketintang',
            'type' => 'street_light',
            'latitude' => -7.3110,
            'longitude' => 112.7210,
            'status' => 'active',
        ]);
        SafetyFacility::create([
            'name' => 'CCTV Pengawas Simpang',
            'type' => 'cctv',
            'latitude' => -7.3115,
            'longitude' => 112.7215,
            'status' => 'active',
        ]);

        // Laporan pelecehan/catcalling di koridor Rute A
        SecurityReport::create([
            'report_code' => 'SR-WOMEN-01',
            'user_id' => $user->id,
            'category' => 'Pelecehan Seksual',
            'description' => 'Ada tindakan catcalling dan intimidasi verbal di gang gelap.',
            'address' => 'Gang Gelap Rute A',
            'latitude' => -7.3200,
            'longitude' => 112.7300,
            'status' => 'map_updated',
        ]);

        $response = $this->postJson('/api/routes/score', [
            'routes' => [
                [
                    'id' => 'rute_a', // Rute dengan insiden pelecehan
                    'coordinates' => [
                        [112.7295, -7.3195],
                        [112.7300, -7.3200],
                        [112.7305, -7.3205],
                    ],
                ],
                [
                    'id' => 'rute_b', // Rute dengan lampu & CCTV terang
                    'coordinates' => [
                        [112.7205, -7.3105],
                        [112.7210, -7.3110],
                        [112.7215, -7.3115],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(200);

        $ruteAData = collect($response->json('data'))->firstWhere('id', 'rute_a');
        $ruteBData = collect($response->json('data'))->firstWhere('id', 'rute_b');

        // Pastikan Rute B (skor tertinggi untuk perempuan) mendapatkan warna PINK dan label "Paling Aman untuk Perempuan"
        $this->assertTrue($ruteBData['womens_zone']['is_pink_recommendation']);
        $this->assertEquals('#ec4899', $ruteBData['womens_zone']['route_color']);
        $this->assertEquals('pink', $ruteBData['womens_zone']['route_color_name']);
        $this->assertEquals('Paling Aman untuk Perempuan', $ruteBData['womens_zone']['badge_label']);

        // Rute A (terdapat laporan pelecehan) tidak boleh berwarna pink
        $this->assertFalse($ruteAData['womens_zone']['is_pink_recommendation']);
        $this->assertNotEquals('#ec4899', $ruteAData['womens_zone']['route_color']);
        $this->assertGreaterThan(
            $ruteAData['womens_zone']['score'],
            $ruteBData['womens_zone']['score'],
            "Skor keselamatan perempuan Rute B harus lebih tinggi dibanding Rute A yang ada laporan pelecehan."
        );
    }

    public function test_womens_zone_displays_insufficient_data_when_telemetry_lacking(): void
    {
        $response = $this->postJson('/api/routes/score', [
            'routes' => [
                [
                    'id' => 'rute_terpencil',
                    'coordinates' => [
                        [115.0000, -8.0000],
                        [115.0100, -8.0100],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(200);
        $ruteData = $response->json('data.0');

        $this->assertFalse($ruteData['womens_zone']['is_data_sufficient']);
        $this->assertEquals('Data keamanan belum mencukupi', $ruteData['womens_zone']['badge_label']);
        $this->assertFalse($ruteData['womens_zone']['is_pink_recommendation']);
    }

    public function test_route_comparison_view_and_api_endpoint(): void
    {
        // 1. Halaman perbandingan rute dapat diakses
        $this->get('/rute/bandingkan')->assertOk();

        // 2. Endpoint API perbandingan rute
        $ruteA = Rute::create([
            'start_location' => 'Titik A',
            'destination' => 'Titik B',
            'distance' => 5.2,
            'duration' => 15,
            'security_score' => 88.5,
            'security_level' => 'aman',
        ]);

        $ruteB = Rute::create([
            'start_location' => 'Titik A',
            'destination' => 'Titik B',
            'distance' => 4.8,
            'duration' => 18,
            'security_score' => 65.0,
            'security_level' => 'sedang',
        ]);

        $response = $this->getJson("/api/routes/compare?ids[]={$ruteA->route_id}&ids[]={$ruteB->route_id}");

        $response->assertStatus(200)
            ->assertJsonPath('recommended_id', $ruteA->route_id)
            ->assertJsonCount(2, 'routes');
    }

    public function test_womens_zone_access_rules_based_on_gender_and_verification(): void
    {
        // 1. Pengguna laki-laki DITOLAK: web redirect ke rute, api 403
        $male = User::factory()->create([
            'role' => 'masyarakat',
            'gender' => 'laki-laki',
            'identity_status' => 'verified',
        ]);
        $this->actingAs($male)->get('/womens-zone')->assertRedirect(route('rute'));
        $this->actingAs($male)->getJson('/api/womens-zone/status')->assertStatus(403);

        // 2. Pengguna perempuan belum verifikasi -> dialihkan ke rute
        $unverifiedFemale = User::factory()->create([
            'role' => 'masyarakat',
            'gender' => 'perempuan',
            'identity_status' => 'unverified',
        ]);
        $this->actingAs($unverifiedFemale)->get('/womens-zone')->assertRedirect(route('rute'));
        $this->actingAs($unverifiedFemale)->getJson('/api/womens-zone/status')->assertStatus(403);

        // 3. Pengguna perempuan status verified -> akses penuh
        $verifiedFemale = User::factory()->create([
            'role' => 'masyarakat',
            'gender' => 'perempuan',
            'identity_status' => 'verified',
        ]);
        $this->actingAs($verifiedFemale)->get('/womens-zone')
            ->assertOk()
            ->assertSee('Mode Perempuan Terverifikasi Aktif ✨');
        $this->actingAs($verifiedFemale)->getJson('/api/womens-zone/status')
            ->assertOk()
            ->assertJsonPath('success', true);

        // 4. Admin dan Petugas Keamanan dialihkan ke dashboard masing-masing
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/womens-zone')->assertRedirect(route('admin.dashboard'));

        $keamanan = User::factory()->create(['role' => 'keamanan']);
        $this->actingAs($keamanan)->get('/womens-zone')->assertRedirect(route('keamanan.dashboard'));
    }

    public function test_keamanan_can_follow_up_incident_reports(): void
    {
        $masyarakat = User::factory()->create(['role' => 'masyarakat']);
        $keamanan = User::factory()->create([
            'name' => 'Petugas Budi',
            'role' => 'keamanan',
        ]);

        $report = SecurityReport::create([
            'report_code' => 'SR-261117-001',
            'user_id' => $masyarakat->id,
            'category' => 'Pencurian',
            'description' => 'Helm dicuri di depan minimarket.',
            'address' => 'Jl. Jetis No. 10',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($keamanan)->postJson("/api/keamanan/reports/{$report->id}/follow-up", [
            'status' => 'officer_check',
            'note' => 'Petugas telah berkoordinasi dengan warga sekitar dan memeriksa rekaman CCTV.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'officer_check');

        $report->refresh();
        $this->assertEquals('officer_check', $report->status);
        $this->assertDatabaseHas('security_report_histories', [
            'security_report_id' => $report->id,
            'status' => 'officer_check',
        ]);
    }

    public function test_facilities_and_danger_zones_api_endpoints(): void
    {
        SafetyFacility::create([
            'name' => 'Pos Pantau Polsek',
            'type' => 'security_post',
            'latitude' => -7.3100,
            'longitude' => 112.7200,
            'status' => 'active',
        ]);

        DangerZone::create([
            'name' => 'Persimpangan Rawan',
            'danger_level' => 'tinggi',
            'category' => 'begal',
            'latitude' => -7.3000,
            'longitude' => 112.7100,
            'radius_meters' => 150,
            'is_active' => true,
        ]);

        $this->getJson('/api/facilities')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['name' => 'Pos Pantau Polsek']);

        $this->getJson('/api/danger-zones')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['name' => 'Persimpangan Rawan']);
    }
}
