<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportGroup extends Model
{
    use HasFactory;

    protected $fillable = ['wialon_id', 'name'];

    // 1. Yangi guruh qo'lda yaratilganda soxta (yangi) wialon_id berish
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->wialon_id)) {
                // Agar wialon_id yozilmagan bo'lsa, tasodifiy raqam beramiz
                $model->wialon_id = rand(1000000, 9999999); 
            }
        });
    }

    // 2. Mashinalar bilan ko'pdan-ko'pga (Many-to-Many) munosabat
    public function machines()
    {
        // 'machine_transport_group' - bu siz o'tgan safar yaratgan pivot jadval nomi
        return $this->belongsToMany(Machine::class, 'machine_transport_group', 'transport_group_id', 'machine_id');
    }
}