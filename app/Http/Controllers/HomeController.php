<?php

namespace App\Http\Controllers;

use App\Models\Kamar;

class HomeController extends Controller
{
    /**
     * Landing Page
     */
    public function index()
    {
        $kamars = Kamar::where('status_kamar', 'Kosong')
                    ->orderBy('no_kamar')
                    ->get();

        return view('home', compact('kamars'));
    }

    /**
     * Detail Kamar
     */
    public function detail($id)
    {
        $kamar = Kamar::findOrFail($id);

        return view('detail_kamar', compact('kamar'));
    }
}