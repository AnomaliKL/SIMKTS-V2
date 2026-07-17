<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);

        $userIdsPengunjung = [];
        $userIdsPenghuni = [];

        // Buat 50 User Pengunjung
        for ($i = 1; $i <= 50; $i++) {
            $userIdsPengunjung[] = DB::table('users')->insertGetId([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'no_hp' => fake()->phoneNumber(),
                'password' => Hash::make('password'),
                'role' => 'pengunjung',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat 50 User Penghuni
        for ($i = 1; $i <= 50; $i++) {
            $userIdsPenghuni[] = DB::table('users')->insertGetId([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'no_hp' => fake()->phoneNumber(),
                'password' => Hash::make('password'),
                'role' => 'penghuni',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------------
        // 2. SEEDER TABEL KAMARS (55 Kamar)
        // -------------------------------------------------------------
        $kamarIds = [];
        $hargaPilihan = [500000, 750000, 1000000, 1200000];
        $deskripsiPilihan = [
            'Fasilitas Lengkap AC, Kasur Springbed, Kamar Mandi Dalam',
            'Kamar Standar Kipas Angin, Kasur Busa, Lemari Pakaian',
            'Kamar Premium Balkon Luar, AC, TV, Kamar Mandi Dalam',
        ];

        for ($i = 1; $i <= 55; $i++) {
            $kamarIds[] = DB::table('kamars')->insertGetId([
                'no_kamar' => 'K-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'deskripsi' => fake()->randomElement($deskripsiPilihan),
                'harga_sewa' => fake()->randomElement($hargaPilihan),
                'status_kamar' => fake()->randomElement(['Kosong', 'Terisi']),
                'foto_kamar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------------
        // 3. SEEDER TABEL PENGHUNIS (50 Penghuni Tetap)
        // -------------------------------------------------------------
        $penghuniIds = [];
        for ($i = 0; $i < 50; $i++) {
            $penghuniIds[] = DB::table('penghunis')->insertGetId([
                'id_user' => $userIdsPenghuni[$i], // Hubungkan ke user role penghuni
                'id_kamar' => fake()->randomElement($kamarIds),
                'nama_lengkap' => fake()->name(),
                'nik_ktp' => fake()->unique()->numerify('################'),
                'no_hp' => fake()->phoneNumber(),
                'email' => fake()->unique()->safeEmail(),
                'tgl_masuk' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'tgl_keluar' => null,
                'status_huni' => fake()->randomElement(['Aktif', 'Keluar']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------------
        // 4. SEEDER TABEL BOOKINGS (50 Data Transaksi)
        // -------------------------------------------------------------
        for ($i = 0; $i < 50; $i++) {
            DB::table('bookings')->insert([
                'id_user' => fake()->randomElement($userIdsPengunjung),
                'id_kamar' => fake()->randomElement($kamarIds),
                'tgl_booking' => fake()->dateTimeBetween('-1 months', 'now')->format('Y-m-d'),
                'tgl_masuk' => fake()->dateTimeBetween('now', '+2 weeks')->format('Y-m-d'),
                'lama_sewa' => fake()->numberBetween(1, 12),
                'nik_ktp' => fake()->numerify('################'),
                'status' => fake()->randomElement(['pending', 'diterima', 'ditolak']),
                'catatan' => fake()->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------------
        // 5. SEEDER TABEL TAGIHANS (50+ Riwayat Pembayaran Bulanan)
        // -------------------------------------------------------------
        foreach ($penghuniIds as $idPenghuni) {
            $statusBayar = fake()->randomElement(['belum_bayar', 'menunggu_validasi', 'lunas']);

            DB::table('tagihans')->insert([
                'id_penghuni' => $idPenghuni,
                'bulan_tagihan' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
                'tgl_jatuh_tempo' => fake()->dateTimeBetween('now', '+7 days')->format('Y-m-d'),
                'jumlah_tagihan' => fake()->randomElement($hargaPilihan),
                'status' => $statusBayar,
                'bukti_bayar' => $statusBayar !== 'belum_bayar' ? 'bukti_transfer_dummy.jpg' : null,
                'tgl_bayar' => $statusBayar !== 'belum_bayar' ? fake()->dateTimeBetween('-3 days', 'now') : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
