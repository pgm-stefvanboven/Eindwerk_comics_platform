<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = ['poster', 'title', 'description', 'image', 'price', 'type'];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}