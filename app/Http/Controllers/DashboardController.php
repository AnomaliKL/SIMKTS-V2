<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalKamar'      => 0,
            'kamarKosong'     => 0,
            'kamarIsi'        => 0,
            'totalPenghuni'   => 0,
            'penghuniTerbaru' => collect(),
        ]);
    }
}