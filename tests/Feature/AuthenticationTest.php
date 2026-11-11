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

    public function test_user_can_register_and_is_authenticated(): void
    {
        $response = $this->post('/register', [
            'name' => 'Rina Aman',
            'email' => 'Rina@example.com',
            'password' => 'SafeRoad123',
            'password_confirmation' => 'SafeRoad123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Rina Aman',
            'email' => 'rina@example.com',
        ]);

        $user = User::where('email', 'rina@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('SafeRoad123', $user->password));
    }

    public function test_registration_rejects_unconfirmed_password(): void
    {
        $this->from('/register')
            ->post('/register', [
                'name' => 'Rina Aman',
                'email' => 'rina@example.com',
                'password' => 'SafeRoad123',
                'password_confirmation' => 'Different123',
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
        ]);

        $this->post('/login', [
            'email' => 'SAM@example.com',
            'password' => 'SafeRoad123',
            'remember' => '1',
        ])->assertRedirect(route('dashboard'));

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
}
