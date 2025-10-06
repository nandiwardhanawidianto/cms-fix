<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SlugList;

class GuestMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug_list_id' => SlugList::inRandomOrder()->value('id') ?? 1,
            'name' => $this->faker->name(),
            'attendance' => $this->faker->randomElement(['Hadir', 'Tidak Hadir', 'Belum Pasti']),
            'message' => $this->faker->sentence(12),
        ];
    }
}
