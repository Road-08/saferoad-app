<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityReportHistory extends Model
{
    protected $fillable = [
        'security_report_id',
        'status',
        'note',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(
            SecurityReport::class,
            'security_report_id'
        );
    }
}
