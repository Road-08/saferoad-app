<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Report;
use App\Models\Role;
use App\Models\SecurityFacility;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SafeRoadDemoSeeder extends Seeder
{
    public function run(): void
    {
        $roles = $this->seedRoles();
        $users = $this->seedUsers($roles);
        $locations = $this->seedLocations();

        $this->seedSecurityFacilities($locations);
        $this->seedReports($users['masyarakat'], $locations);
    }

    /**
     * @return array<string, Role>
     */
    private function seedRoles(): array
    {
        $roles = [];

        foreach (['masyarakat', 'admin', 'petugas_keamanan'] as $roleName) {
            $roles[$roleName] = Role::query()->updateOrCreate([
                'role_name' => $roleName,
            ]);
        }

        return $roles;
    }

    /**
     * @param  array<string, Role>  $roles
     * @return array<string, User>
     */
    private function seedUsers(array $roles): array
    {
        $userData = [
            'masyarakat' => [
                'name' => 'Ayu Pratiwi',
                'email' => 'ayu.pratiwi@example.test',
                'gender' => 'perempuan',
                'gender_data_consent' => true,
            ],
            'admin' => [
                'name' => 'Bima Santoso',
                'email' => 'bima.admin@example.test',
                'gender' => 'laki-laki',
                'gender_data_consent' => true,
            ],
            'petugas_keamanan' => [
                'name' => 'Dedi Kurniawan',
                'email' => 'dedi.petugas@example.test',
                'gender' => null,
                'gender_data_consent' => false,
            ],
        ];
        $users = [];

        foreach ($userData as $roleName => $attributes) {
            $users[$roleName] = User::query()->updateOrCreate(
                ['email' => $attributes['email']],
                [
                    'name' => $attributes['name'],
                    'password' => Hash::make('SafeRoadDemo123!'),
                    'role_id' => $roles[$roleName]->role_id,
                    'gender' => $attributes['gender'],
                    'gender_data_consent' => $attributes['gender_data_consent'],
                ],
            );
        }

        return $users;
    }

    /**
     * @return array<int, Location>
     */
    private function seedLocations(): array
    {
        $locationData = [
            ['-7.3140000', '112.7250000', 'Jalan Ketintang, dekat balai warga'],
            ['-7.3108000', '112.7262000', 'Jalan Ketintang Baru, area pertokoan'],
            ['-7.3171000', '112.7234000', 'Jalan Ketintang Madya, dekat permukiman'],
            ['-7.3124000', '112.7206000', 'Jalan Ketintang Barat, dekat fasilitas umum'],
            ['-7.3163000', '112.7291000', 'Jalan Ketintang Timur, area hunian'],
            ['-7.3089000', '112.7227000', 'Gang Ketintang, dekat lapangan warga'],
            ['-7.3192000', '112.7265000', 'Jalan Ketintang Selatan, dekat rumah ibadah'],
            ['-7.3115000', '112.7310000', 'Jalan Ketintang Baru, dekat halte'],
            ['-7.3200000', '112.7220000', 'Jalan Ketintang Barat, dekat taman lingkungan'],
            ['-7.3080000', '112.7283000', 'Jalan Ketintang, dekat area kampus'],
            ['-7.3152000', '112.7189000', 'Gang Ketintang Barat, area permukiman'],
            ['-7.3185000', '112.7277000', 'Jalan Ketintang Selatan, dekat warung warga'],
            ['-7.3096000', '112.7307000', 'Jalan Ketintang Timur, dekat pertokoan'],
            ['-7.3180000', '112.7305000', 'Jalan Ketintang Madya, dekat persimpangan'],
            ['-7.3078000', '112.7248000', 'Jalan Ketintang, dekat fasilitas pendidikan'],
            ['-7.3202000', '112.7299000', 'Gang Ketintang Selatan, dekat pos ronda'],
            ['-7.3117000', '112.7287000', 'Jalan Ketintang Timur, area kos'],
            ['-7.3171000', '112.7212000', 'Jalan Ketintang Barat, dekat minimarket'],
            ['-7.3081000', '112.7269000', 'Jalan Ketintang Selatan, dekat taman bermain'],
            ['-7.3199000', '112.7237000', 'Jalan Ketintang Barat, dekat perempatan'],
            ['-7.3092000', '112.7294000', 'Jalan Ketintang Selatan, area hunian'],
            ['-7.3157000', '112.7193000', 'Jalan Ketintang Timur, dekat pusat kegiatan warga'],
            ['-7.3200000', '112.7255000', 'Gang Ketintang Barat, dekat lapangan'],
            ['-7.3129000', '112.7218000', 'Jalan Ketintang Selatan, dekat pertokoan'],
            ['-7.3188000', '112.7210000', 'Jalan Ketintang, dekat area pendidikan'],
        ];
        $locations = [];

        foreach ($locationData as [$latitude, $longitude, $address]) {
            $locations[] = Location::query()->updateOrCreate(
                ['latitude' => $latitude, 'longitude' => $longitude],
                ['address' => $address],
            );
        }

        return $locations;
    }

    /**
     * @param  array<int, Location>  $locations
     */
    private function seedSecurityFacilities(array $locations): void
    {
        $facilityData = [
            [0, 'cctv', 'CCTV Simpang Ketintang', 'Memantau arus kendaraan di persimpangan.', 'aktif'],
            [3, 'cctv', 'CCTV Ketintang Barat', 'Kamera pemantau jalan lingkungan.', 'aktif'],
            [7, 'cctv', 'CCTV Halte Ketintang', 'Memantau area halte dan trotoar.', 'aktif'],
            [13, 'cctv', 'CCTV Ketintang Madya', 'Kamera pemantau kawasan permukiman.', 'perlu_perawatan'],
            [21, 'cctv', 'CCTV Ketintang Timur', 'Memantau akses menuju area pendidikan.', 'aktif'],
            [1, 'penerangan', 'Lampu Jalan Ketintang Baru 01', 'Penerangan jalan utama.', 'aktif'],
            [4, 'penerangan', 'Lampu Jalan Ketintang Timur 02', 'Penerangan ruas jalan lingkungan.', 'aktif'],
            [8, 'penerangan', 'Lampu Taman Ketintang Barat', 'Penerangan taman dan jalur pejalan kaki.', 'perlu_perawatan'],
            [15, 'penerangan', 'Lampu Gang Ketintang Selatan', 'Penerangan gang permukiman.', 'aktif'],
            [23, 'penerangan', 'Lampu Jalan Ketintang Selatan 03', 'Penerangan dekat area pertokoan.', 'aktif'],
            [2, 'pos_keamanan', 'Pos Keamanan Ketintang Madya', 'Pos ronda lingkungan warga.', 'aktif'],
            [6, 'pos_keamanan', 'Pos Keamanan Ketintang Selatan', 'Pos pemantauan keamanan warga.', 'aktif'],
            [10, 'pos_keamanan', 'Pos Ronda Ketintang Barat', 'Pos ronda di area permukiman.', 'aktif'],
            [16, 'pos_keamanan', 'Pos Keamanan Area Kos Ketintang', 'Pos bantuan untuk penghuni dan pengunjung.', 'perlu_perawatan'],
            [19, 'pos_keamanan', 'Pos Ronda Perempatan Ketintang', 'Pos ronda dekat akses jalan lingkungan.', 'aktif'],
            [5, 'titik_aman', 'Titik Aman Lapangan Ketintang', 'Tempat berkumpul sementara saat keadaan darurat.', 'aktif'],
            [9, 'titik_aman', 'Titik Aman Area Kampus', 'Lokasi aman yang mudah dijangkau pejalan kaki.', 'aktif'],
            [12, 'titik_aman', 'Titik Aman Pertokoan Timur', 'Titik temu darurat untuk pengunjung kawasan.', 'aktif'],
            [18, 'titik_aman', 'Titik Aman Taman Bermain', 'Area terbuka untuk berkumpul saat darurat.', 'aktif'],
            [24, 'titik_aman', 'Titik Aman Fasilitas Pendidikan', 'Titik bantuan di dekat fasilitas pendidikan.', 'aktif'],
        ];

        foreach ($facilityData as [$locationIndex, $facilityType, $facilityName, $description, $status]) {
            SecurityFacility::query()->updateOrCreate(
                ['facility_name' => $facilityName],
                [
                    'location_id' => $locations[$locationIndex]->location_id,
                    'facility_type' => $facilityType,
                    'description' => $description,
                    'status' => $status,
                ],
            );
        }
    }

    /**
     * @param  array<int, Location>  $locations
     */
    private function seedReports(User $reporter, array $locations): void
    {
        $reportData = [
            [1, 'pelecehan', '2025-10-01 19:20:00', 'Pengguna melaporkan pelecehan verbal di dekat halte; membutuhkan tindak lanjut.', 'menunggu_validasi', null],
            [3, 'penerangan', '2025-10-02 20:10:00', 'Lampu jalan mati sehingga ruas ini gelap pada malam hari.', 'perlu_perbaikan', 'Jadwalkan pemeriksaan lampu.'],
            [6, 'kecelakaan', '2025-10-03 08:35:00', 'Terjadi tabrakan ringan antara dua sepeda motor.', 'diteruskan_ke_keamanan', 'Informasi diteruskan ke petugas keamanan.'],
            [8, 'kriminalitas', '2025-10-04 21:15:00', 'Warga melihat upaya pencurian kendaraan di area parkir.', 'ditangani', 'Petugas sudah memeriksa lokasi.'],
            [10, 'gangguan_ketertiban', '2025-10-05 22:05:00', 'Keributan di area permukiman mengganggu warga sekitar.', 'ditolak', 'Laporan contoh ditutup karena informasi kejadian tidak dapat diverifikasi.'],
            [12, 'pelecehan', '2025-10-06 18:40:00', 'Laporan perilaku tidak pantas terhadap pejalan kaki di dekat pertokoan.', 'menunggu_validasi', null],
            [14, 'kecelakaan', '2025-10-07 07:50:00', 'Pengendara terjatuh di jalan; warga membantu mengamankan lokasi.', 'diteruskan_ke_keamanan', 'Petugas diminta membantu pengaturan lalu lintas.'],
            [17, 'kriminalitas', '2025-10-08 17:25:00', 'Warga melaporkan kehilangan barang di sekitar minimarket.', 'perlu_perbaikan', 'Mohon lengkapi perkiraan waktu kejadian.'],
            [20, 'penerangan', '2025-10-09 19:05:00', 'Beberapa lampu jalan di ruas ini tidak menyala.', 'ditangani', 'Lampu telah diperiksa oleh petugas.'],
            [23, 'pelecehan', '2025-10-10 20:30:00', 'Pengguna merasa tidak aman setelah mengalami pelecehan di jalan.', 'ditolak', 'Laporan contoh ditutup setelah pelapor mengoreksi lokasi kejadian.'],
        ];

        foreach ($reportData as [$locationIndex, $category, $eventTime, $description, $status, $adminNote]) {
            Report::query()->updateOrCreate(
                [
                    'user_id' => $reporter->getKey(),
                    'location_id' => $locations[$locationIndex]->location_id,
                    'event_time' => $eventTime,
                ],
                [
                    'category' => $category,
                    'description' => $description,
                    'status' => $status,
                    'admin_note' => $adminNote,
                ],
            );
        }
    }
}
