<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $table = 'kamars';

    protected $primaryKey = 'id_kamar';

    protected $fillable = [

        'no_kamar',
        'deskripsi',
        'harga_sewa',
        'status_kamar',
        'foto_kamar'

    ];

    public function penghunis()
    {
        return $this->hasMany(
            Penghuni::class,
            'id_kamar',
            'id_kamar'
        );
    }

    public function bookings()
    {
        return $this->hasMany(
            Booking::class,
            'id_kamar',
            'id_kamar'
        );
    }
}