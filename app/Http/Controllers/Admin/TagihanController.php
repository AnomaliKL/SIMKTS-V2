<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Penghuni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    /**
     * Menampilkan Data Tagihan
     */
    public function index(Request $request)
    {
        $query = Tagihan::join(
                    'penghunis',
                    'tagihans.id_penghuni',
                    '=',
                    'penghunis.id_penghuni'
                )
                ->join(
                    'kamars',
                    'penghunis.id_kamar',
                    '=',
                    'kamars.id_kamar'
                )
                ->select(
                    'tagihans.*',
                    'penghunis.nama_lengkap',
                    'kamars.no_kamar as nomor_kamar'
                );

        // Filter Status
        if ($request->filled('status')) {
            $query->where('tagihans.status', $request->status);
        }

        // Filter Bulan
        if ($request->filled('bulan')) {
            $query->whereMonth(
                'bulan_tagihan',
                date('m', strtotime($request->bulan))
            );

            $query->whereYear(
                'bulan_tagihan',
                date('Y', strtotime($request->bulan))
            );
        }

        $tagihans = $query
                        ->orderBy('bulan_tagihan', 'desc')
                        ->orderBy('nama_lengkap')
                        ->get();

        return view(
            'admin.data_tagihan',
            compact('tagihans')
        );
    }

    public function generate(Request $request)
    {
        // 1. Cari tahu kapan periode tagihan paling terakhir yang ada di database
        $tagihanTerakhir = Tagihan::orderBy('bulan_tagihan', 'desc')->first();

        if ($tagihanTerakhir) {
            // Jika sudah ada tagihan, maju 1 bulan dari tanggal tagihan terakhir tersebut
            $carbonBulan = \Carbon\Carbon::parse($tagihanTerakhir->bulan_tagihan)->addMonth();
        } else {
            // Jika database masih kosong/bersih, mulai dari bulan berjalan sekarang ini (Juli 2026)
            $carbonBulan = \Carbon\Carbon::now();
        }

        $bulanTagihan = $carbonBulan->startOfMonth()->format('Y-m-d'); // Format ke YYYY-MM-01
        $stringBulanTahun = $carbonBulan->isoFormat('MMMM YYYY'); // Untuk keperluan pesan alert return

        // 2. Ambil semua penghuni yang saat ini sedang menghuni kamar
        $penghuniAktif = Penghuni::join('kamars', 'penghunis.id_kamar', '=', 'kamars.id_kamar')
            ->whereNotNull('penghunis.id_kamar')
            ->select('penghunis.id_penghuni', 'kamars.harga_sewa')
            ->get();

        if ($penghuniAktif->isEmpty()) {
            return back()->with('error', 'Tidak dapat menaikkan periode. Tidak ada kamar yang sedang dihuni saat ini.');
        }

        $jumlahGenerate = 0;

        // 3. Langsung buat tagihan massal untuk periode berikutnya tanpa skip
        foreach ($penghuniAktif as $penghuni) {
            Tagihan::create([
                'id_penghuni'     => $penghuni->id_penghuni,
                'jumlah_tagihan'  => $penghuni->harga_sewa,
                'bulan_tagihan'   => $bulanTagihan,
                'status'          => 'belum_bayar',
                'tgl_jatuh_tempo' => $carbonBulan->copy()->day(10)->format('Y-m-d') // Jatuh tempo otomatis tanggal 10 di bulan tersebut
            ]);
            $jumlahGenerate++;
        }

        return back()->with('success', $jumlahGenerate . ' data tagihan baru berhasil dibuat maju untuk periode ' . $stringBulanTahun . '.');
    }

    public function validatePayment(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->update(['status' => 'lunas']);

        // Kirim Email Sukses
        $emailUser = $tagihan->penghuni->user->email;
        Mail::to($emailUser)->send(new NotifikasiPembayaranMail($tagihan, 'terima'));

        return back()->with('success', 'Pembayaran berhasil divalidasi & email terkirim.');
    }

    public function rejectPayment(Request $request, $id)
    {
       // Validasi agar admin wajib mengisi teks alasannya
        $request->validate([
            'alasan_ditolak' => 'required|string|max:255'
        ]);

        $tagihan = Tagihan::findOrFail($id);
        
        $tagihan->update([
            'status' => 'ditolak',
            'alasan_ditolak' => $request->alasan_ditolak
        ]);

        // Kirim Email Penolakan dengan Alasan
        $emailUser = $tagihan->penghuni->user->email;
        Mail::to($emailUser)->send(new NotifikasiPembayaranMail($tagihan, 'tolak', $request->alasan_ditolak));

        return back()->with('success', 'Bukti pembayaran ditolak & email alasan berhasil terkirim.');
    }
}