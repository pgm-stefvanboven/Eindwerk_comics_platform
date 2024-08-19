<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'comic_id',
        'rental_end_date'
    ];

    protected $dates = [
        'rental_end_date',
    ];

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }
}