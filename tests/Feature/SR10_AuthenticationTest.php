<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SR10_AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_name_email_and_password(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => '',
            'password' => '',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Pengguna Lain',
            'email' => 'duplicate@example.com',
            'password' => 'SafeRoad123',
            'password_confirmation' => 'SafeRoad123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_requires_minimum_password_length(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'User Pendek',
            'email' => 'pendek@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
