<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'avatar',
        'gender',
        'gender_consent',
        'nik_hash',
        'identity_status',
        'identity_verified_at',
        'identity_rejection_note',
        'ktp_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'gender_consent' => 'boolean',
        'identity_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKeamanan(): bool
    {
        return $this->role === 'keamanan';
    }

    public function isMasyarakat(): bool
    {
        return $this->role === 'masyarakat' || empty($this->role);
    }

    public function hasRole(string|array $roles): bool
    {
        $roleList = is_array($roles) ? $roles : func_get_args();
        return in_array($this->role, $roleList, true);
    }

    public function isIdentityVerified(): bool
    {
        return $this->identity_status === 'verified';
    }

    public function isFemale(): bool
    {
        return strtolower((string) $this->gender) === 'perempuan';
    }

    public function isMale(): bool
    {
        return strtolower((string) $this->gender) === 'laki-laki';
    }

    public function canAccessWomenZone(): bool
    {
        return $this->isMasyarakat() && $this->isFemale() && $this->isIdentityVerified();
    }

    public function setGenderAttribute(?string $value): void
    {
        if ($value === null) {
            $this->attributes['gender'] = null;
            return;
        }

        $val = strtolower(trim($value));
        $map = [
            'p' => 'perempuan',
            'perempuan' => 'perempuan',
            'female' => 'perempuan',
            'wanita' => 'perempuan',
            'l' => 'laki-laki',
            'laki' => 'laki-laki',
            'male' => 'laki-laki',
            'pria' => 'laki-laki',
            'laki-laki' => 'laki-laki',
        ];

        $this->attributes['gender'] = $map[$val] ?? $val;
    }

    public function securityReports(): HasMany
    {
        return $this->hasMany(SecurityReport::class);
    }

    public function suspiciousFlags(): HasMany
    {
        return $this->hasMany(SuspiciousReportFlag::class);
    }
}