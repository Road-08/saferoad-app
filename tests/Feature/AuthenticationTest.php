<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_open_login_and_registration_pages(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Selamat datang kembali');

        $this->get('/register')
            ->assertOk()
            ->assertSee('Mulai perjalananmu');
    }

    public function test_female_user_can_register_and_is_redirected_to_womens_mode(): void
    {
        // NIK Perempuan: digit 7-8 = 55 (> 40)
        $nikPerempuan = '3201015505950001';

        $response = $this->post('/register', [
            'name' => 'Rina Aman',
            'email' => 'Rina@example.com',
            'password' => 'SafeRoad123',
            'password_confirmation' => 'SafeRoad123',
            'nik' => $nikPerempuan,
        ]);

        $response->assertRedirect(route('rute'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Rina Aman',
            'email' => 'rina@example.com',
            'gender' => 'perempuan',
            'identity_status' => 'verified',
        ]);

        $user = User::where('email', 'rina@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('SafeRoad123', $user->password));
        $this->assertTrue($user->canAccessWomenZone());
    }

    public function test_male_user_can_register_and_is_redirected_to_regular_route(): void
    {
        // NIK Laki-laki: digit 7-8 = 15 (<= 40)
        $nikLaki = '3201011505950001';

        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'SafeRoad123',
            'password_confirmation' => 'SafeRoad123',
            'nik' => $nikLaki,
        ]);

        $response->assertRedirect(route('rute'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'gender' => 'laki-laki',
            'identity_status' => 'verified',
        ]);

        $user = User::where('email', 'budi@example.com')->firstOrFail();
        $this->assertFalse($user->canAccessWomenZone());
    }

    public function test_registration_rejects_unconfirmed_password(): void
    {
        $this->from('/register')
            ->post('/register', [
                'name' => 'Rina Aman',
                'email' => 'rina@example.com',
                'password' => 'SafeRoad123',
                'password_confirmation' => 'Different123',
                'nik' => '3201015505950001',
            ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('password');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create([
            'email' => 'sam@example.com',
            'password' => 'SafeRoad123',
            'role' => 'masyarakat',
            'gender' => 'laki-laki',
        ]);

        $this->post('/login', [
            'email' => 'SAM@example.com',
            'password' => 'SafeRoad123',
            'remember' => '1',
        ])->assertRedirect(route('rute'));

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_invalid_credentials_do_not_authenticate_user(): void
    {
        User::factory()->create([
            'email' => 'sam@example.com',
            'password' => 'SafeRoad123',
        ]);

        $this->from('/login')
            ->post('/login', [
                'email' => 'sam@example.com',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login_from_a_protected_feature(): void
    {
        $this->get('/laporan/terakhir')
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_reach_a_protected_feature(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson('/laporan/terakhir')
            ->assertNotFound()
            ->assertJsonPath('message', 'Belum ada laporan keamanan.');
    }

    public function test_login_attempts_are_limited_per_email_and_ip(): void
    {
        foreach (range(1, 5) as $_) {
            $this->from('/login')->post('/login', [
                'email' => 'unknown@example.com',
                'password' => 'incorrect-password',
            ]);
        }

        $this->assertTrue(RateLimiter::tooManyAttempts('unknown@example.com|127.0.0.1', 5));

        $this->from('/login')
            ->post('/login', [
                'email' => 'unknown@example.com',
                'password' => 'incorrect-password',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_login_redirects_to_respective_dashboard_based_on_role(): void
    {
        // 1. Masyarakat laki-laki login redirects ke rute umum
        $masyarakat = User::factory()->create([
            'email' => 'warga@saferoad.test',
            'password' => 'Password123',
            'role' => 'masyarakat',
            'gender' => 'laki-laki',
        ]);

        $this->post('/login', [
            'email' => 'warga@saferoad.test',
            'password' => 'Password123',
        ])->assertRedirect(route('rute'));

        $this->post('/logout');

        // 2. Masyarakat perempuan terverifikasi login redirects ke rute mode women
        $wanita = User::factory()->create([
            'email' => 'wanita@saferoad.test',
            'password' => 'Password123',
            'role' => 'masyarakat',
            'gender' => 'perempuan',
            'identity_status' => 'verified',
        ]);

        $this->post('/login', [
            'email' => 'wanita@saferoad.test',
            'password' => 'Password123',
        ])->assertRedirect(route('rute'));

        $this->post('/logout');

        // 3. Petugas Keamanan login redirects to keamanan dashboard
        $keamanan = User::factory()->create([
            'email' => 'petugas@saferoad.test',
            'password' => 'Password123',
            'role' => 'keamanan',
        ]);

        $this->post('/login', [
            'email' => 'petugas@saferoad.test',
            'password' => 'Password123',
        ])->assertRedirect(route('keamanan.dashboard'));

        $this->post('/logout');

        // 4. Admin login redirects to admin dashboard
        $admin = User::factory()->create([
            'email' => 'admin@saferoad.test',
            'password' => 'Password123',
            'role' => 'admin',
        ]);

        $this->post('/login', [
            'email' => 'admin@saferoad.test',
            'password' => 'Password123',
        ])->assertRedirect(route('admin.dashboard'));
    }
}
