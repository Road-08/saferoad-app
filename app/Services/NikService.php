<?php

namespace App\Services;

class NikService
{
    /**
     * Detect gender from NIK.
     *
     * @param string $nik
     * @return array{valid: bool, gender: ?string, message: string}
     */
    public static function detect(string $nik): array
    {
        $nik = preg_replace('/\D/', '', $nik);

        if (strlen($nik) !== 16) {
            return [
                'valid' => false,
                'gender' => null,
                'message' => 'NIK harus 16 digit angka.',
            ];
        }

        $tanggal = (int) substr($nik, 6, 2);
        $bulan = (int) substr($nik, 8, 2);

        if ($tanggal > 40) {
            $gender = 'perempuan';
            $tanggal -= 40;
        } else {
            $gender = 'laki-laki';
        }

        if ($tanggal < 1 || $tanggal > 31 || $bulan < 1 || $bulan > 12) {
            return [
                'valid' => false,
                'gender' => null,
                'message' => 'NIK tidak valid.',
            ];
        }

        return [
            'valid' => true,
            'gender' => $gender,
            'message' => 'Terdeteksi: ' . ucfirst($gender),
        ];
    }
}
