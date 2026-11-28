<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafetyScoreLog extends Model
{
    protected $fillable = [
        'target_type',
        'target_identifier',
        'latitude',
        'longitude',
        'radius_km',
        'score_before',
        'score_after',
        'score_change',
        'trigger_event',
        'security_report_id',
        'actor_id',
        'actor_role',
        'details',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_km' => 'float',
        'score_before' => 'float',
        'score_after' => 'float',
        'score_change' => 'float',
        'details' => 'array',
    ];

    public function securityReport(): BelongsTo
    {
        return $this->belongsTo(SecurityReport::class, 'security_report_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function scopeForReport(Builder $query, int $reportId): Builder
    {
        return $query->where('security_report_id', $reportId);
    }

    public function scopeForArea(Builder $query, float $latitude, float $longitude, float $toleranceKm = 1.0): Builder
    {
        // 1 derajat lat ~ 111 km
        $latDelta = $toleranceKm / 111.0;
        $lngDelta = $toleranceKm / (111.0 * max(0.1, cos(deg2rad($latitude))));

        return $query->whereBetween('latitude', [$latitude - $latDelta, $latitude + $latDelta])
            ->whereBetween('longitude', [$longitude - $lngDelta, $longitude + $lngDelta]);
    }
}
