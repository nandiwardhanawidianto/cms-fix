<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongList extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug_list_id',
        'song_id',
    ];

    public function slugList()
    {
        return $this->belongsTo(SlugList::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
