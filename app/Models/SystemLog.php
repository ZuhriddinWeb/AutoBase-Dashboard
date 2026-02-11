<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_name', 'action', 'status'];

    // Log yozish uchun yordamchi funksiya
    public static function record($action, $status = 'Success')
    {
        self::create([
            // Agar user kirgan bo'lsa ismini oladi, bo'lmasa 'Tizim' deb yozadi
            'user_name' => Auth::check() ? Auth::user()->name : 'Tizim (Mehmon)',
            'action' => $action,
            'status' => $status
        ]);
    }
}