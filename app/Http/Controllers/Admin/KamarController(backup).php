<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KamarController extends Controller
{
    public function index()
    {
        $kamars = Kamar::orderBy('no_kamar')->get();
        return view('admin.data_kamar', compact('kamars'));
    }

    public function store(Request $request)
    {
        $request->validate([

            'no_kamar'=>'required|unique:kamars,no_kamar',
            'deskripsi'=>'nullable',
            'harga_sewa'=>'required|numeric',
            'foto'=>'nullable|image|mimes:jpg,jpeg,png|max:2048'

        ]);

        $foto = null;

        if($request->hasFile('foto')){
            $foto = $request->file('foto')->store('kamar','public');

        }

        Kamar::create([

            'no_kamar'=>$request->no_kamar,
            'deskripsi'=>$request->deskripsi,
            'harga_sewa'=>$request->harga_sewa,
            'status_kamar'=>'Kosong',
            'foto_kamar'=>$foto

        ]);

        return back()->with('success','Kamar berhasil ditambahkan.');
    }

    public function update(Request $request, Kamar $kamar)
    {
        $request->validate([

            'no_kamar'=>'required|unique:kamars,no_kamar,'.$kamar->id_kamar.',id_kamar',
            'deskripsi'=>'nullable',
            'harga_sewa'=>'required|numeric',
            'foto'=>'nullable|image|max:2048'

        ]);

        $foto = $request->foto_lama;

        if($request->hasFile('foto')){

            if($foto){

                Storage::disk('public')->delete($foto);

            }

            $foto = $request->file('foto')->store('kamar','public');
        }

        $kamar->update([

            'no_kamar'=>$request->no_kamar,
            'deskripsi'=>$request->deskripsi,
            'harga_sewa'=>$request->harga_sewa,
            'foto_kamar'=>$foto

        ]);

        return back()->with('success','Kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar)
    {
        if($kamar->foto_kamar){

            Storage::disk('public')->delete($kamar->foto_kamar);

        }

        $kamar->delete();

        return back()->with('success','Kamar berhasil dihapus.');
    }
}