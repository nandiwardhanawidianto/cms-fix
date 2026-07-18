<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class love_story extends Model
{
    protected $table = 'love_storys';

    protected $fillable = [
        'slug_list_id',
        'judul_awal_pertemuan',
        'judul_menjalin_hubungan',
        'judul_lamaran',
        'awal_pertemuan',
        'menjalin_hubungan',
        'lamaran',
        'gambar_awal',
        'gambar_hubungan',
        'gambar_lamaran',
    ];

}
