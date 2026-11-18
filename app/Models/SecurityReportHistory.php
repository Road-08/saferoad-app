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
        'actor_id',
        'actor_role',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(
            SecurityReport::class,
            'security_report_id'
        );
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
