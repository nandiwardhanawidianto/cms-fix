<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acara extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug_list_id',
        'nama_acara',
        'tanggal_acara',
        'pukul_acara',
        'alamat_acara',
        'link_acara',
    ];

    public function slug()
    {
        return $this->belongsTo(SlugList::class, 'slug_list_id');
    }
}
