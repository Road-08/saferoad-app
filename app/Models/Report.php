<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'location_id', 'category', 'event_time', 'description', 'status', 'admin_note'])]
#[Hidden(['user_id', 'admin_note'])]
class Report extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';

    protected function casts(): array
    {
        return [
            'event_time' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }
}
