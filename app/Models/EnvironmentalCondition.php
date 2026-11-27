<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvironmentalCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_name',
        'latitude',
        'longitude',
        'activity_level',
        'lighting_condition',
        'observed_at',
        'validation_status',
        'is_simulation',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'observed_at' => 'datetime',
            'is_simulation' => 'boolean',
        ];
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('validation_status', 'verified');
    }
}
