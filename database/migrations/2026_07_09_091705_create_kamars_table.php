<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamars', function (Blueprint $table) {

            $table->id('id_kamar');
            $table->string('no_kamar')->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_sewa',12,0);
            $table->enum('status_kamar',[
                'Kosong',
                'Terisi'
            ])->default('Kosong');

            $table->string('foto_kamar')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};