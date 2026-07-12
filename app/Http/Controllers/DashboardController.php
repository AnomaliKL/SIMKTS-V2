<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard', [
            'totalKamar'      => Kamar::count(),
            'kamarKosong'     => Kamar::where('status_kamar', 'Kosong')->count(),
            'kamarIsi'        => Kamar::where('status_kamar', 'Terisi')->count(),
            'totalPenghuni'   => Penghuni::count(),
            'penghuniTerbaru' => Penghuni::latest()->take(5)->get(),
        ]);
    }


    public function penghuni()
    {
        return view('penghuni.dashboard');
    }
}