<?php

namespace Tests\Feature;

use App\Enums\ReportStatus;
use App\Models\SecurityReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportWorkflowRealtimeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $keamanan;
    protected User $masyarakat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@saferoad.test',
            'role' => 'admin',
        ]);

        $this->keamanan = User::factory()->create([
            'email' => 'keamanan@saferoad.test',
            'role' => 'keamanan',
        ]);

        $this->masyarakat = User::factory()->create([
            'email' => 'masyarakat@saferoad.test',
            'role' => 'masyarakat',
        ]);
    }

    /**
     * Test: Laporan baru berstatus pending_validation, incident_at terisi, dan TIDAK muncul di GET keamanan.
     */
    public function test_new_report_is_pending_validation_and_not_visible_to_keamanan(): void
    {
        $response = $this->actingAs($this->masyarakat)->postJson('/laporan', [
            'category' => 'Pencurian',
            'description' => 'Ada pencurian helm di parkiran.',
            'address' => 'Jl. Ketintang No. 10',
            'latitude' => -7.3150,
            'longitude' => 112.7280,
            'incident_time' => now()->subMinutes(10)->toDateTimeLocalString(),
        ]);

        $response->assertStatus(201);
        $data = $response->json('data');

        $this->assertEquals(ReportStatus::PENDING_VALIDATION->value, $data['status']);
        $this->assertNotNull($data['incident_at']);
        $this->assertNotNull($data['reported_at']);

        // Keamanan tidak boleh melihat laporan ini
        $keamananResponse = $this->actingAs($this->keamanan)->getJson('/keamanan/data/reports');
        $keamananResponse->assertStatus(200);
        $reports = $keamananResponse->json('data');

        $reportCodes = collect($reports)->pluck('report_code')->toArray();
        $this->assertNotContains($data['report_code'], $reportCodes);
    }

    /**
     * Test: incident_time di masa depan menghasilkan error 422.
     */
    public function test_future_incident_time_returns_422(): void
    {
        $response = $this->actingAs($this->masyarakat)->postJson('/laporan', [
            'category' => 'Pencurian',
            'description' => 'Kejadian masa depan.',
            'address' => 'Jl. Masa Depan',
            'incident_time' => now()->addHours(2)->toDateTimeLocalString(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['incident_time']);
    }

    /**
     * Test: Tanpa incident_time, incident_at otomatis diisi waktu saat ini (now).
     */
    public function test_missing_incident_time_defaults_to_now(): void
    {
        $before = now()->subSeconds(2);

        $response = $this->actingAs($this->masyarakat)->postJson('/laporan', [
            'category' => 'Kondisi Jalan',
            'description' => 'Lampu jalan mati.',
            'address' => 'Jl. Gelap',
        ]);

        $response->assertStatus(201);
        $incidentAt = Carbon::parse($response->json('data.incident_at'));
        $this->assertTrue($incidentAt->greaterThanOrEqualTo($before));
    }

    /**
     * Test: Admin approve merubah status ke forwarded_to_security dan muncul di GET keamanan serta via ?since=.
     */
    public function test_admin_approve_forwards_to_security_and_appears_in_keamanan_feed(): void
    {
        $report = SecurityReport::create([
            'report_code' => 'SR-TEST-APP1',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pencurian',
            'description' => 'Test deskripsi',
            'address' => 'Jl. Ketintang',
            'status' => ReportStatus::PENDING_VALIDATION->value,
            'incident_at' => now()->subHour(),
        ]);

        $sinceTime = now()->subSeconds(2)->toIso8601String();

        // Admin approve
        $approveResponse = $this->actingAs($this->admin)->postJson("/admin/data/reports/{$report->id}/approve");
        $approveResponse->assertStatus(200);

        $this->assertEquals(ReportStatus::FORWARDED_TO_SECURITY->value, $report->fresh()->status);
        $this->assertNotNull($report->fresh()->forwarded_at);
        $this->assertEquals($this->admin->id, $report->fresh()->forwarded_by);

        // Muncul di GET keamanan biasa
        $keamananList = $this->actingAs($this->keamanan)->getJson('/keamanan/data/reports');
        $keamananList->assertStatus(200);
        $codes = collect($keamananList->json('data'))->pluck('report_code')->toArray();
        $this->assertContains('SR-TEST-APP1', $codes);

        // Muncul di GET keamanan dengan ?since
        $sinceList = $this->actingAs($this->keamanan)->getJson("/keamanan/data/reports?since=" . urlencode($sinceTime));
        $sinceList->assertStatus(200);
        $sinceCodes = collect($sinceList->json('data'))->pluck('report_code')->toArray();
        $this->assertContains('SR-TEST-APP1', $sinceCodes);
    }

    /**
     * Test: Endpoint Keamanan (?since=) tidak pernah menerima laporan pending_validation.
     */
    public function test_keamanan_since_never_receives_pending_validation_reports(): void
    {
        $sinceTime = now()->subMinutes(10)->toIso8601String();

        SecurityReport::create([
            'report_code' => 'SR-TEST-PENDING-ONLY',
            'user_id' => $this->masyarakat->id,
            'category' => 'Lainnya',
            'description' => 'Pending validation report',
            'address' => 'Jl. Pending',
            'status' => ReportStatus::PENDING_VALIDATION->value,
            'incident_at' => now(),
        ]);

        $response = $this->actingAs($this->keamanan)->getJson("/keamanan/data/reports?since=" . urlencode($sinceTime));
        $response->assertStatus(200);
        $codes = collect($response->json('data'))->pluck('report_code')->toArray();

        $this->assertNotContains('SR-TEST-PENDING-ONLY', $codes);
    }

    /**
     * Test: Konfirmasi petugas validasi inspection_note dan needs_action.
     */
    public function test_officer_confirm_validations_and_success(): void
    {
        $report = SecurityReport::create([
            'report_code' => 'SR-TEST-CONFIRM',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pelecehan / Gangguan Keamanan',
            'description' => 'Catcalling di gang',
            'address' => 'Gang Masjid',
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'forwarded_at' => now()->subHour(),
            'incident_at' => now()->subHours(2),
        ]);

        // 1. Tanpa inspection_note -> 422
        $res1 = $this->actingAs($this->keamanan)->postJson("/keamanan/data/reports/{$report->id}/confirm", [
            'needs_action' => false,
        ]);
        $res1->assertStatus(422);
        $res1->assertJsonValidationErrors(['inspection_note']);

        // 2. needs_action true tanpa action_note -> 422
        $res2 = $this->actingAs($this->keamanan)->postJson("/keamanan/data/reports/{$report->id}/confirm", [
            'inspection_note' => 'Lokasi telah diperiksa.',
            'needs_action' => true,
        ]);
        $res2->assertStatus(422);
        $res2->assertJsonValidationErrors(['action_note']);

        // 3. Confirm sukses -> status tetap, security_confirmed_at terisi
        $res3 = $this->actingAs($this->keamanan)->postJson("/keamanan/data/reports/{$report->id}/confirm", [
            'inspection_note' => 'Lokasi telah diperiksa dan situasi aman.',
            'needs_action' => false,
        ]);
        $res3->assertStatus(200);

        $report->refresh();
        $this->assertEquals(ReportStatus::FORWARDED_TO_SECURITY->value, $report->status);
        $this->assertNotNull($report->security_confirmed_at);
        $this->assertEquals($this->keamanan->id, $report->inspected_by);

        // 4. Confirm kedua kali -> 422
        $res4 = $this->actingAs($this->keamanan)->postJson("/keamanan/data/reports/{$report->id}/confirm", [
            'inspection_note' => 'Konfirmasi ulang.',
            'needs_action' => false,
        ]);
        $res4->assertStatus(422);
    }

    /**
     * Test: Finalize sebelum confirm -> 422; sesudah confirm -> handled.
     */
    public function test_finalize_before_and_after_security_confirmation(): void
    {
        $report = SecurityReport::create([
            'report_code' => 'SR-TEST-FINALIZE',
            'user_id' => $this->masyarakat->id,
            'category' => 'Kondisi Jalan',
            'description' => 'Jalan berlubang parah',
            'address' => 'Jl. Rusak',
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'forwarded_at' => now()->subHour(),
            'incident_at' => now()->subHours(2),
        ]);

        // Finalize sebelum confirm -> 422
        $failRes = $this->actingAs($this->admin)->postJson("/admin/data/reports/{$report->id}/finalize");
        $failRes->assertStatus(422);

        // Petugas melakukan konfirmasi
        $report->update([
            'inspection_note' => 'Sudah dipasang rambu peringatan.',
            'needs_action' => false,
            'inspected_by' => $this->keamanan->id,
            'security_confirmed_at' => now(),
        ]);

        // Finalize sesudah confirm -> handled
        $successRes = $this->actingAs($this->admin)->postJson("/admin/data/reports/{$report->id}/finalize");
        $successRes->assertStatus(200);

        $report->refresh();
        $this->assertEquals(ReportStatus::HANDLED->value, $report->status);
        $this->assertNotNull($report->handled_at);
    }

    /**
     * Test: Batasan peran (Role boundaries - 403 status).
     */
    public function test_role_boundary_enforcements(): void
    {
        $report = SecurityReport::create([
            'report_code' => 'SR-TEST-RBAC',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pencurian',
            'description' => 'RBAC test',
            'address' => 'Jl. RBAC',
            'status' => ReportStatus::PENDING_VALIDATION->value,
            'incident_at' => now(),
        ]);

        // 1. Keamanan akses route admin -> 403
        $this->actingAs($this->keamanan)->getJson('/admin/data/reports')->assertStatus(403);
        $this->actingAs($this->keamanan)->postJson("/admin/data/reports/{$report->id}/approve")->assertStatus(403);

        // 2. Keamanan buka detail pending_validation -> 403
        $this->actingAs($this->keamanan)->getJson("/keamanan/data/reports/{$report->id}")->assertStatus(403);

        // 3. Masyarakat akses route keamanan atau admin -> 403
        $this->actingAs($this->masyarakat)->getJson('/admin/data/reports')->assertStatus(403);
        $this->actingAs($this->masyarakat)->getJson('/keamanan/data/reports')->assertStatus(403);
    }

    /**
     * Test: Data pelapor anonim disembunyikan di response keamanan.
     */
    public function test_anonymous_reporter_data_hidden_in_keamanan_response(): void
    {
        $report = SecurityReport::create([
            'report_code' => 'SR-TEST-ANON',
            'user_id' => $this->masyarakat->id,
            'category' => 'Pelecehan / Gangguan Keamanan',
            'description' => 'Laporan sensitif anonim',
            'address' => 'Jl. Rahasia',
            'is_anonymous' => true,
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'forwarded_at' => now(),
            'incident_at' => now(),
        ]);

        $response = $this->actingAs($this->keamanan)->getJson("/keamanan/data/reports/{$report->id}");
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals('Anonim', $data['reporter_name']);
        $this->assertNull($data['user']);
        $this->assertNull($data['reporter_phone']);
        $this->assertNull($data['reporter_email']);
    }

    /**
     * Test: Setiap aksi transisi mencatat history dengan actor_role yang tepat.
     */
    public function test_transitions_record_history_with_correct_actor_role(): void
    {
        // 1. Create laporan oleh masyarakat
        $createRes = $this->actingAs($this->masyarakat)->postJson('/laporan', [
            'category' => 'Pencurian',
            'description' => 'Pencurian spion',
            'address' => 'Jl. Ketintang',
        ]);
        $reportId = $createRes->json('data.id');
        $report = SecurityReport::find($reportId);

        $this->assertDatabaseHas('security_report_histories', [
            'security_report_id' => $reportId,
            'status' => ReportStatus::PENDING_VALIDATION->value,
            'actor_role' => 'masyarakat',
        ]);

        // 2. Admin approve
        $this->actingAs($this->admin)->postJson("/admin/data/reports/{$reportId}/approve");
        $this->assertDatabaseHas('security_report_histories', [
            'security_report_id' => $reportId,
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'actor_role' => 'admin',
        ]);

        // 3. Keamanan confirm
        $this->actingAs($this->keamanan)->postJson("/keamanan/data/reports/{$reportId}/confirm", [
            'inspection_note' => 'Pemeriksaan selesai.',
            'needs_action' => false,
        ]);
        $this->assertDatabaseHas('security_report_histories', [
            'security_report_id' => $reportId,
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'actor_role' => 'keamanan',
        ]);

        // 4. Admin finalize
        $this->actingAs($this->admin)->postJson("/admin/data/reports/{$reportId}/finalize");
        $this->assertDatabaseHas('security_report_histories', [
            'security_report_id' => $reportId,
            'status' => ReportStatus::HANDLED->value,
            'actor_role' => 'admin',
        ]);
    }

    /**
     * Test: Response endpoint berisi reported_at dan incident_at dalam format ISO 8601.
     */
    public function test_response_contains_iso8601_dates(): void
    {
        $report = SecurityReport::create([
            'report_code' => 'SR-TEST-ISO',
            'user_id' => $this->masyarakat->id,
            'category' => 'Kondisi Jalan',
            'description' => 'Test ISO Date',
            'address' => 'Jl. ISO',
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'incident_at' => now(),
            'forwarded_at' => now(),
        ]);

        $resAdmin = $this->actingAs($this->admin)->getJson("/admin/data/reports/{$report->id}");
        $resAdmin->assertStatus(200);
        $dataAdmin = $resAdmin->json('data');

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $dataAdmin['reported_at']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $dataAdmin['incident_at']);

        $resMasyarakat = $this->actingAs($this->masyarakat)->getJson('/laporan/data');
        $resMasyarakat->assertStatus(200);
        $dataMasyarakat = $resMasyarakat->json('data.0');

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $dataMasyarakat['reported_at']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $dataMasyarakat['incident_at']);
    }
}
