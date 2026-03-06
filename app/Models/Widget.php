<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'i', 'type', 'title', 'x', 'y', 'w', 'h', 'data'
    ];

    // JSON ma'lumotni avtomatik Array ga o'girish uchun
    protected $casts = [
        'data' => 'array',
    ];

    // Vidjetga biriktirilgan Avtoguruhlar
    public function transportGroups()
    {
        return $this->belongsToMany(TransportGroup::class, 'widget_transport_group');
    }

    // Vidjetga biriktirilgan Geozonalar
    public function geozones()
    {
        return $this->belongsToMany(GeozoneGroup::class, 'widget_geozone_group');
    }
}