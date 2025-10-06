<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugList extends Model
{
    protected $table = 'slug_lists';

    protected $fillable = [
        'nama',
        'slug',
        'keterangan',
        'hosting_at'
    ];

    // generate slug otomatis (dan pastikan unik) saat creating/updating
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::createUniqueSlug($model->nama);
            }
        });

        static::updating(function ($model) {
            // kalau nama berubah dan slug kosong, regenerate unik
            if ($model->isDirty('nama') && empty($model->slug)) {
                $model->slug = static::createUniqueSlug($model->nama, $model->id);
            }
            // kalau slug diisi manual, biarkan — validasi uniqueness di controller nanti
        });
    }

    protected static function createUniqueSlug(string $name, $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        // tambahkan suffix -1, -2, ... kalau slug sudah dipakai
        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
