<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SecurityReport extends Model
{
    protected $fillable = [
        'report_code',
        'user_id',
        'category',
        'description',
        'address',
        'latitude',
        'longitude',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(SecurityReportHistory::class)->latest();
    }

    public function flags(): HasMany
    {
        return $this->hasMany(SuspiciousReportFlag::class);
    }

    public function scopeActiveOnMap(Builder $query): Builder
    {
        return $query->where('status', 'map_updated');
    }

    public function scopePendingReview(Builder $query): Builder
    {
        return $query->whereIn('status', ['submitted', 'admin_review']);
    }

    public function scopeForOfficer(Builder $query): Builder
    {
        return $query->where('status', 'officer_check');
    }
}
