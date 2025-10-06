<?php

// app/Models/Bank.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['nama_bank', 'logo_bank'];

    // Relasi ke Lovegift
    public function lovegifts()
    {
        return $this->hasMany(Lovegift::class, 'bank_id');
    }
}
