<?php

namespace Tests\Feature;

use App\Models\SecurityReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SR17_ReportStatusMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_their_latest_report(): void
    {
        $user = User::factory()->create();

        $report = SecurityReport::create([
            'report_code' => 'SR-261117-TEST',
            'user_id' => $user->id,
            'category' => 'Pencurian',
            'description' => 'Kehilangan helm di area parkir',
            'address' => 'Jl. Ketintang Madya',
            'status' => 'submitted',
        ]);

        $report->histories()->create([
            'status' => 'submitted',
            'note' => 'Menunggu verifikasi admin.',
        ]);

        $response = $this->actingAs($user)->getJson('/laporan/terakhir');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'report_code' => 'SR-261117-TEST',
                'status' => 'submitted',
                'histories' => [
                    [
                        'status' => 'submitted',
                        'note' => 'Menunggu verifikasi admin.',
                    ],
                ],
            ],
        ]);
    }

    public function test_user_without_reports_receives_404_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/laporan/terakhir');

        $response->assertNotFound();
        $response->assertJson([
            'success' => false,
            'message' => 'Belum ada laporan keamanan.',
        ]);
    }

    public function test_user_cannot_view_another_users_latest_report(): void
    {
        $userA = User::factory()->create(['email' => 'usera@example.com']);
        $userB = User::factory()->create(['email' => 'userb@example.com']);

        // User A memiliki laporan
        SecurityReport::create([
            'report_code' => 'SR-USERA-0001',
            'user_id' => $userA->id,
            'category' => 'Pelecehan / Gangguan Keamanan',
            'description' => 'Laporan rahasia User A',
            'address' => 'Jl. Rahasia',
            'status' => 'submitted',
        ]);

        // User B yang belum punya laporan mengakses endpoint laporan terakhir
        $response = $this->actingAs($userB)->getJson('/laporan/terakhir');

        // Harus 404, tidak boleh mengekspos laporan milik User A
        $response->assertNotFound();
        $response->assertJsonPath('message', 'Belum ada laporan keamanan.');
    }

    public function test_guest_is_redirected_when_accessing_latest_report(): void
    {
        $response = $this->get('/laporan/terakhir');

        $response->assertRedirect(route('login'));
    }
}
