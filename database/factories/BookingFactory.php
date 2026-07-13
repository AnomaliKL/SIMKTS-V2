<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Kamar;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'id_user' => User::factory(),
            'id_kamar' => Kamar::factory(),
            'tgl_booking' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'tgl_masuk' => fake()->dateTimeBetween('now', '+1 months')->format('Y-m-d'),
            'lama_sewa' => fake()->numberBetween(1, 12),
            'nik_ktp' => fake()->numerify('################'),
            'status' => fake()->randomElement(['pending', 'diterima', 'ditolak']),
            'catatan' => fake()->sentence(),
        ];
    }
}
