<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'file_path'];

    // biar nanti langsung bisa ambil URL publik
    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
    