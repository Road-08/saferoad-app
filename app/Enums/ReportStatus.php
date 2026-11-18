<?php

namespace App\Enums;

enum ReportStatus: string
{
    case PENDING_VALIDATION = 'pending_validation';
    case NEEDS_REVISION = 'needs_revision';
    case FORWARDED_TO_SECURITY = 'forwarded_to_security';
    case HANDLED = 'handled';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_VALIDATION => 'Menunggu Validasi',
            self::NEEDS_REVISION => 'Perlu Perbaikan',
            self::FORWARDED_TO_SECURITY => 'Diteruskan ke Keamanan',
            self::HANDLED => 'Ditangani',
            self::REJECTED => 'Ditolak',
        };
    }

    /**
     * @return array<ReportStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING_VALIDATION => [
                self::FORWARDED_TO_SECURITY,
                self::NEEDS_REVISION,
                self::REJECTED,
            ],
            self::NEEDS_REVISION => [
                self::PENDING_VALIDATION,
                self::REJECTED,
            ],
            self::FORWARDED_TO_SECURITY => [
                self::HANDLED,
            ],
            self::HANDLED => [],
            self::REJECTED => [],
        };
    }

    public function canTransitionTo(self|string $target): bool
    {
        $targetCase = is_string($target) ? self::tryFrom($target) : $target;
        if (! $targetCase) {
            return false;
        }

        return in_array($targetCase, $this->allowedTransitions(), true);
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
}
