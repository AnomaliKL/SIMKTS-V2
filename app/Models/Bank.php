<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';

    protected $primaryKey = 'id_bank';

    protected $fillable = [

        'nama_bank',
        'no_rekening',
        'atas_nama',
        'smtp_email', 
        'smtp_app_password'

    ];
}