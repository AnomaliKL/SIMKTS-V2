@extends('layouts.app')

@section('title', 'Data Kamar - SIMKTS')

@section('content')
<div class="space-y-6" 
     x-data="{ 
         search: '',
         showAddModal: false,
         showEditModal: false,
         // Form state untuk modal edit
         editData: { id: '', nomor: '', harga: '', fasilitas: '', foto_lama: '' }
     }">
    
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="text-center md:text-left w-full">
            <h2 class="text-2xl font-black text-white tracking-tight">Data Kamar</h2>
            <p class="text-slate-400 text-xs font-medium mt-1">Kelola inventaris, harga sewa bulanan, dan berkas foto fasilitas kamar kost.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto shrink-0">
            <div class="relative w-full sm:w-64">
                {{-- <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-xs">🔍</span> --}}
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <img src="{{ asset('assets/icon/search.png') }}" alt="Search" class="w-3.5 h-3.5 object-contain opacity-50">
                </span>
                <input type="text" x-model="search" placeholder="Cari Nomor / Fasilitas..." 
                       class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-2.5 text-slate-200 focus:outline-none focus:border-blue-500 transition">
            </div>
            
            <button @click="showAddModal = true" type="button" 
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/10 flex items-center justify-center space-x-2 transition cursor-pointer text-nowrap">
                <span>Tambah Kamar</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#3b82f6'
                });
            });
        </script>
    @endif

    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden flex flex-col shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-950/40 border-b border-slate-800/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3">No. Kamar</th>
                        <th class="px-5 py-3">Fasilitas</th>
                        <th class="px-5 py-3">Harga/Bulan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-xs font-medium">
                    @forelse($kamars as $row)
                        @php $status = strtolower($row->status_kamar); @endphp
                        <tr x-show="search === '' || '{{ strtolower($row->no_kamar . ' ' . $row->deskripsi) }}'.includes(search.toLowerCase())"
                            x-transition.opacity
                            class="hover:bg-slate-800/20 text-slate-200 transition">
                            
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center text-xs shadow-inner">
                                        🚪
                                    </div>
                                    <div class="flex items-center space-x-1.5">
                                        <span class="font-extrabold text-sm tracking-tight text-slate-100">Kamar {{ $row->no_kamar }}</span>
                                        @if($row->foto_kamar)
                                            <span class="text-xs opacity-60 cursor-help" title="Memiliki berkas foto">🖼️</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-3.5 text-slate-400 font-normal max-w-xs truncate">
                                {{ $row->deskripsi ?? 'Fasilitas Kamar Standar Kamar Tiga Saudara.' }}
                            </td>

                            <td class="px-5 py-3.5 text-emerald-400 font-extrabold font-mono">
                                Rp {{ number_format($row->harga_sewa, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-3.5">
                                @if($status === 'kosong')
                                    <span class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">
                                        Terisi
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5 text-center space-x-1 flex justify-center items-center">
                                <button @click="
                                            editData.id = '{{ $row->id_kamar }}';
                                            editData.nomor = '{{ $row->no_kamar }}';
                                            editData.harga = '{{ (int)$row->harga_sewa }}';
                                            editData.fasilitas = `{{ $row->deskripsi }}`;
                                            editData.foto_lama = '{{ $row->foto_kamar }}';
                                            showEditModal = true;
                                        "
                                        class="p-2 text-amber-400 hover:text-slate-950 bg-amber-500/10 hover:bg-amber-400 border border-amber-500/20 rounded-xl cursor-pointer transition">
                                    📝
                                </button>

                                <form action="{{ route('admin.kamar.destroy', $row->id_kamar) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-hapus p-2 text-rose-400 hover:text-white bg-rose-500/10 hover:bg-rose-500 border border-rose-500/20 rounded-xl cursor-pointer transition">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500 italic">
                                Belum ada rekaman inventaris data kamar kos di sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak x-transition>
        <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl transform transition-all p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <h5 class="text-base font-extrabold text-white flex items-center space-x-2">
                    <span>🏢</span> <span>Tambah Kamar Baru</span>
                </h5>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>
            
            <form action="{{ route('admin.kamar.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nomor Kamar</label>
                        <input type="text" name="no_kamar" required placeholder="Cth: A-01" 
                               class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Harga Sewa (Rp)</label>
                        <input type="number" name="harga_sewa" required placeholder="Cth: 500000" 
                               class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Fasilitas Kamar</label>
                    <textarea name="deskripsi" rows="3" placeholder="Tuliskan kelengkapan fasilitas kamar..." 
                              class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Foto Kamar (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" 
                           class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-950 file:text-slate-300 file:cursor-pointer hover:file:bg-slate-800 transition">
                </div>
                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-md shadow-blue-600/10 transition">Simpan Kamar</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak x-transition>
        <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl transform transition-all p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <h5 class="text-base font-extrabold text-white flex items-center space-x-2">
                    <span>📝</span> <span>Edit Data Kamar</span>
                </h5>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/kamar/' + editData.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="foto_lama" :value="editData.foto_lama">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nomor Kamar</label>
                        <input type="text" name="no_kamar" x-model="editData.nomor" required
                               class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Harga Sewa (Rp)</label>
                        <input type="number" name="harga_sewa" x-model="editData.harga" required
                               class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Fasilitas Kamar</label>
                    <textarea name="deskripsi" rows="3" x-model="editData.fasilitas"
                              class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Ganti Foto Kamar (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" 
                           class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-950 file:text-slate-300 file:cursor-pointer hover:file:bg-slate-800 transition">
                    <span class="text-[10px] text-slate-500 block mt-1">Biarkan kosong jika tidak ingin merubah foto lama.</span>
                </div>
                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black rounded-full shadow-md shadow-amber-500/10 transition">Update Kamar 💾</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection