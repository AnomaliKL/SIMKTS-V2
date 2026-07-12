<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Daftar booking milik pengunjung
     */
    public function index()
    {
        $bookings = Booking::with('kamar')
                    ->where('id_user', Auth::id())
                    ->latest()
                    ->get();

        return view('pengunjung.booking', compact('bookings'));
    }

    /**
     * Simpan booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kamar'  => 'required|exists:kamars,id_kamar',
            'tgl_masuk' => 'required|date|after_or_equal:today',
            'lama_sewa' => 'required|integer|min:1|max:60',
            'nik_ktp'   => 'required|string|min:16',
        ]);

        // Cek apakah user sudah punya booking pending atau diterima
        $cekBooking = Booking::where('id_user', auth()->id())
                            ->whereIn('status', ['pending', 'diterima'])
                            ->exists();

        if ($cekBooking) {

            return redirect()
                ->route('home')
                ->with('warning', 'Anda sudah memiliki pengajuan booking.');

            }

        Booking::create([

            'id_user'      => Auth::id(),
            'id_kamar'     => $request->id_kamar,
            'tgl_booking'  => now(),
            'tgl_masuk'    => $request->tgl_masuk,
            'lama_sewa'    => $request->lama_sewa,
            'nik_ktp'      => $request->nik_ktp,
            'status'       => 'pending',

        ]);

        return redirect()
            ->route('home')
            ->with('booking_success', 'Pengajuan Booking Berhasil! Tunggu konfirmasi Admin.');

    }
}