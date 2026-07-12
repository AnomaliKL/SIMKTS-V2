<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Penghuni;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'kamar'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.data_booking', compact('bookings'));
    }

    public function approve($id)
    {
        DB::beginTransaction();

        try {

            $booking = Booking::with(['user', 'kamar'])->findOrFail($id);

            // 1. Update status booking
            $booking->update([
                'status' => 'diterima'
            ]);

            // 2. Ubah role menjadi penghuni
            $booking->user->update([
                'role' => 'penghuni'
            ]);

            // 3. Ubah status kamar
            $booking->kamar->update([
                'status_kamar' => 'Terisi'
            ]);

            // 4. Tambahkan ke tabel penghuni
            $penghuni = Penghuni::create([

                'id_user'       => $booking->user->id,
                'id_kamar'      => $booking->id_kamar,
                'nama_lengkap'  => $booking->user->name,
                'nik_ktp'       => $booking->nik_ktp,
                'no_hp'         => $booking->user->no_hp,
                'email'         => $booking->user->email,
                'tgl_masuk'     => $booking->tgl_masuk,
                'status_huni'   => 'Aktif',

            ]);

            Tagihan::create([

                'id_penghuni'      => $penghuni->id_penghuni,
                'bulan_tagihan'    => now()->startOfMonth(),
                'tgl_jatuh_tempo'  => now()->addDays(7),
                'jumlah_tagihan'   => $booking->kamar->harga_sewa,
                'status'           => 'belum_bayar'

            ]);

            DB::commit();

            return redirect()
                ->route('admin.booking.index')
                ->with('success', 'Booking berhasil diterima.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());

        }
    }

    public function reject($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'ditolak'
        ]);

        return redirect()
            ->route('admin.booking.index')
            ->with('success', 'Booking berhasil ditolak.');
    }
}