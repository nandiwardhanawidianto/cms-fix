<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug_id',
        'nama_panggilan_pria',
        'nama_lengkap_pria',
        'foto_pria',
        'orangtua_pria',
        'nama_panggilan_wanita',
        'nama_lengkap_wanita',
        'foto_wanita',
        'orangtua_wanita',
    ];

    public function slug()
    {
        return $this->belongsTo(Slug::class);
    }
}
