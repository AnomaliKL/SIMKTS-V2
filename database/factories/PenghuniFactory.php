<?php

namespace Database\Factories;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenghuniFactory extends Factory
{
    protected $model = Penghuni::class;

    public function definition(): array
    {
        return [
            'id_user' => User::factory(), // Otomatis membuat user bertipe role penghuni
            'id_kamar' => Kamar::factory(),
            'nama_lengkap' => fake()->name(),
            'nik_ktp' => fake()->unique()->numerify('################'),
            'no_hp' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'tgl_masuk' => fake()->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
            'tgl_keluar' => null,
            'status_huni' => 'Aktif',
        ];
    }
}
