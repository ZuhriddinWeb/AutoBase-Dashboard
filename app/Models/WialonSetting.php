<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WialonSetting extends Model
{
    use HasFactory;
    protected $fillable = [
    'base_url', 
    'token', 
    'username', 
    'password', 
    'resource_id', 
    'status'
];
protected $attributes = [
    'status' => 'active',
];
// Tokenni shifrlab saqlash tavsiya etiladi (ixtiyoriy)
protected $hidden = ['password'];
}
