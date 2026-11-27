<?php

namespace App\Services;

use Carbon\Carbon;

class NikValidator
{
    /**
     * Validate a 16‑digit Indonesian NIK.
     *
     * @param string $nik
     * @return array{valid: bool, gender: ?string, birth_date: ?Carbon, error: ?string}
     */
    public function validate(string $nik): array
    {
        // 1. Length & numeric check
        if (!ctype_digit($nik) || strlen($nik) !== 16) {
            return [
                'valid' => false,
                'gender' => null,
                'birth_date' => null,
                'error' => 'NIK harus terdiri 16 digit angka.',
            ];
        }

        // 2. Province code whitelist
        $provinceCode = (int) substr($nik, 0, 2);
        $validProvinces = [
            11,12,13,14,15,16,17,18,19,
            21,31,32,33,34,35,36,
            51,52,53,
            61,62,63,64,65,
            71,72,73,74,75,76,
            81,82,
            91,92,93,94,95,96,
        ];
        if (!in_array($provinceCode, $validProvinces, true)) {
            return [
                'valid' => false,
                'gender' => null,
                'birth_date' => null,
                'error' => 'Kode provinsi tidak valid.',
            ];
        }

        // 3. Extract date of birth (digits 7‑12)
        $dayPart = (int) substr($nik, 6, 2);
        $monthPart = (int) substr($nik, 8, 2);
        $yearPart = (int) substr($nik, 10, 2);

        $isFemale = false;
        if ($dayPart > 40) {
            $isFemale = true;
            $dayPart -= 40; // actual day
        }

        // Validate day/month range
        if ($dayPart < 1 || $dayPart > 31 || $monthPart < 1 || $monthPart > 12) {
            return [
                'valid' => false,
                'gender' => null,
                'birth_date' => null,
                'error' => 'Tanggal lahir tidak valid.',
            ];
        }

        // Determine full year
        $currentYear = (int) date('Y');
        $currentYearTwoDigits = $currentYear % 100;
        $fullYear = ($yearPart <= $currentYearTwoDigits) ? (2000 + $yearPart) : (1900 + $yearPart);

        // Build Carbon instance and validate date & future/age constraints
        try {
            $birthDate = Carbon::createFromDate($fullYear, $monthPart, $dayPart);
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'gender' => null,
                'birth_date' => null,
                'error' => 'Tanggal lahir tidak valid.',
            ];
        }

        if ($birthDate->isFuture()) {
            return [
                'valid' => false,
                'gender' => null,
                'birth_date' => null,
                'error' => 'Tanggal lahir tidak valid.',
            ];
        }

        $age = $birthDate->diffInYears(Carbon::now());
        if ($age < 17) {
            return [
                'valid' => false,
                'gender' => null,
                'birth_date' => null,
                'error' => 'Umur minimal 17 tahun.',
            ];
        }

        // 4. Sequence number (digits 13‑16) must not be 0000
        $sequence = substr($nik, 12, 4);
        if ($sequence === '0000') {
            return [
                'valid' => false,
                'gender' => null,
                'birth_date' => null,
                'error' => 'Nomor urut tidak boleh 0000.',
            ];
        }

        // All checks passed
        return [
            'valid' => true,
            'gender' => $isFemale ? 'perempuan' : 'laki-laki',
            'birth_date' => $birthDate,
            'error' => null,
        ];
    }
}
?>