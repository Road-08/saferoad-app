<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuspiciousReportFlag extends Model
{
    protected $fillable = [
        'security_report_id',
        'user_id',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(
            SecurityReport::class,
            'security_report_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
