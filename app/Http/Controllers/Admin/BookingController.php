<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Menampilkan seluruh booking yang masih pending
     */
    public function index()
    {
        $bookings = Booking::join(
                'kamars',
                'bookings.id_kamar',
                '=',
                'kamars.id_kamar'
            )
            ->select(
                'bookings.*',
                'kamars.no_kamar as nomor_kamar'
            )
            ->where('status_booking','Pending')
            ->orderBy('tgl_pengajuan')
            ->get();

        return view('admin.data_booking', compact('bookings'));
    }

    /**
     * Approve Booking
     */
    public function accept($id)
    {

    }

    /**
     * Reject Booking
     */
    public function reject($id)
    {

    }

    /**
     * Hapus Booking
     */
    public function destroy($id)
    {

    }
}