<?php

namespace Tests\Feature;

use App\Models\SecurityReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SR19_SuspiciousReportFlagTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_flag_report_as_suspicious(): void
    {
        $author = User::factory()->create();
        $flagger = User::factory()->create();

        $report = SecurityReport::create([
            'report_code' => 'SR-261117-FLAG',
            'user_id' => $author->id,
            'category' => 'Lainnya',
            'description' => 'Laporan palsu / prank',
            'address' => 'Jl. Ketintang',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($flagger)->postJson("/laporan/{$report->id}/tandai-mencurigakan");

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Laporan berhasil ditandai sebagai mencurigakan.',
        ]);

        $this->assertDatabaseHas('suspicious_report_flags', [
            'security_report_id' => $report->id,
            'user_id' => $flagger->id,
        ]);
    }

    public function test_user_cannot_flag_the_same_report_multiple_times(): void
    {
        $author = User::factory()->create();
        $flagger = User::factory()->create();

        $report = SecurityReport::create([
            'report_code' => 'SR-261117-DUP',
            'user_id' => $author->id,
            'category' => 'Lainnya',
            'description' => 'Laporan terindikasi hoaks',
            'address' => 'Jl. Ketintang',
            'status' => 'submitted',
        ]);

        // Penandaan pertama (sukses)
        $this->actingAs($flagger)
            ->postJson("/laporan/{$report->id}/tandai-mencurigakan")
            ->assertStatus(201);

        // Penandaan kedua oleh user yang sama (harus ditolak dengan HTTP 409 Conflict)
        $duplicateResponse = $this->actingAs($flagger)
            ->postJson("/laporan/{$report->id}/tandai-mencurigakan");

        $duplicateResponse->assertStatus(409);
        $duplicateResponse->assertJson([
            'success' => false,
            'message' => 'Kamu sudah menandai laporan ini sebelumnya.',
        ]);

        // Pastikan jumlah flag di database tetap 1
        $this->assertDatabaseCount('suspicious_report_flags', 1);
    }

    public function test_guest_cannot_flag_report(): void
    {
        $author = User::factory()->create();

        $report = SecurityReport::create([
            'report_code' => 'SR-261117-GST',
            'user_id' => $author->id,
            'category' => 'Lainnya',
            'description' => 'Laporan tes',
            'address' => 'Jl. Ketintang',
            'status' => 'submitted',
        ]);

        $response = $this->post("/laporan/{$report->id}/tandai-mencurigakan");

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('suspicious_report_flags', 0);
    }
}
