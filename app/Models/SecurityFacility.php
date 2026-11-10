<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['location_id', 'facility_type', 'facility_name', 'description', 'status'])]
class SecurityFacility extends Model
{
    use HasFactory;

    protected $primaryKey = 'facility_id';

    public $timestamps = false;

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }
}
