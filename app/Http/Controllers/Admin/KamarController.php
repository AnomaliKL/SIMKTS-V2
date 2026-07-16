<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KamarController extends Controller
{
    public function index()
    {
        $kamars = Kamar::orderBy('no_kamar', 'asc')->get();

    // Mengambil nomor kamar paling terakhir di database
    $kamarTerakhir = Kamar::orderBy('id_kamar', 'desc')->first();

    if ($kamarTerakhir) {
        // Mengambil angka saja dari format "KM-052" -> 52
        $angkaTerakhir = (int) filter_var($kamarTerakhir->no_kamar, FILTER_SANITIZE_NUMBER_INT);
        $nomorBerikutnya = $angkaTerakhir + 1;
    } else {
        $nomorBerikutnya = 1;
    }

    // Mengubah angka 1 menjadi "KM-001", angka 52 menjadi "KM-052"
    $next_no_kamar = 'KM-' . str_pad($nomorBerikutnya, 3, '0', STR_PAD_LEFT);

    return view('admin.data_kamar', compact('kamars', 'next_no_kamar'));
    }

    public function store(Request $request)
    {
        $rules = [
            'no_kamar'   => 'required|unique:kamars,no_kamar',
            'deskripsi'  => 'nullable',
            'harga_sewa' => 'required|numeric|min:0', // <-- Min 0 agar tidak minus
            'foto'       => 'nullable|image|mimes:jpg,jpeg,png'
        ];

        $messages = [
            'no_kamar.unique' => 'Nomor Kamar sudah digunakan.',
            'harga_sewa.min'  => 'Harga sewa tidak boleh bernilai minus.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        try {
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $foto = null;
            if($request->hasFile('foto')){
                $foto = $request->file('foto')->store('kamar','public');
            }

            Kamar::create([
                'no_kamar'     => $request->no_kamar,
                'deskripsi'    => $request->deskripsi,
                'harga_sewa'   => $request->harga_sewa,
                'status_kamar' => 'Kosong',
                'foto_kamar'   => $foto
            ]);

            return back()->with('success','Kamar berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Mengubah parameter $id untuk mencocokkan Primary Key asli
    public function update(Request $request, $id)
    {
        try {
            $kamar = Kamar::findOrFail($id);

            $rules = [
                'no_kamar'   => 'required|unique:kamars,no_kamar,'.$kamar->id_kamar.',id_kamar',
                'deskripsi'  => 'nullable',
                'harga_sewa' => 'required|numeric|min:0', // <-- Min 0 agar tidak minus
                'foto'       => 'nullable|image'
            ];

            $messages = [
                'no_kamar.unique' => 'Nomor Kamar sudah digunakan.',
                'harga_sewa.min'  => 'Harga sewa tidak boleh bernilai minus.'
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $foto = $request->foto_lama;
            if($request->hasFile('foto')){
                if($foto){
                    Storage::disk('public')->delete($foto);
                }
                $foto = $request->file('foto')->store('kamar','public');
            }

            $kamar->update([
                'no_kamar'   => $request->no_kamar,
                'deskripsi'  => $request->deskripsi,
                'harga_sewa' => $request->harga_sewa,
                'foto_kamar' => $foto
            ]);

            return back()->with('success','Kamar berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Mengubah pencarian data berdasarkan $id int konvensional agar form submit Route terbaca
    public function destroy($id)
    {
        try {
            // Menggunakan findOrFail jika binding model di route custom/berbeda
            $kamar = Kamar::findOrFail($id);

            // Proteksi: Mencegah kamar terhapus jika status sedang Terisi
            if (strtolower($kamar->status_kamar) === 'terisi') {
                throw new \Exception('Kamar tidak dapat dihapus karena masih digunakan oleh penghuni aktif.');
            }

            if($kamar->foto_kamar){
                Storage::disk('public')->delete($kamar->foto_kamar);
            }

            $kamar->delete();
            return back()->with('success','Kamar berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }   
}