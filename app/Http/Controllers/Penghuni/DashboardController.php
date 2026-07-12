<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Penghuni;
use App\Models\Tagihan;
use App\Models\Bank;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $penghuni = Penghuni::with('kamar')->where('id_user', $user->id)->first();

        if (!$penghuni) {

            return redirect()
                ->route('home')
                ->with('error','Data penghuni belum tersedia.');

        }

        $current_tagihan = Tagihan::where('id_penghuni', $penghuni->id_penghuni)
            ->where('status','!=','lunas')
            ->latest()
            ->first();

        $riwayats = Tagihan::where('id_penghuni', $penghuni->id_penghuni)
            ->latest()
            ->get();

        $bank = Bank::first();

        $wa_admin = "6281234567890";

        return view('penghuni.dashboard', compact(

            'penghuni',
            'current_tagihan',
            'riwayats',
            'bank',
            'wa_admin'

        ));
    }
}