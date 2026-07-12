<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {

            $table->id('id_booking');

            $table->foreignId('id_user')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('id_kamar')
                  ->constrained('kamars', 'id_kamar')
                  ->cascadeOnDelete();

            $table->date('tgl_booking');
            $table->date('tgl_masuk');
            $table->integer('lama_sewa');
            $table->string('nik_ktp', 20);
            $table->enum('status',[
                'pending',
                'diterima',
                'ditolak'
            ])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};