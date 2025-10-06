<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $fillable = [
        'slug_list_id',
        'carousel_atas',
        'carousel_bawah',
    ];

    // biar otomatis array
    protected $casts = [
        'carousel_atas' => 'array',
        'carousel_bawah' => 'array',
    ];
}
