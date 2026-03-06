<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;
    protected $fillable = ['wialon_id', 'name', 'imei'];

    public function transportGroups()
    {
        return $this->belongsToMany(TransportGroup::class, 'machine_transport_group');
    }
}
