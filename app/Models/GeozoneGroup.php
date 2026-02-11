<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeozoneGroup extends Model
{
    use HasFactory;
    protected $fillable = ['wialon_id', 'name'];
}
