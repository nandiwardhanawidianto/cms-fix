<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lovegift extends Model
{
    use HasFactory;

    protected $table = 'lovegifts';

    protected $fillable = [
        'slug_list_id',
        'bank_id',
        'no_rekening',
        'pemilik_bank',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
