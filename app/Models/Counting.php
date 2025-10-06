<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counting extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug_list_id',
        'nama_surat',
        'surat_arab',
        'deskripsi_surat',
    ];

    public function slug()
    {
        return $this->belongsTo(SlugList::class, 'slug_list_id');
    }
}
