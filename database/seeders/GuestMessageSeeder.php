<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GuestMessage;
use App\Models\SlugList;

class GuestMessageSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan minimal ada 1 slug_list dulu
        if (SlugList::count() === 0) {
            \App\Models\SlugList::factory()->create([
                'nama' => 'Dummy Undangan',
                'slug' => 'dummy',
                'keterangan' => 'Slug untuk testing ucapan',
            ]);
        }

        GuestMessage::factory(10)->create();
    }
}
