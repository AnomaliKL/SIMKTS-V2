<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $primaryKey = 'id_booking';

    protected $fillable = [

        'id_user',
        'id_kamar',
        'nama_lengkap',
        'nik_ktp',
        'no_hp',
        'email',
        'tgl_pengajuan',
        'tgl_mulai_kos',
        'status_booking',
        'catatan'

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
}