<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass Assignment
     */
    protected $fillable = [

        'name',
        'email',
        'no_hp',
        'password',
        'role',
        'foto',

    ];

    /**
     * Hidden Attribute
     */
    protected $hidden = [

        'password',
        'remember_token',

    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [

            'password' => 'hashed',

        ];
    }

    // Relasi
    public function penghuni()
    {
        return $this->hasOne(
            Penghuni::class,
            'id_user',
            'id'
        );
    }

    public function bookings()
    {
        return $this->hasMany(
            Booking::class,
            'id_user',
            'id'
        );
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPengunjung()
    {
        return $this->role === 'pengunjung';
    }

    public function isPenghuni()
    {
        return $this->role === 'penghuni';
    }
}