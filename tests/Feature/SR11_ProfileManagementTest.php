<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SR11_ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);

        $response = $this->actingAs($user)->get('/profil');

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('budi@example.com');
    }

    public function test_guest_accessing_profile_should_be_protected_by_auth(): void
    {
        // Berdasarkan requirement SR-11 dan standar keamanan NFR-04,
        // halaman profil akun seharusnya dilindungi middleware auth.
        $response = $this->get('/profil');

        $response->assertRedirect(route('login'));
    }

    public function test_guest_accessing_identity_verification_should_be_protected_by_auth(): void
    {
        // Halaman verifikasi dokumen identitas sensitif (KTP) wajib dilindungi auth.
        $response = $this->get('/verifikasi-identitas');

        $response->assertRedirect(route('login'));
    }
}
