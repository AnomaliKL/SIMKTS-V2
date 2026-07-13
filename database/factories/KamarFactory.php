<?php

namespace Database\Factories;

use App\Models\Kamar;
use Illuminate\Database\Eloquent\Factories\Factory;

class KamarFactory extends Factory
{
    protected $model = Kamar::class;

    public function definition(): array
    {
        static $number = 1;

        return [
            'no_kamar' => 'K-'.str_pad($number++, 3, '0', STR_PAD_LEFT),
            'deskripsi' => fake()->paragraph(1),
            'harga_sewa' => fake()->randomElement([500000, 650000, 800000, 1000000]),
            'status_kamar' => fake()->randomElement(['Kosong', 'Terisi']),
            'foto_kamar' => null,
        ];
    }
}
