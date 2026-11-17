<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyFacility extends Model
{
    use HasFactory;

    protected $table = 'safety_facilities';

    protected $fillable = [
        'name',
        'type',
        'latitude',
        'longitude',
        'address',
        'status',
        'condition',
        'verification_status',
        'description',
        'is_simulation',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_simulation' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
