<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'status', 'color'];
    public function pages()
    {
        return $this->belongsToMany(Page::class, 'group_page');
    }
    public function transport_groups()
{
    return $this->belongsToMany(TransportGroup::class, 'group_transport_group');
}
}
