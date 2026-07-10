<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KamarController extends Controller
{
    /**
     * Menampilkan List Halaman Utama Data Kamar
     */
    public function index()
    {
        // Mengambil semua data kamar diurutkan berdasarkan nomor kamar terkecil
        $kamars = Kamar::orderBy('no_kamar', 'asc')->get();

        return view('admin.data_kamar', compact('kamars'));
    }

    /**
     * Aksi ROUTE: admin.kamar.store (POST)
     * Memproses Input Kamar Baru dari Modal Tambah
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Form
        $request->validate([
            'no_kamar' => 'required|string|max:10|unique:kamar,no_kamar',
            'harga_sewa' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Batas Maks 2MB
        ]);

        // 2. Inisialisasi Variabel Path File Foto
        $pathFoto = null;

        // 3. Proses Upload File Jika Admin Mengunggah Foto Kamar
        if ($request->hasFile('foto')) {
            // File disimpan ke folder 'storage/app/public/kamar'
            $pathFoto = $request->file('foto')->store('kamar', 'public');
        }

        // 4. Eksekusi Simpan Data ke Database menggunakan Model Eloquent
        Kamar::create([
            'no_kamar' => $request->no_kamar,
            'harga_sewa' => $request->harga_sewa,
            'deskripsi' => $request->deskripsi,
            'foto_kamar' => $pathFoto,
            'status_kamar' => 'kosong', // Status default saat kamar baru didaftarkan
        ]);

        // 5. Kembalikan Halaman dengan Flash Message Sukses untuk SweetAlert2
        return redirect()->route('admin.kamar.index')->with('success', 'Unit Kamar Baru Berhasil Ditambahkan.');
    }

    /**
     * Aksi ROUTE: admin.kamar.update (PUT/PATCH)
     * Memproses Perubahan Data Kamar dari Modal Edit
     */
    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $request->validate([
            'no_kamar' => 'required|string|max:10|unique:kamar,no_kamar,'.$id.',id_kamar',
            'harga_sewa' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pathFoto = $request->foto_lama;

        // Jika ada unggahan file foto baru
        if ($request->hasFile('foto')) {
            // Hapus berkas foto lama dari sistem storage jika ada berkas sebelumnya
            if ($request->foto_lama && Storage::disk('public')->exists($request->foto_lama)) {
                Storage::disk('public')->delete($request->foto_lama);
            }
            // Simpan berkas foto yang baru
            $pathFoto = $request->file('foto')->store('kamar', 'public');
        }

        $kamar->update([
            'no_kamar' => $request->no_kamar,
            'harga_sewa' => $request->harga_sewa,
            'deskripsi' => $request->deskripsi,
            'foto_kamar' => $pathFoto,
        ]);

        return redirect()->route('admin.kamar.index')->with('success', 'Rekam Data Kamar Berhasil Diperbarui.');
    }

    /**
     * Aksi ROUTE: admin.kamar.destroy (DELETE)
     * Menghapus Kamar Sekaligus Berkas Fotonya
     */
    public function destroy($id)
    {
        $kamar = Kamar::findOrFail($id);

        // Hapus file fisik gambar dari penyimpanan lokal disk agar tidak memenuhi server
        if ($kamar->foto_kamar && Storage::disk('public')->exists($kamar->foto_kamar)) {
            Storage::disk('public')->delete($kamar->foto_kamar);
        }

        $kamar->delete();

        return redirect()->route('admin.kamar.index')->with('success', 'Data Kamar Berhasil Dihapus Permanen.');
    }
}
