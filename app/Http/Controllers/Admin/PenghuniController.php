<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use App\Models\Booking;
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

        // Mengambil akun pengunjung yang belum menjadi penghuni aktif
        $pengunjung = User::where('role', 'pengunjung')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('penghunis')
                    ->whereRaw('penghunis.id_user = users.id');
            })->get();

        return view('admin.data_penghuni', compact(
            'penghunis',
            'kamar_kosong',
            'pengunjung' // <- Ditambahkan ke compact agar dibaca oleh View
        ));
    }

    /**
     * Simpan Penghuni Baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input dasar
        $rules = [
            'jenis_user' => 'required|in:baru,lama',
            'nik' => 'required|unique:penghunis,nik_ktp|digits_between:16,20',
            'hp' => 'required|max:20',
            'id_kamar' => 'required|exists:kamars,id_kamar',
            'tgl_masuk' => 'required|date|after_or_equal:today',
        ];

        // Validasi bersyarat: Nama & Email hanya wajib jika membuat akun baru
        if ($request->jenis_user === 'baru') {
            $rules['nama'] = 'required|string|max:100';
            $rules['email'] = 'required|email|unique:users,email';
        } else {
            $rules['id_user'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            if ($request->jenis_user === 'baru') {
                // JALUR USER BARU
                $user = User::create([
                    'name' => $request->nama,
                    'email' => $request->email,
                    'no_hp' => $request->hp,
                    'password' => Hash::make('123456'),
                    'role' => 'penghuni',
                ]);
                $namaPenghuni = $request->nama;
                $emailPenghuni = $request->email;
            } else {
                // JALUR USER LAMA: Ambil nama asli langsung dari tabel users
                $user = User::findOrFail($request->id_user);
                $user->update([
                    'role' => 'penghuni',
                    'no_hp' => $request->hp,
                ]);
                $namaPenghuni = $user->name; // <- Kunci nama dari database tabel users
                $emailPenghuni = $user->email;
            }

            // 2. Buat data baru pada rekaman tabel Penghuni
            $penghuni = new Penghuni;
            $penghuni->id_user = $user->id;
            $penghuni->id_kamar = $request->id_kamar;
            $penghuni->nama_lengkap = $namaPenghuni; // <- Menggunakan nama terverifikasi
            $penghuni->nik_ktp = $request->nik;
            $penghuni->no_hp = $request->hp;
            $penghuni->email = $emailPenghuni;
            $penghuni->tgl_masuk = $request->tgl_masuk;
            $penghuni->status_huni = 'Aktif';
            $penghuni->save();

            // 3. Ubah status hunian kamar kost
            Kamar::where('id_kamar', $request->id_kamar)->update([
                'status_kamar' => 'Terisi',
            ]);

            DB::commit();

            return redirect()
                ->route('admin.penghuni.index')
                ->with('success', 'Penghuni berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Update Penghuni
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => 'required|digits_between:16,20',
            'hp' => 'required|max:20',
            'id_kamar' => 'required|exists:kamars,id_kamar',
            'tgl_masuk' => 'required|date|after_or_equal:today',
        ]);

        DB::beginTransaction();

        try {
            $penghuni = Penghuni::findOrFail($id);

            // Update data user utama agar sinkron
            User::where('id', $penghuni->id_user)->update([
                'name' => $request->nama,
                'no_hp' => $request->hp,
            ]);

            // Logika jika memutuskan pindah alokasi kamar kost
            if ($penghuni->id_kamar != $request->id_kamar) {
                // Kosongkan kamar lama
                Kamar::where('id_kamar', $penghuni->id_kamar)->update([
                    'status_kamar' => 'Kosong',
                ]);

                // Isi kamar baru
                Kamar::where('id_kamar', $request->id_kamar)->update([
                    'status_kamar' => 'Terisi',
                ]);

                $penghuni->id_kamar = $request->id_kamar;
                $penghuni->tgl_masuk = $request->tgl_masuk;
            }

            // Simpan perubahan biodata penghuni
            $penghuni->nama_lengkap = $request->nama;
            $penghuni->nik_ktp = $request->nik;
            $penghuni->no_hp = $request->hp;
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
                'status_kamar' => 'Kosong',
            ]);

            User::where('id', $penghuni->id_user)->update([
                'role' => 'pengunjung',
            ]);

            DB::commit();

            Booking::where('id_user', $penghuni->id_user)->delete();

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
