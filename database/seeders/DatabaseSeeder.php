<?php

namespace Database\Seeders;

use App\Models\DangerZone;
use App\Models\EnvironmentalCondition;
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

        User::firstOrCreate(
            ['email' => 'siti.pending@saferoad.test'],
            [
                'name' => 'Siti Aminah (Pending)',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'phone_number' => '081234567894',
                'gender' => 'perempuan',
                'gender_consent' => true,
                'identity_status' => 'pending',
                'ktp_path' => 'identitas_ktp/sample_ktp.jpg',
            ]
        );

        User::firstOrCreate(
            ['email' => 'maya.rejected@saferoad.test'],
            [
                'name' => 'Maya Anggraeni (Rejected)',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'phone_number' => '081234567895',
                'gender' => 'perempuan',
                'gender_consent' => true,
                'identity_status' => 'rejected',
            ]
        );

        User::firstOrCreate(
            ['email' => 'ayu.unverified@saferoad.test'],
            [
                'name' => 'Ayu Wardani (Belum Verifikasi)',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'phone_number' => '081234567896',
                'gender' => 'perempuan',
                'gender_consent' => false,
                'identity_status' => 'unverified',
            ]
        );

        User::firstOrCreate(
            ['email' => 'bima.laki@saferoad.test'],
            [
                'name' => 'Bima Perkasa (Laki-laki)',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'phone_number' => '081234567897',
                'gender' => 'laki-laki',
                'gender_consent' => true,
                'identity_status' => 'verified',
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

        // 4. Laporan Insiden Contoh & Simulasi Pengujian
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
                'validation_status' => 'verified',
                'severity' => 'sedang',
                'incident_at' => now()->subDays(2),
                'is_simulation' => false,
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

        // 5. DATASET SIMULASI PENGUJIAN (Kategori Umum & Khusus Perempuan, Fasilitas, Kondisi Lingkungan)
        if (SecurityReport::where('is_simulation', true)->count() === 0) {
            $simulationReports = [
                // Koridor Rawan Kriminalitas Umum (Wonokromo / Joyoboyo)
                [
                    'report_code' => 'SIM-CRIME-01',
                    'user_id' => $user->id,
                    'category' => 'Begal',
                    'description' => '[DATA SIMULASI PENGUJIAN] Terjadi aksi begal motor oleh dua pelaku bersenjata tajam.',
                    'severity' => 'tinggi',
                    'incident_at' => now()->subDays(3),
                    'address' => 'Jl. Joyoboyo Dekat Jembatan',
                    'latitude' => -7.3015000,
                    'longitude' => 112.7360000,
                    'status' => 'map_updated',
                    'validation_status' => 'verified',
                    'is_simulation' => true,
                ],
                [
                    'report_code' => 'SIM-CRIME-02',
                    'user_id' => $user->id,
                    'category' => 'Pencurian',
                    'description' => '[DATA SIMULASI PENGUJIAN] Pencurian helm dan spion motor di pinggir jalan utama.',
                    'severity' => 'rendah',
                    'incident_at' => now()->subDays(5),
                    'address' => 'Jl. Wonokromo No. 45',
                    'latitude' => -7.3060000,
                    'longitude' => 112.7375000,
                    'status' => 'map_updated',
                    'validation_status' => 'verified',
                    'is_simulation' => true,
                ],
                [
                    'report_code' => 'SIM-CRIME-03',
                    'user_id' => $user->id,
                    'category' => 'Perampokan',
                    'description' => '[DATA SIMULASI PENGUJIAN] Penjambretan dompet dan ponsel pengendara roda dua.',
                    'severity' => 'tinggi',
                    'incident_at' => now()->subDays(12),
                    'address' => 'Jl. SMEA Wonokromo',
                    'latitude' => -7.3080000,
                    'longitude' => 112.7340000,
                    'status' => 'map_updated',
                    'validation_status' => 'verified',
                    'is_simulation' => true,
                ],
                [
                    'report_code' => 'SIM-CRIME-04',
                    'user_id' => $user->id,
                    'category' => 'Penipuan',
                    'description' => '[DATA SIMULASI PENGUJIAN] Modus penipuan pura-pura ban bocor dan minta uang paksa.',
                    'severity' => 'sedang',
                    'incident_at' => now()->subDays(20),
                    'address' => 'Jl. Raya Darmo Selatan',
                    'latitude' => -7.2980000,
                    'longitude' => 112.7320000,
                    'status' => 'submitted',
                    'validation_status' => 'pending',
                    'is_simulation' => true,
                ],
                // Koridor Insiden Khusus Perempuan (Catcalling, Pelecehan, Intimidasi)
                [
                    'report_code' => 'SIM-WOMEN-01',
                    'user_id' => $user->id,
                    'category' => 'Catcalling',
                    'description' => '[DATA SIMULASI PENGUJIAN] Gerombolan pemuda melakukan catcalling dan siulan bernada melecehkan kepada pejalan kaki perempuan.',
                    'severity' => 'sedang',
                    'incident_at' => now()->subDays(2),
                    'address' => 'Jl. Pulo Wonokromo Gang Buntu',
                    'latitude' => -7.3040000,
                    'longitude' => 112.7390000,
                    'status' => 'map_updated',
                    'validation_status' => 'verified',
                    'is_simulation' => true,
                ],
                [
                    'report_code' => 'SIM-WOMEN-02',
                    'user_id' => $user->id,
                    'category' => 'Pelecehan Seksual',
                    'description' => '[DATA SIMULASI PENGUJIAN] Pelaku pengendara motor memegang tubuh korban perempuan lalu kabur.',
                    'severity' => 'tinggi',
                    'incident_at' => now()->subDays(4),
                    'address' => 'Gang Jagir Sidoresmo',
                    'latitude' => -7.3090000,
                    'longitude' => 112.7410000,
                    'status' => 'map_updated',
                    'validation_status' => 'verified',
                    'is_simulation' => true,
                ],
                [
                    'report_code' => 'SIM-WOMEN-03',
                    'user_id' => $user->id,
                    'category' => 'Intimidasi',
                    'description' => '[DATA SIMULASI PENGUJIAN] Pengendara perempuan dibuntuti dan diancam di lorong sepi.',
                    'severity' => 'tinggi',
                    'incident_at' => now()->subDays(6),
                    'address' => 'Jl. Ketintang Baru Selatan',
                    'latitude' => -7.3180000,
                    'longitude' => 112.7310000,
                    'status' => 'map_updated',
                    'validation_status' => 'verified',
                    'is_simulation' => true,
                ],
                [
                    'report_code' => 'SIM-WOMEN-04',
                    'user_id' => $user->id,
                    'category' => 'Kekerasan terhadap Perempuan',
                    'description' => '[DATA SIMULASI PENGUJIAN] Penganiayaan terhadap pejalan kaki wanita oleh orang tak dikenal.',
                    'severity' => 'tinggi',
                    'incident_at' => now()->subDays(15),
                    'address' => 'Jl. Karah Agung',
                    'latitude' => -7.3120000,
                    'longitude' => 112.7150000,
                    'status' => 'rejected',
                    'validation_status' => 'rejected', // Laporan ditolak tidak dihitung
                    'is_simulation' => true,
                ],
            ];

            foreach ($simulationReports as $simRep) {
                SecurityReport::create($simRep);
            }
        }

        // 6. Fasilitas Tambahan (Termasuk Fasilitas Rusak & Non-Aktif untuk Uji Kondisi)
        $simFacilities = [
            [
                'name' => 'Lampu Jalan Koridor Wonokromo (Rusak)',
                'type' => 'street_light',
                'latitude' => -7.3055000,
                'longitude' => 112.7380000,
                'address' => 'Jl. Pulo Wonokromo No. 12',
                'status' => 'inactive',
                'condition' => 'rusak',
                'verification_status' => 'verified',
                'description' => '[DATA SIMULASI PENGUJIAN] Lampu penerangan jalan mati total sejak minggu lalu.',
                'is_simulation' => true,
            ],
            [
                'name' => 'CCTV Koridor Darmo Terkoneksi',
                'type' => 'cctv',
                'latitude' => -7.2960000,
                'longitude' => 112.7340000,
                'address' => 'Jl. Raya Darmo Indah',
                'status' => 'active',
                'condition' => 'aktif',
                'verification_status' => 'verified',
                'description' => '[DATA SIMULASI PENGUJIAN] Kamera HD pantau koridor pejalan kaki wanita.',
                'is_simulation' => true,
            ],
            [
                'name' => 'Pos Pengamanan Siaga Wanita & Warga',
                'type' => 'security_post',
                'latitude' => -7.3100000,
                'longitude' => 112.7230000,
                'address' => 'Jl. Ketintang Madya Utama',
                'status' => 'active',
                'condition' => 'aktif',
                'verification_status' => 'verified',
                'description' => '[DATA SIMULASI PENGUJIAN] Pos siaga keamanan dengan petugas ramah perempuan.',
                'is_simulation' => true,
            ],
        ];

        foreach ($simFacilities as $sf) {
            SafetyFacility::firstOrCreate(['name' => $sf['name']], $sf);
        }

        // 7. Data Kondisi Lingkungan (Environmental Conditions)
        $environmentalConditions = [
            [
                'location_name' => 'Koridor Kampus Ketintang',
                'latitude' => -7.3130000,
                'longitude' => 112.7265000,
                'activity_level' => 'ramai',
                'lighting_condition' => 'terang',
                'observed_at' => now()->subHours(4),
                'validation_status' => 'verified',
                'is_simulation' => true,
            ],
            [
                'location_name' => 'Kawasan Gang Gelap Wonokromo',
                'latitude' => -7.3045000,
                'longitude' => 112.7385000,
                'activity_level' => 'sepi',
                'lighting_condition' => 'gelap',
                'observed_at' => now()->subHours(6),
                'validation_status' => 'verified',
                'is_simulation' => true,
            ],
            [
                'location_name' => 'Jalur Alternatif Karah',
                'latitude' => -7.3115000,
                'longitude' => 112.7160000,
                'activity_level' => 'sedang',
                'lighting_condition' => 'cukup',
                'observed_at' => now()->subHours(8),
                'validation_status' => 'verified',
                'is_simulation' => true,
            ],
        ];

        foreach ($environmentalConditions as $env) {
            EnvironmentalCondition::firstOrCreate(['location_name' => $env['location_name']], $env);
        }
    }
}
