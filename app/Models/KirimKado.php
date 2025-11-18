<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KirimKado extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug_list_id',
        'nama_penerima',
        'no_hp_penerima',
        'alamat_penerima',
    ];

    public function slug()
    {
        return $this->belongsTo(SlugList::class, 'slug_list_id');
    }
}
