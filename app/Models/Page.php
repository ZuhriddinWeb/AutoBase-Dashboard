<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    // app/Models/Page.php

    protected $fillable = [
        'title',
        'slug', // <--- MANA SHU YERDA BO'LISHI SHART
        'content',
        'status',
        'group_id'
    ];


    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_page');
    }
}
