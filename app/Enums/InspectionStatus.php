<?php

namespace App\Enums;

enum InspectionStatus: string
{
    case WAITING_VERIFICATION = 'waiting_verification';
    case UNDER_INVESTIGATION = 'under_investigation';
    case VERIFIED_TRUE = 'verified_true';
    case UNPROVEN = 'unproven';
    case HANDLED = 'handled';
    case SAFE_CONDITION = 'safe_condition';

    public function label(): string
    {
        return match ($this) {
            self::WAITING_VERIFICATION => 'Menunggu Verifikasi',
            self::UNDER_INVESTIGATION => 'Sedang Diperiksa',
            self::VERIFIED_TRUE => 'Terverifikasi - Kejadian Benar',
            self::UNPROVEN => 'Tidak Terbukti',
            self::HANDLED => 'Sudah Ditangani',
            self::SAFE_CONDITION => 'Kondisi Aman',
        };
    }

    /**
     * Bobot kontribusi risiko dalam perhitungan Safety Score.
     * Rentang: 0.00 (tidak ada risiko) hingga 1.00 (risiko penuh).
     */
    public function riskWeight(): float
    {
        return match ($this) {
            self::WAITING_VERIFICATION => (float) config('safetyscore.inspection_weights.waiting_verification', 0.35),
            self::UNDER_INVESTIGATION => (float) config('safetyscore.inspection_weights.under_investigation', 0.50),
            self::VERIFIED_TRUE => (float) config('safetyscore.inspection_weights.verified_true', 1.00),
            self::UNPROVEN => (float) config('safetyscore.inspection_weights.unproven', 0.00),
            self::HANDLED => (float) config('safetyscore.inspection_weights.handled', 0.15),
            self::SAFE_CONDITION => (float) config('safetyscore.inspection_weights.safe_condition', 0.00),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }

    /**
     * Menormalkan input string menjadi case InspectionStatus.
     * Menerima format key value, format slug, maupun label Bahasa Indonesia.
     */
    public static function normalize(?string $value): self
    {
        if (empty($value)) {
            return self::WAITING_VERIFICATION;
        }

        $direct = self::tryFrom(strtolower(trim($value)));
        if ($direct) {
            return $direct;
        }

        $cleaned = strtolower(trim($value));

        return match ($cleaned) {
            'menunggu verifikasi', 'menunggu validasi', 'pending', 'pending_validation', 'waiting' => self::WAITING_VERIFICATION,
            'sedang diperiksa', 'diperiksa', 'investigating', 'inspecting', 'check', 'officer_check' => self::UNDER_INVESTIGATION,
            'terverifikasi - kejadian benar', 'terverifikasi', 'kejadian benar', 'benar', 'verified', 'verified_true' => self::VERIFIED_TRUE,
            'tidak terbukti', 'palsu', 'tidak_terbukti', 'false', 'unproven', 'rejected' => self::UNPROVEN,
            'sudah ditangani', 'ditangani', 'selesai', 'handled', 'resolved', 'fixed' => self::HANDLED,
            'kondisi aman', 'aman', 'terkendali', 'safe', 'safe_condition', 'kondusif' => self::SAFE_CONDITION,
            default => self::WAITING_VERIFICATION,
        };
    }
}
