<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $table = 'penghunis';

    protected $primaryKey = 'id_penghuni';

    protected $fillable = [

        'id_user',
        'id_kamar',
        'nama_lengkap',
        'nik_ktp',
        'no_hp',
        'email',
        'tgl_masuk',
        'tgl_keluar',
        'status_huni'

    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id'
        );
    }

    public function kamar()
    {
        return $this->belongsTo(
            Kamar::class,
            'id_kamar',
            'id_kamar'
        );
    }

    public function tagihans()
    {
        return $this->hasMany(
            Tagihan::class,
            'id_penghuni',
            'id_penghuni'
        );
    }
}