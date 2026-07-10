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

            $table->unsignedBigInteger('id_user');

            $table->unsignedBigInteger('id_kamar');

            $table->string('nama_lengkap');

            $table->string('nik_ktp',20);

            $table->string('no_hp',20);

            $table->string('email');

            $table->dateTime('tgl_pengajuan');

            $table->date('tgl_mulai_kos');

            $table->enum('status_booking',[
                'Pending',
                'Diterima',
                'Ditolak'
            ])->default('Pending');

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('id_kamar')
                ->references('id_kamar')
                ->on('kamars')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};