<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geozone extends Model
{
    use HasFactory;

    protected $fillable = [
        'wialon_id', 'resource_id', 'name', 'description', 
        'type', 'min_x', 'min_y', 'max_x', 'max_y', 'cen_x', 'cen_y'
    ];
}