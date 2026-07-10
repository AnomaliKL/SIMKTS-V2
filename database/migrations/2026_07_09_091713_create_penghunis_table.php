<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penghunis', function (Blueprint $table) {

            $table->id('id_penghuni');

            // Relasi ke user
            $table->foreignId('id_user')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();

            // Relasi ke kamar
            $table->foreignId('id_kamar')->constrained('kamars', 'id_kamar')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('nama_lengkap');
            $table->string('nik_ktp', 20)->unique();
            $table->string('no_hp', 20);
            $table->string('email')->unique();

            $table->date('tgl_masuk');
            $table->date('tgl_keluar')->nullable();

            $table->enum('status_huni', [
                'Aktif',
                'Keluar'
            ])->default('Aktif');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghunis');
    }
};