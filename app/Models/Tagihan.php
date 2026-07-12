<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihans';

    protected $primaryKey = 'id_tagihan';

    protected $fillable = [

        'id_penghuni',
        'bulan_tagihan',
        'tgl_jatuh_tempo',
        'jumlah_tagihan',
        'status',
        'bukti_bayar',
        'tgl_bayar'

    ];

    public function penghuni()
    {
        return $this->belongsTo(
            Penghuni::class,
            'id_penghuni',
            'id_penghuni'
        );
    }
}