<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user', 'listing_id', 'rating', 'review'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}