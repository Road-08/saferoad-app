<?php

namespace Database\Seeders;

use App\Models\DangerZone;
use App\Models\SafetyFacility;
use App\Models\SecurityReport;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pengguna untuk Setiap Role
        $admin = User::firstOrCreate(
            ['email' => 'admin@saferoad.test'],
            [
                'name' => 'Administrator SafeRoad',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone_number' => '081234567890',
                'identity_status' => 'verified',
                'identity_verified_at' => now(),
            ]
        );

        $keamanan = User::firstOrCreate(
            ['email' => 'keamanan@saferoad.test'],
            [
                'name' => 'Petugas Keamanan Wilayah',
                'password' => Hash::make('password'),
                'role' => 'keamanan',
                'phone_number' => '081234567891',
                'identity_status' => 'verified',
                'identity_verified_at' => now(),
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'user@saferoad.test'],
            [
                'name' => 'Rina Salsabila (Masyarakat)',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'phone_number' => '081234567892',
                'gender' => 'perempuan',
                'gender_consent' => true,
                'identity_status' => 'verified',
                'identity_verified_at' => now(),
            ]
        );

        $pendingUser = User::firstOrCreate(
            ['email' => 'pending@saferoad.test'],
            [
                'name' => 'Budi Santoso (Menunggu Verifikasi)',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'phone_number' => '081234567893',
                'gender' => 'laki-laki',
                'gender_consent' => true,
                'identity_status' => 'pending',
                'ktp_path' => 'identitas_ktp/sample_ktp.jpg',
            ]
        );

        // Akun default lama agar backward compatible
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'identity_status' => 'verified',
            ]
        );

        // 2. Data Fasilitas Keamanan (CCTV, Lampu Jalan, Pos Keamanan)
        $facilities = [
            [
                'name' => 'CCTV Simpang Ketintang',
                'type' => 'cctv',
                'latitude' => -7.3152000,
                'longitude' => 112.7280000,
                'address' => 'Jl. Ketintang No. 12, Surabaya',
                'status' => 'active',
                'description' => 'Kamera pengawas PTZ 360 aktif 24 jam.',
            ],
            [
                'name' => 'CCTV Jalan Jetis Baru',
                'type' => 'cctv',
                'latitude' => -7.3138000,
                'longitude' => 112.7268000,
                'address' => 'Jl. Jetis Baru, Surabaya',
                'status' => 'active',
                'description' => 'Kamera pantau persimpangan jalan lingkungan.',
            ],
            [
                'name' => 'Lampu Jalan Penerangan Jetis',
                'type' => 'street_light',
                'latitude' => -7.3145000,
                'longitude' => 112.7275000,
                'address' => 'Jl. Jetis Kulon Gang 2',
                'status' => 'active',
                'description' => 'Penerangan LED tenaga surya aktif mulai pukul 17:30.',
            ],
            [
                'name' => 'Lampu Jalan Koridor Kampus',
                'type' => 'street_light',
                'latitude' => -7.3132000,
                'longitude' => 112.7262000,
                'address' => 'Jl. Ketintang Madya Barat',
                'status' => 'active',
                'description' => 'Lampu sorot penerangan jalan umum utama.',
            ],
            [
                'name' => 'Pos Jaga Jetis Kulon',
                'type' => 'security_post',
                'latitude' => -7.3147000,
                'longitude' => 112.7291000,
                'address' => 'Pos Kamling Jetis Kulon RW 03',
                'status' => 'active',
                'description' => 'Pos ronda warga dengan petugas jaga malam.',
            ],
            [
                'name' => 'Pos Satpam Keamanan Utama Kampus',
                'type' => 'security_post',
                'latitude' => -7.3126000,
                'longitude' => 112.7258000,
                'address' => 'Gerbang Kampus Telkom University Surabaya',
                'status' => 'active',
                'description' => 'Pos satpam siaga 24 jam dengan pertolongan pertama.',
            ],
        ];

        foreach ($facilities as $facility) {
            SafetyFacility::firstOrCreate(['name' => $facility['name']], $facility);
        }

        // 3. Data Titik Rawan (Danger Zones / Blackspots)
        $dangerZones = [
            [
                'name' => 'Gang Gelap Jetis Kulon',
                'danger_level' => 'sedang',
                'category' => 'minim_penerangan',
                'latitude' => -7.3155000,
                'longitude' => 112.7270000,
                'radius_meters' => 120,
                'description' => 'Penerangan minim dan gang sempit, disarankan tidak melintas sendirian di atas jam 23.00.',
                'is_active' => true,
            ],
            [
                'name' => 'Persimpangan Rawan Jambret Wonokromo',
                'danger_level' => 'tinggi',
                'category' => 'begal',
                'latitude' => -7.3050000,
                'longitude' => 112.7350000,
                'radius_meters' => 200,
                'description' => 'Riwayat aksi penjambretan tas pengendara motor pada malam hari.',
                'is_active' => true,
            ],
        ];

        foreach ($dangerZones as $dz) {
            DangerZone::firstOrCreate(['name' => $dz['name']], $dz);
        }

        // 4. Laporan Insiden Contoh
        if (SecurityReport::count() === 0) {
            $report = SecurityReport::create([
                'report_code' => 'SR-' . now()->format('ymd') . '-A1B2',
                'user_id' => $user->id,
                'category' => 'Kondisi Jalan',
                'description' => 'Lampu jalan padam dan jalan berlubang cukup dalam di dekat persimpangan.',
                'address' => 'Jl. Ketintang Barat No. 5',
                'latitude' => -7.3140000,
                'longitude' => 112.7270000,
                'status' => 'officer_check',
            ]);

            $report->histories()->create([
                'status' => 'submitted',
                'note' => 'Laporan dikirimkan oleh warga.',
            ]);

            $report->histories()->create([
                'status' => 'officer_check',
                'note' => "[Petugas: {$keamanan->name}] Petugas sedang melakukan pengecekan fisik lokasi lampu jalan.",
            ]);
        }
    }
}
