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
        ]);

        $response->assertRedirect(route('dashboard'));
        $user = User::where('email', 'budi@warga.test')->firstOrFail();
        $this->assertEquals('masyarakat', $user->role);
        $this->assertEquals('unverified', $user->identity_status);
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

    public function test_user_can_submit_identity_verification(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'masyarakat',
            'identity_status' => 'unverified',
        ]);

        $file = UploadedFile::fake()->create('ktp_saya.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($user)->post('/verifikasi-identitas', [
            'ktp' => $file,
            'gender' => 'perempuan',
            'gender_consent' => '1',
        ]);

        $response->assertRedirect(route('profile.index'));

        $user->refresh();
        $this->assertEquals('pending', $user->identity_status);
        $this->assertEquals('perempuan', $user->gender);
        $this->assertTrue($user->gender_consent);
        $this->assertNotNull($user->ktp_path);
        Storage::disk('public')->assertExists($user->ktp_path);
    }

    public function test_admin_can_approve_and_reject_identity_verification(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $applicant = User::factory()->create([
            'role' => 'masyarakat',
            'identity_status' => 'pending',
            'gender' => 'laki-laki',
            'gender_consent' => true,
        ]);

        // Admin menyetujui
        $this->actingAs($admin)
            ->postJson("/api/admin/verifications/{$applicant->id}/approve")
            ->assertStatus(200)
            ->assertJsonPath('data.identity_status', 'verified');

        $applicant->refresh();
        $this->assertEquals('verified', $applicant->identity_status);
        $this->assertNotNull($applicant->identity_verified_at);

        // Test penolakan
        $applicant2 = User::factory()->create(['identity_status' => 'pending']);
        $this->actingAs($admin)
            ->postJson("/api/admin/verifications/{$applicant2->id}/reject", [
                'reason' => 'Foto KTP buram dan tidak terbaca.',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.identity_status', 'rejected');

        $applicant2->refresh();
        $this->assertEquals('rejected', $applicant2->identity_status);
        $this->assertEquals('Foto KTP buram dan tidak terbaca.', $applicant2->identity_rejection_note);
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
