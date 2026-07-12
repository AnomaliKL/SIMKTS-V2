<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihans', function (Blueprint $table) {

            $table->id('id_tagihan');
            
            $table->foreignId('id_penghuni')
                ->constrained('penghunis', 'id_penghuni')
                ->cascadeOnDelete();

            $table->date('bulan_tagihan');
            $table->date('tgl_jatuh_tempo');
            $table->decimal('jumlah_tagihan',12,2);
            $table->enum('status',[
                'belum_bayar',
                'menunggu_validasi',
                'lunas'
            ])->default('belum_bayar');

            $table->string('bukti_bayar')->nullable();
            $table->timestamp('tgl_bayar')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};