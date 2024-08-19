<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComicNote extends Model
{
    use HasFactory;

    // Specify the table if not using Laravel's naming convention
    protected $table = 'comic_notes';

    // Define the fillable attributes
    protected $fillable = [
        'comic_id',
        'note',
    ];

    // Define the relationships

    /**
     * Get the comic associated with this note.
     */
    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }
}