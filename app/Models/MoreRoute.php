<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoreRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'start',
        'end',
        'route_name',
        'start_translated',
        'end_translated',
        'coordinates_from_lat',
        'coordinates_from_long',
        'coordinates_to_lat',
        'coordinates_to_long'
    ];
}
