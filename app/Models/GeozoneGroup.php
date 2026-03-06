<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeozoneGroup extends Model
{
    use HasFactory;

    protected $table = 'geozone_groups';

    protected $fillable = [
        'wialon_id', 
        'resource_id', 
        'name', 
        'description', 
        'type', 
        'min_x', 
        'min_y', 
        'max_x', 
        'max_y', 
        'cen_x', 
        'cen_y'
    ];
    // Shu geozona ulangan vidjetlar
    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'widget_geozone_group');
    }
}