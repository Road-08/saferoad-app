<?php

namespace Tests\Feature;

use App\Models\SecurityReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SR16_SecurityReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_submit_security_report(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/laporan', [
            'category' => 'Kondisi Jalan',
            'description' => 'Terdapat lubang besar dan minim pencahayaan di dekat belokan.',
            'address' => 'Jl. Jetis Kulon Gg. 10 No. 4, Surabaya',
            'latitude' => -7.3145,
            'longitude' => 112.7275,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Laporan berhasil dikirim.',
        ]);

        $this->assertDatabaseHas('security_reports', [
            'user_id' => $user->id,
            'category' => 'Kondisi Jalan',
            'address' => 'Jl. Jetis Kulon Gg. 10 No. 4, Surabaya',
            'status' => 'submitted',
        ]);

        $report = SecurityReport::first();
        $this->assertStringStartsWith('SR-', $report->report_code);

        // Verifikasi riwayat awal otomatis tercatat
        $this->assertDatabaseHas('security_report_histories', [
            'security_report_id' => $report->id,
            'status' => 'submitted',
        ]);
    }

    public function test_guest_cannot_submit_security_report(): void
    {
        $response = $this->postJson('/laporan', [
            'category' => 'Kondisi Jalan',
            'description' => 'Deskripsi laporan tanpa login',
            'address' => 'Jl. Contoh',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseCount('security_reports', 0);
    }

    public function test_security_report_validates_required_fields_and_category(): void
    {
        $user = User::factory()->create();

        // 1. Payload kosong
        $response = $this->actingAs($user)->postJson('/laporan', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['category', 'description', 'address']);

        // 2. Kategori tidak valid
        $invalidCatResponse = $this->actingAs($user)->postJson('/laporan', [
            'category' => 'Kategori Tidak Terdaftar',
            'description' => 'Deskripsi valid',
            'address' => 'Alamat valid',
        ]);
        $invalidCatResponse->assertStatus(422);
        $invalidCatResponse->assertJsonValidationErrors('category');
    }

    public function test_security_report_rejects_description_exceeding_max_characters(): void
    {
        $user = User::factory()->create();

        // Max description adalah 500 karakter
        $longDescription = str_repeat('A', 501);

        $response = $this->actingAs($user)->postJson('/laporan', [
            'category' => 'Pencurian',
            'description' => $longDescription,
            'address' => 'Jl. Ketintang Baru',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('description');
    }
}
