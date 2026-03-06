<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDashboard extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'layout'];

    // Layoutni avtomatik arrayga o'girish (Casting)
    protected $casts = [
        'layout' => 'array',
    ];
}
