<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NikService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenderModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_nik_service_correctly_detects_gender_and_validity(): void
    {
        // Perempuan (tanggal > 40: digit 7-8 = 55 -> 55 - 40 = 15 Mei)
        $femaleNik = '3201015505950001';
        $resFemale = NikService::detect($femaleNik);
        $this->assertTrue($resFemale['valid']);
        $this->assertEquals('perempuan', $resFemale['gender']);

        // Laki-laki (tanggal 01-31: digit 7-8 = 15 -> 15 Mei)
        $maleNik = '3201011505950001';
        $resMale = NikService::detect($maleNik);
        $this->assertTrue($resMale['valid']);
        $this->assertEquals('laki-laki', $resMale['gender']);

        // Tidak valid (kurang dari 16 digit)
        $shortNik = '32010155059';
        $resShort = NikService::detect($shortNik);
        $this->assertFalse($resShort['valid']);

        // Tidak valid (tanggal 00 atau > 71)
        $invalidDay = '3201010005950001';
        $resInvalidDay = NikService::detect($invalidDay);
        $this->assertFalse($resInvalidDay['valid']);

        // Tidak valid (bulan 13)
        $invalidMonth = '3201011513950001';
        $resInvalidMonth = NikService::detect($invalidMonth);
        $this->assertFalse($resInvalidMonth['valid']);
    }

    public function test_user_model_gender_mutator_normalizes_variants(): void
    {
        $user1 = new User();
        $user1->gender = 'Female';
        $this->assertEquals('perempuan', $user1->gender);

        $user2 = new User();
        $user2->gender = 'WANITA';
        $this->assertEquals('perempuan', $user2->gender);

        $user3 = new User();
        $user3->gender = 'P';
        $this->assertEquals('perempuan', $user3->gender);

        $user4 = new User();
        $user4->gender = 'Male';
        $this->assertEquals('laki-laki', $user4->gender);

        $user5 = new User();
        $user5->gender = 'PRIA';
        $this->assertEquals('laki-laki', $user5->gender);

        $user6 = new User();
        $user6->gender = 'L';
        $this->assertEquals('laki-laki', $user6->gender);
    }

    public function test_registration_with_female_nik_unlocks_womens_zone(): void
    {
        $response = $this->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'SafeRoad123',
            'password_confirmation' => 'SafeRoad123',
            'nik' => '3201015505950002',
        ]);

        $response->assertRedirect(route('rute', ['mode' => 'women']));

        $user = User::where('email', 'siti@example.com')->firstOrFail();
        $this->assertEquals('perempuan', $user->gender);
        $this->assertEquals('verified', $user->identity_status);
        $this->assertTrue($user->canAccessWomenZone());

        // Halaman rute menampilkan akses mode perempuan
        $this->actingAs($user)->get('/rute')
            ->assertOk()
            ->assertSee('PILIHAN JENIS RUTE')
            ->assertSee('Rute Perempuan');

        // Akses langsung ke Women's Zone diizinkan
        $this->actingAs($user)->get('/womens-zone')
            ->assertOk()
            ->assertSee('Mode Perempuan Terverifikasi Aktif ✨');
    }

    public function test_registration_with_male_nik_restricts_womens_zone(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'password' => 'SafeRoad123',
            'password_confirmation' => 'SafeRoad123',
            'nik' => '3201011505950002',
        ]);

        $response->assertRedirect(route('rute'));

        $user = User::where('email', 'ahmad@example.com')->firstOrFail();
        $this->assertEquals('laki-laki', $user->gender);
        $this->assertFalse($user->canAccessWomenZone());

        // Halaman rute untuk laki-laki tidak menampilkan toggle Rute Perempuan
        $ruteRes = $this->actingAs($user)->get('/rute')->assertOk();
        $ruteRes->assertDontSee('id="tabModeWomen"', false);

        // Akses ke Women's Zone diblokir dan dialihkan ke /rute
        $wzRes = $this->actingAs($user)->get('/womens-zone');
        $wzRes->assertRedirect(route('rute'));
        $wzRes->assertSessionHas('error', 'Zona Perempuan khusus pengguna perempuan terverifikasi.');
    }

    public function test_login_flow_redirects_based_on_gender(): void
    {
        // 1. Login user perempuan -> /rute?mode=women
        $female = User::factory()->create([
            'role' => 'masyarakat',
            'gender' => 'perempuan',
            'identity_status' => 'verified',
            'email' => 'female.user@test.com',
            'password' => 'Secret123',
        ]);

        $this->post('/login', [
            'email' => 'female.user@test.com',
            'password' => 'Secret123',
        ])->assertRedirect(route('rute', ['mode' => 'women']));

        $this->post('/logout');

        // 2. Login user laki-laki -> /rute
        $male = User::factory()->create([
            'role' => 'masyarakat',
            'gender' => 'laki-laki',
            'identity_status' => 'verified',
            'email' => 'male.user@test.com',
            'password' => 'Secret123',
        ]);

        $this->post('/login', [
            'email' => 'male.user@test.com',
            'password' => 'Secret123',
        ])->assertRedirect(route('rute'));
    }

    public function test_male_user_cannot_bypass_via_intended_url(): void
    {
        $male = User::factory()->create([
            'role' => 'masyarakat',
            'gender' => 'laki-laki',
            'identity_status' => 'verified',
            'email' => 'male.intended@test.com',
            'password' => 'Secret123',
        ]);

        // Simulasikan session intended mengarah ke /womens-zone
        $this->withSession(['url.intended' => route('womens-zone')])
            ->post('/login', [
                'email' => 'male.intended@test.com',
                'password' => 'Secret123',
            ])
            ->assertRedirect(route('rute'));
    }
}
