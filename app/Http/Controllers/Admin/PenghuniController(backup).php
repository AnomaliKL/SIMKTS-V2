<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenghuniController extends Controller
{
    /**
     * Helper privat untuk membersihkan & merakit nomor HP ke format 62
     */
    private function formatPhoneNumber($rawHp)
    {
        if (!$rawHp) return null;

        // Bersihkan karakter selain angka
        $cleanHp = preg_replace('/\D/', '', $rawHp);

        // Jika diawali '0', potong '0'-nya
        if (str_starts_with($cleanHp, '0')) {
            $cleanHp = substr($cleanHp, 1);
        }

        // Tambahkan kode negara '62' di depan jika belum ada
        if (!str_starts_with($cleanHp, '62')) {
            $cleanHp = '62' . $cleanHp;
        }

        return $cleanHp;
    }

    /**
     * Menampilkan Data Penghuni
     */
    public function index()
    {
        $penghunis = Penghuni::with('kamar')->where('status_huni', 'Aktif')->get();
        $kamar_kosong = Kamar::where('status_kamar', 'Kosong')->get();
        $pengunjung = User::where('role', 'pengunjung')->get();

        return view('admin.data_penghuni', compact(
            'penghunis',
            'kamar_kosong',
            'pengunjung'
        ));
    }

    /**
     * Simpan Penghuni Baru
     */
    public function store(Request $request)
    {
        // Sanitasi input nomor HP sebelum validasi
        $rawHp = $request->input('hp') ?: $request->input('add_phone_input');
        if ($rawHp) {
            $request->merge(['hp' => $this->formatPhoneNumber($rawHp)]);
        }

        $rules = [
            'jenis_user' => 'required|in:baru,lama',
            'nik'        => 'required|numeric|unique:penghunis,nik_ktp',
            'hp'         => 'required|numeric|digits_between:9,15',
            'id_kamar'   => 'required|exists:kamars,id_kamar',
            'tgl_masuk'  => 'required|date',
        ];

        if ($request->jenis_user === 'baru') {
            $rules['nama']  = 'required|string|max:100';
            $rules['email'] = 'required|email|unique:users,email';
        } else {
            $rules['id_user'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules, [
            'hp.required'       => 'Nomor WhatsApp wajib diisi.',
            'hp.digits_between' => 'Format nomor WhatsApp tidak valid.',
            'nik.unique'        => 'NIK KTP sudah terdaftar.',
            'email.unique'      => 'Email sudah terdaftar.',
        ]);

        DB::beginTransaction();

        try {
            // 1. Tentukan / Update User
            if ($request->jenis_user === 'baru') {
                $user = User::create([
                    'name'     => $validated['nama'],
                    'email'    => $validated['email'],
                    'no_hp'    => $validated['hp'],
                    'password' => Hash::make('123456'), // Password default
                    'role'     => 'penghuni',
                ]);
                $userId    = $user->id;
                $userNama  = $user->name;
                $userEmail = $user->email;
            } else {
                $userId = $validated['id_user'];
                $user   = User::findOrFail($userId);
                
                // Naikkan role pengunjung lama menjadi penghuni & update No. HP
                $user->update([
                    'role'  => 'penghuni',
                    'no_hp' => $validated['hp'],
                ]);

                $userNama  = $user->name;
                $userEmail = $user->email;
            }

            // 2. Buat Record Penghuni
            Penghuni::create([
                'id_user'      => $userId,
                'id_kamar'     => $validated['id_kamar'],
                'nama_lengkap' => $userNama,
                'nik_ktp'      => $validated['nik'],
                'no_hp'        => $validated['hp'],
                'email'        => $userEmail,
                'tgl_masuk'    => $validated['tgl_masuk'],
                'status_huni'  => 'Aktif',
            ]);

            // 3. Ubah Status Kamar menjadi Terisi
            Kamar::where('id_kamar', $validated['id_kamar'])->update([
                'status_kamar' => 'Terisi',
            ]);

            DB::commit();

            return redirect()
                ->route('admin.penghuni.index')
                ->with('success', 'Penghuni berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan penghuni: ' . $e->getMessage());
        }
    }

    /**
     * Update Penghuni
     */
    public function update(Request $request, $id)
    {
        // Sanitasi input nomor HP sebelum validasi
        $rawHp = $request->input('hp') ?: $request->input('edit_phone_input');
        if ($rawHp) {
            $request->merge(['hp' => $this->formatPhoneNumber($rawHp)]);
        }

        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'nik'       => 'required|numeric',
            'hp'        => 'required|numeric|digits_between:9,15',
            'id_kamar'  => 'required|exists:kamars,id_kamar',
            'tgl_masuk' => 'required|date',
        ], [
            'hp.required'       => 'Nomor WhatsApp wajib diisi.',
            'hp.digits_between' => 'Format nomor WhatsApp tidak valid.',
            'nik.numeric'       => 'NIK KTP harus berupa angka.',
        ]);

        DB::beginTransaction();

        try {
            $penghuni = Penghuni::findOrFail($id);

            // Update data user induk (Nama dan No HP)
            User::where('id', $penghuni->id_user)->update([
                'name'  => $validated['nama'],
                'no_hp' => $validated['hp'],
            ]);

            // Jika ada perubahan kamar (Mutasi Kamar)
            if ($penghuni->id_kamar != $validated['id_kamar']) {
                // Bebaskan kamar lama
                Kamar::where('id_kamar', $penghuni->id_kamar)->update([
                    'status_kamar' => 'Kosong',
                ]);

                // Isi kamar baru
                Kamar::where('id_kamar', $validated['id_kamar'])->update([
                    'status_kamar' => 'Terisi',
                ]);

                $penghuni->id_kamar  = $validated['id_kamar'];
                $penghuni->tgl_masuk = $validated['tgl_masuk'];
            }

            // Update data record penghuni
            $penghuni->nama_lengkap = $validated['nama'];
            $penghuni->nik_ktp      = $validated['nik'];
            $penghuni->no_hp        = $validated['hp'];
            $penghuni->save();

            DB::commit();

            return redirect()
                ->route('admin.penghuni.index')
                ->with('success', 'Data penghuni berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
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

            // Update status hunian
            $penghuni->update([
                'status_huni' => 'Keluar',
                'tgl_keluar'  => now(),
            ]);

            // Kembalikan status kamar menjadi Kosong
            Kamar::where('id_kamar', $penghuni->id_kamar)->update([
                'status_kamar' => 'Kosong',
            ]);

            // Turunkan role akun user kembali menjadi Pengunjung
            User::where('id', $penghuni->id_user)->update([
                'role' => 'pengunjung',
            ]);

            DB::commit();

            return redirect()
                ->route('admin.penghuni.index')
                ->with('success', 'Penghuni berhasil checkout.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melakukan checkout: ' . $e->getMessage());
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

            // Blokir penghapusan jika status masih Aktif
            if (strtolower($penghuni->status_huni) === 'aktif') {
                return redirect()
                    ->route('admin.penghuni.index')
                    ->with('error', 'Penghuni masih aktif. Silakan checkout terlebih dahulu.');
            }

            $penghuni->delete();

            DB::commit();

            return redirect()
                ->route('admin.penghuni.index')
                ->with('success', 'Data penghuni berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.penghuni.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}