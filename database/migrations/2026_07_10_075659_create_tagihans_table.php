<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            // Mengubah struktur enum dengan menambahkan opsi ditolak
            $table->enum('status', [
                'belum_bayar',
                'menunggu_validasi',
                'lunas',
                'ditolak'
            ])->default('belum_bayar')->change();
        });
    }

    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->enum('status', [
                'belum_bayar',
                'menunggu_validasi',
                'lunas'
            ])->default('belum_bayar')->change();
        });
    }
};