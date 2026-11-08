<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'route_id';
    protected $fillable = [
        'start_location', 'destination', 'distance',
        'duration', 'security_score', 'security_level',
    ];
}
