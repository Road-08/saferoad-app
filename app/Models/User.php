<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'gender',
    'gender_statistics_consent',
    'role',
    'verification_status',
    'verified_gender',
    'ktp_path',
    'verified_at',
    'verification_note',
])]
#[Hidden([
    'password',
    'remember_token',
    'ktp_path',
    'verification_note',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
            'gender_statistics_consent' => 'boolean',
        ];
    }

    public function hasVerifiedFemaleIdentity(): bool
    {
        return $this->verification_status === 'approved'
            && $this->verified_gender === 'female';
    }
}
