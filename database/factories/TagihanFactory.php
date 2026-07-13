<?php

namespace Database\Factories;

use App\Models\Penghuni;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagihanFactory extends Factory
{
    protected $model = Tagihan::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['belum_bayar', 'menunggu_validasi', 'lunas']);

        return [
            'id_penghuni' => Penghuni::factory(),
            'bulan_tagihan' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'tgl_jatuh_tempo' => fake()->dateTimeBetween('now', '+10 days')->format('Y-m-d'),
            'jumlah_tagihan' => fake()->randomElement([500000, 650000, 800000, 1000000]),
            'status' => $status,
            'bukti_bayar' => $status !== 'belum_bayar' ? 'bukti_tf_dummy.png' : null,
            'tgl_bayar' => $status !== 'belum_bayar' ? fake()->dateTimeBetween('-5 days', 'now') : null,
        ];
    }
}
