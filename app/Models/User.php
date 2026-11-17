<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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
        'ktp_path',
        'identity_status',
        'identity_verified_at',
        'identity_rejection_note',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender_consent' => 'boolean',
            'identity_verified_at' => 'datetime',
        ];
    }

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

    /**
     * @param string|array<int, string> $roles
     */
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

    public function securityReports(): HasMany
    {
        return $this->hasMany(SecurityReport::class);
    }

    public function suspiciousFlags(): HasMany
    {
        return $this->hasMany(SuspiciousReportFlag::class);
    }
}
