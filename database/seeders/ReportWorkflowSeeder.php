<?php

namespace Database\Seeders;

use App\Enums\ReportStatus;
use App\Models\SecurityReport;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReportWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan 4 user utama ada
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

        $perempuan = User::firstOrCreate(
            ['email' => 'perempuan@saferoad.test'],
            [
                'name' => 'Siti Nurhaliza (Perempuan)',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'gender' => 'perempuan',
                'phone_number' => '081234567892',
                'identity_status' => 'verified',
                'gender_consent' => true,
                'identity_verified_at' => now(),
            ]
        );

        $laki = User::firstOrCreate(
            ['email' => 'laki@saferoad.test'],
            [
                'name' => 'Budi Santoso (Laki-laki)',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'gender' => 'laki-laki',
                'phone_number' => '081234567893',
                'identity_status' => 'verified',
                'gender_consent' => true,
                'identity_verified_at' => now(),
            ]
        );

        // 2. Buat Laporan Contoh di Setiap Status
        // A. pending_validation (Baru 5 menit lalu)
        $r1 = SecurityReport::firstOrCreate(
            ['report_code' => 'SR-2611-PND1'],
            [
                'user_id' => $laki->id,
                'category' => 'Pencurian',
                'description' => 'Percobaan pencurian helm di parkiran pinggir jalan dekat minimarket.',
                'address' => 'Jl. Ketintang Baru No. 18, Surabaya',
                'latitude' => -7.3142000,
                'longitude' => 112.7275000,
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'incident_at' => now()->subMinutes(15),
                'is_anonymous' => false,
                'created_at' => now()->subMinutes(5),
                'updated_at' => now()->subMinutes(5),
            ]
        );
        if ($r1->wasRecentlyCreated) {
            $r1->histories()->create([
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'note' => 'Laporan berhasil dikirim dan menunggu validasi admin.',
                'actor_id' => $laki->id,
                'actor_role' => 'masyarakat',
                'created_at' => now()->subMinutes(5),
            ]);
        }

        // B. pending_validation kedua (Anonim, 1 jam lalu)
        $r2 = SecurityReport::firstOrCreate(
            ['report_code' => 'SR-2611-PND2'],
            [
                'user_id' => $perempuan->id,
                'category' => 'Pelecehan / Gangguan Keamanan',
                'description' => 'Ada oknum catcalling dan membuntuti pejalan kaki saat lampu jalan redup.',
                'address' => 'Gang Masjid Ketintang Timur',
                'latitude' => -7.3155000,
                'longitude' => 112.7290000,
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'incident_at' => now()->subHours(2),
                'is_anonymous' => true,
                'created_at' => now()->subHour(),
                'updated_at' => now()->subHour(),
            ]
        );
        if ($r2->wasRecentlyCreated) {
            $r2->histories()->create([
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'note' => 'Laporan berhasil dikirim dan menunggu validasi admin.',
                'actor_id' => $perempuan->id,
                'actor_role' => 'masyarakat',
                'created_at' => now()->subHour(),
            ]);
        }

        // C. forwarded_to_security (BELUM DIKONFIRMASI oleh keamanan)
        $r3 = SecurityReport::firstOrCreate(
            ['report_code' => 'SR-2611-FWD1'],
            [
                'user_id' => $laki->id,
                'category' => 'Kondisi Jalan',
                'description' => 'Penerangan lampu jalan padam sepanjang 150 meter, rawan kecelakaan di tikungan.',
                'address' => 'Jl. Ahmad Yani KM 7',
                'latitude' => -7.3125000,
                'longitude' => 112.7310000,
                'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
                'forwarded_at' => now()->subHours(2),
                'forwarded_by' => $admin->id,
                'incident_at' => now()->subHours(3),
                'is_anonymous' => false,
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(2),
            ]
        );
        if ($r3->wasRecentlyCreated) {
            $r3->histories()->create([
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'note' => 'Laporan berhasil dikirim dan menunggu validasi admin.',
                'actor_id' => $laki->id,
                'actor_role' => 'masyarakat',
                'created_at' => now()->subHours(3),
            ]);
            $r3->histories()->create([
                'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
                'note' => 'Laporan disetujui oleh admin dan diteruskan ke petugas keamanan.',
                'actor_id' => $admin->id,
                'actor_role' => 'admin',
                'created_at' => now()->subHours(2),
            ]);
        }

        // D. forwarded_to_security (SUDAH DIKONFIRMASI oleh keamanan, siap difinalisasi admin)
        $r4 = SecurityReport::firstOrCreate(
            ['report_code' => 'SR-2611-CONF'],
            [
                'user_id' => $perempuan->id,
                'category' => 'Fasilitas Keamanan',
                'description' => 'Tiang pos pantau keamanan rusak dan kamera CCTV miring tidak mengarah ke jalan.',
                'address' => 'Dekat Halte Wonokromo',
                'latitude' => -7.3080000,
                'longitude' => 112.7345000,
                'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
                'forwarded_at' => now()->subHours(4),
                'forwarded_by' => $admin->id,
                'inspected_by' => $keamanan->id,
                'inspection_note' => 'Petugas telah memeriksa TKP. Pos pantau telah diamankan dan koordinasi perbaikan CCTV sedang diproses dinas perhubungan.',
                'needs_action' => true,
                'action_note' => 'Penambahan patroli petugas setiap 2 jam di sekitar halte.',
                'security_confirmed_at' => now()->subHour(),
                'incident_at' => now()->subHours(5),
                'is_anonymous' => true,
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHour(),
            ]
        );
        if ($r4->wasRecentlyCreated) {
            $r4->histories()->create([
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'note' => 'Laporan berhasil dikirim dan menunggu validasi admin.',
                'actor_id' => $perempuan->id,
                'actor_role' => 'masyarakat',
                'created_at' => now()->subHours(5),
            ]);
            $r4->histories()->create([
                'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
                'note' => 'Laporan disetujui oleh admin dan diteruskan ke petugas keamanan.',
                'actor_id' => $admin->id,
                'actor_role' => 'admin',
                'created_at' => now()->subHours(4),
            ]);
            $r4->histories()->create([
                'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
                'note' => 'Pemeriksaan selesai oleh Petugas Keamanan Wilayah: Petugas telah memeriksa TKP. Pos pantau telah diamankan. | Tindakan: Penambahan patroli petugas setiap 2 jam di sekitar halte.',
                'actor_id' => $keamanan->id,
                'actor_role' => 'keamanan',
                'created_at' => now()->subHour(),
            ]);
        }

        // E. needs_revision (Perlu Perbaikan)
        $r5 = SecurityReport::firstOrCreate(
            ['report_code' => 'SR-2611-REV1'],
            [
                'user_id' => $laki->id,
                'category' => 'Lainnya',
                'description' => 'Ada aktivitas mencurigakan di malam hari.',
                'admin_note' => 'Mohon tambahkan patokan jalan atau nomor rumah yang jelas agar petugas dapat mengecek lokasi.',
                'address' => 'Surabaya',
                'latitude' => -7.3100000,
                'longitude' => 112.7250000,
                'status' => ReportStatus::NEEDS_REVISION->value,
                'incident_at' => now()->subDays(1),
                'is_anonymous' => false,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(8),
            ]
        );
        if ($r5->wasRecentlyCreated) {
            $r5->histories()->create([
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'note' => 'Laporan berhasil dikirim dan menunggu validasi admin.',
                'actor_id' => $laki->id,
                'actor_role' => 'masyarakat',
                'created_at' => now()->subDays(1),
            ]);
            $r5->histories()->create([
                'status' => ReportStatus::NEEDS_REVISION->value,
                'note' => 'Mohon tambahkan patokan jalan atau nomor rumah yang jelas agar petugas dapat mengecek lokasi.',
                'actor_id' => $admin->id,
                'actor_role' => 'admin',
                'created_at' => now()->subHours(8),
            ]);
        }

        // F. handled (Ditangani / Selesai / Tayang di peta)
        $r6 = SecurityReport::firstOrCreate(
            ['report_code' => 'SR-2611-HND1'],
            [
                'user_id' => $perempuan->id,
                'category' => 'Kondisi Jalan',
                'description' => 'Pohon tumbang menutup separuh badan jalan dan membahayakan pengendara malam.',
                'address' => 'Jl. Ketintang Selatan Dekat Kampus',
                'latitude' => -7.3130000,
                'longitude' => 112.7285000,
                'status' => ReportStatus::HANDLED->value,
                'forwarded_at' => now()->subDays(2),
                'forwarded_by' => $admin->id,
                'inspected_by' => $keamanan->id,
                'inspection_note' => 'Petugas dan dinas kebersihan telah mengevakuasi dahan pohon. Jalanan sudah bersih dan aman dilalui.',
                'needs_action' => true,
                'action_note' => 'Pembersihan dahan pohon oleh dinas DKRTH.',
                'security_confirmed_at' => now()->subDays(2)->addHours(2),
                'handled_at' => now()->subDays(2)->addHours(3),
                'incident_at' => now()->subDays(2)->subHours(2),
                'is_anonymous' => false,
                'created_at' => now()->subDays(2)->subHours(1),
                'updated_at' => now()->subDays(2)->addHours(3),
            ]
        );
        if ($r6->wasRecentlyCreated) {
            $r6->histories()->create([
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'note' => 'Laporan berhasil dikirim dan menunggu validasi admin.',
                'actor_id' => $perempuan->id,
                'actor_role' => 'masyarakat',
                'created_at' => now()->subDays(2)->subHours(1),
            ]);
            $r6->histories()->create([
                'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
                'note' => 'Laporan disetujui oleh admin dan diteruskan ke petugas keamanan.',
                'actor_id' => $admin->id,
                'actor_role' => 'admin',
                'created_at' => now()->subDays(2),
            ]);
            $r6->histories()->create([
                'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
                'note' => 'Pemeriksaan selesai oleh Petugas Keamanan Wilayah: Petugas dan dinas kebersihan telah mengevakuasi dahan pohon.',
                'actor_id' => $keamanan->id,
                'actor_role' => 'keamanan',
                'created_at' => now()->subDays(2)->addHours(2),
            ]);
            $r6->histories()->create([
                'status' => ReportStatus::HANDLED->value,
                'note' => 'Laporan difinalisasi oleh admin dan ditandai sebagai ditangani.',
                'actor_id' => $admin->id,
                'actor_role' => 'admin',
                'created_at' => now()->subDays(2)->addHours(3),
            ]);
        }

        // G. rejected (Ditolak)
        $r7 = SecurityReport::firstOrCreate(
            ['report_code' => 'SR-2611-REJ1'],
            [
                'user_id' => $laki->id,
                'category' => 'Pencurian',
                'description' => 'Laporan pengujian coba-coba iseng.',
                'admin_note' => 'Laporan ditolak karena tidak menyertakan informasi riil atau bukti kejadian.',
                'address' => 'Test Location',
                'latitude' => -7.3110000,
                'longitude' => 112.7240000,
                'status' => ReportStatus::REJECTED->value,
                'incident_at' => now()->subDays(3),
                'is_anonymous' => false,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3)->addHours(1),
            ]
        );
        if ($r7->wasRecentlyCreated) {
            $r7->histories()->create([
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'note' => 'Laporan berhasil dikirim dan menunggu validasi admin.',
                'actor_id' => $laki->id,
                'actor_role' => 'masyarakat',
                'created_at' => now()->subDays(3),
            ]);
            $r7->histories()->create([
                'status' => ReportStatus::REJECTED->value,
                'note' => 'Laporan ditolak karena tidak menyertakan informasi riil atau bukti kejadian.',
                'actor_id' => $admin->id,
                'actor_role' => 'admin',
                'created_at' => now()->subDays(3)->addHours(1),
            ]);
        }
    }
}
