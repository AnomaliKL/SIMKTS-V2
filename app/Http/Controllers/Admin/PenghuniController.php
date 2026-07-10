<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penghuni;
use App\Models\Kamar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenghuniController extends Controller
{
    /**
     * Menampilkan Data Penghuni
     */
    public function index()
    {
        $penghunis = Penghuni::with('kamar')->where('status_huni', 'Aktif')->get();
        $kamar_kosong = Kamar::where('status_kamar', 'Kosong')->get();

        return view('admin.data_penghuni', compact(
            'penghunis',
            'kamar_kosong'
        ));
    }

    /**
     * Simpan Penghuni Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'nik'       => 'required|unique:penghunis,nik_ktp',
            'hp'        => 'required',
            'email'     => 'required|email',
            'id_kamar'  => 'required|exists:kamars,id_kamar',
            'tgl_masuk' => 'required|date',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Kalau belum ada buat user
        if (!$user) {

            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => bcrypt('123456'),
                'role'     => 'penghuni',
            ]);

        } else {

            $user->role = 'penghuni';
            $user->name = $request->nama;
            $user->save();
        }

        $penghuni = new Penghuni();

        $penghuni->id_user = $user->id;
        $penghuni->id_kamar = $request->id_kamar;
        $penghuni->nama_lengkap = $request->nama;
        $penghuni->nik_ktp = $request->nik;
        $penghuni->no_hp = $request->hp;
        $penghuni->email = $request->email;
        $penghuni->tgl_masuk = $request->tgl_masuk;
        $penghuni->status_huni = 'Aktif';

        $penghuni->save();

        Kamar::where('id_kamar', $request->id_kamar)
            ->update([
                'status_kamar' => 'Terisi'
            ]);

        return redirect()
            ->route('admin.penghuni.index')
            ->with('success', 'Penghuni berhasil ditambahkan.');
    }

    /**
     * Update Penghuni
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'nik'       => 'required|digits_between:16,20',
            'hp'        => 'required|max:20',
            'id_kamar'  => 'required|exists:kamars,id_kamar',
            'tgl_masuk' => 'required|date',
        ]);

        DB::beginTransaction();

        try {

            $penghuni = Penghuni::findOrFail($id);

            // Update data user
            User::where('id', $penghuni->id_user)->update([
                'name' => $request->nama
            ]);

            // Jika pindah kamar
            if ($penghuni->id_kamar != $request->id_kamar) {

                // Kosongkan kamar lama
                Kamar::where('id_kamar', $penghuni->id_kamar)->update([
                        'status_kamar' => 'Kosong'
                    ]);

                // Isi kamar baru
                Kamar::where('id_kamar', $request->id_kamar)->update([
                        'status_kamar' => 'Terisi'
                    ]);

                // Update kamar baru
                $penghuni->id_kamar = $request->id_kamar;

                // Update tanggal masuk baru
                $penghuni->tgl_masuk = $request->tgl_masuk;
            }

            // Update data penghuni
            $penghuni->nama_lengkap = $request->nama;
            $penghuni->nik_ktp      = $request->nik;
            $penghuni->no_hp        = $request->hp;

            $penghuni->save();

            DB::commit();

            return redirect()->route('admin.penghuni.index')->with('success', 'Data penghuni berhasil diperbarui.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());

        }
    }

    /**
     * Checkout Penghuni
     */
    public function checkout($id)
    {
        DB::beginTransaction();

        try {

            $penghuni = Penghuni::findOrFail($id);

            $penghuni->update([

                'status_huni' => 'Keluar',
                'tgl_keluar' => now(),

            ]);


            Kamar::where('id_kamar', $penghuni->id_kamar)->update([

                    'status_kamar' => 'Kosong'

                ]);


            User::where('id', $penghuni->id_user)->update([

                    'role' => 'pengunjung'

                ]);

            DB::commit();

            return redirect()->route('admin.penghuni.index')
                ->with('success', 'Penghuni berhasil checkout.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());

        }
    }

    /**
     * Hapus Penghuni
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $penghuni = Penghuni::findOrFail($id);

            // Tidak boleh menghapus penghuni yang masih aktif
            if ($penghuni->status_huni == 'Aktif') {

                return redirect()->route('admin.penghuni.index')
                    ->with('error', 'Penghuni masih aktif. Silakan checkout terlebih dahulu.');

            }

            $penghuni->delete();

            DB::commit();

            return redirect()->route('admin.penghuni.index')->with('success', 'Data penghuni berhasil dihapus.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->route('admin.penghuni.index')->with('error', $e->getMessage());

        }
    }

}