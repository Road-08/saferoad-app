<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['latitude', 'longitude', 'address'])]
class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';

    public $timestamps = false;

    public function securityFacilities(): HasMany
    {
        return $this->hasMany(SecurityFacility::class, 'location_id', 'location_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'location_id', 'location_id');
    }
}
