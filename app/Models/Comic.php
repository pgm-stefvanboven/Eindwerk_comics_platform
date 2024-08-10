<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    use HasFactory;

    protected $fillable = [
        'poster',
        'title',
        'price', 
        'type',
        'total',
        'publisher',
        'description',
        'thumbnail',

    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}