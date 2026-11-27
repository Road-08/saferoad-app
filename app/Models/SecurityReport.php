<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SecurityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_code',
        'user_id',
        'category',
        'description',
        'severity',
        'incident_at',
        'address',
        'latitude',
        'longitude',
        'status',
        'validation_status',
        'is_simulation',
        'is_anonymous',
        'admin_note',
        'forwarded_at',
        'forwarded_by',
        'inspection_note',
        'needs_action',
        'action_note',
        'inspected_by',
        'security_confirmed_at',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'incident_at' => 'datetime',
            'forwarded_at' => 'datetime',
            'security_confirmed_at' => 'datetime',
            'handled_at' => 'datetime',
            'is_simulation' => 'boolean',
            'is_anonymous' => 'boolean',
            'needs_action' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forwardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(SecurityReportHistory::class)->latest();
    }

    public function flags(): HasMany
    {
        return $this->hasMany(SuspiciousReportFlag::class);
    }

    public function statusEnum(): ?ReportStatus
    {
        return ReportStatus::tryFrom($this->status);
    }

    public function statusLabel(): string
    {
        return $this->statusEnum()?->label() ?? $this->status;
    }

    public function isPendingValidation(): bool
    {
        return $this->status === ReportStatus::PENDING_VALIDATION->value;
    }

    public function isForwardedToSecurity(): bool
    {
        return $this->status === ReportStatus::FORWARDED_TO_SECURITY->value;
    }

    public function isHandled(): bool
    {
        return $this->status === ReportStatus::HANDLED->value;
    }

    public function isRejected(): bool
    {
        return $this->status === ReportStatus::REJECTED->value;
    }

    public function isNeedsRevision(): bool
    {
        return $this->status === ReportStatus::NEEDS_REVISION->value;
    }

    public function isSecurityConfirmed(): bool
    {
        return ! is_null($this->security_confirmed_at);
    }

    public function scopeActiveOnMap(Builder $query): Builder
    {
        return $query->where('status', ReportStatus::HANDLED->value);
    }

    public function scopePendingReview(Builder $query): Builder
    {
        return $query->where('status', ReportStatus::PENDING_VALIDATION->value);
    }

    public function scopeForOfficer(Builder $query): Builder
    {
        return $query->where('status', ReportStatus::FORWARDED_TO_SECURITY->value);
    }
}
