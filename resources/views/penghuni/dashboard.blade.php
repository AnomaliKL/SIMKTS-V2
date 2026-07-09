<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Penghuni - SIMKTS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-slate-100 antialiased h-full font-sans flex flex-col"
      x-data="{ 
          showUploadModal: false,
          showProfileModal: false,
          uploadData: { id: '', bulan: '' }
      }">

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    @if(session('error'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#ef4444'
                });
            });
        </script>
    @endif

    <nav class="bg-slate-900 border-b border-slate-800/60 h-16 sticky top-0 z-30 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-base">🏢</span>
                <span class="font-extrabold text-sm tracking-tight text-white">SIM<span class="text-blue-500">KTS</span></span>
                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-black rounded-md tracking-wider uppercase">Penghuni</span>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 rounded-full overflow-hidden border border-slate-700 bg-slate-950 flex items-center justify-center font-bold text-xs text-white shadow-inner">
                    @if(Auth::user()->foto_user)
                        <img src="{{ asset('storage/' . Auth::user()->foto_user) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 border border-rose-500/30 text-rose-400 hover:bg-rose-500/10 text-xs font-bold rounded-full transition cursor-pointer">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-7xl mx-auto w-full p-4 sm:p-6 lg:p-8 space-y-6 overflow-y-auto">
        
        @if($current_tagihan && strtolower($current_tagihan->status) === 'belum_bayar')
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                <div class="flex items-start space-x-3">
                    <span class="text-xl text-rose-400 mt-0.5">⚠️</span>
                    <div>
                        <h5 class="text-xs font-black text-rose-400 uppercase tracking-wider">Tagihan Bulan Ini Belum Dibayar</h5>
                        <p class="text-slate-400 text-xs font-medium mt-1">Segera lakukan transfer sebesar <strong class="text-slate-200 font-extrabold font-mono">Rp {{ number_format($current_tagihan->jumlah_tagihan, 0, ',', '.') }}</strong> sebelum masa tenggat jatuh tempo habis.</p>
                    </div>
                </div>
                <button @click="
                            uploadData.id = '{{ $current_tagihan->id_tagihan }}';
                            uploadData.bulan = '{{ \Carbon\Carbon::parse($current_tagihan->bulan_tagihan)->isoFormat('MMMM YYYY') }}';
                            showUploadModal = true;
                        " 
                        class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md shadow-rose-600/10 cursor-pointer transition shrink-0">
                    Bayar Sekarang
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-6 text-center space-y-4 shadow-sm">
                <div class="w-24 h-24 rounded-full border-4 border-slate-800 bg-slate-950 mx-auto overflow-hidden flex items-center justify-center font-bold text-3xl shadow-xl">
                    @if(Auth::user()->foto_user)
                        <img src="{{ asset('storage/' . Auth::user()->foto_user) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="leading-tight">
                    <h5 class="text-base font-black text-white tracking-tight">{{ Auth::user()->name }}</h5>
                    <span class="text-[11px] text-slate-500 font-medium">Penghuni Kamar {{ $penghuni->nomor_kamar }}</span>
                </div>
                <div class="pt-2">
                    <button @click="showProfileModal = true" 
                            class="w-full py-2.5 border border-slate-800 hover:bg-slate-800/60 text-slate-300 text-xs font-bold rounded-xl transition cursor-pointer">
                        Edit Informasi Profil
                    </button>
                </div>
            </div>

            <div class="md:col-span-2 bg-slate-900 border border-slate-800/60 rounded-2xl p-6 flex flex-col justify-between h-full shadow-sm">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kamar Hunian Saya</span>
                        <h1 class="text-5xl font-black text-blue-500 tracking-tighter">No. {{ $penghuni->nomor_kamar }}</h1>
                    </div>
                    <div class="sm:text-right leading-tight">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Harga Sewa Bulanan</span>
                        <h3 class="text-xl font-black text-emerald-400 font-mono mt-1">Rp {{ number_format($penghuni->harga_bulanan, 0, ',', '.') }}</h3>
                        <small class="text-[10px] text-slate-500 font-medium block mt-0.5">/ Siklus Bulan Berjalan</small>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-800/60 mt-6">
                    <a href="https://wa.me/{{ $wa_admin }}?text=Halo%20Admin,%20saya%20penghuni%20{{ urlencode(Auth::user()->name) }}%20dari%20Kamar%20{{ $penghuni->nomor_kamar }}%20ingin%20bertanya..." 
                       target="_blank" 
                       class="w-full inline-flex items-center justify-center space-x-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-emerald-600/10 cursor-pointer">
                        <span>💬</span>
                        <span>Hubungi WhatsApp Admin (Bantuan)</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden flex flex-col shadow-sm">
            <div class="p-4 border-b border-slate-800 flex items-center space-x-2">
                <span class="text-xs text-amber-500">💳</span>
                <h5 class="text-xs font-extrabold text-white uppercase tracking-wider">Riwayat Invoice & Pembayaran</h5>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-950/40 border-b border-slate-800/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3">Periode</th>
                            <th class="px-5 py-3">Jatuh Tempo</th>
                            <th class="px-5 py-3">Total Tagihan</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-xs font-medium">
                        @forelse($riwayats as $row)
                            @php $status = strtolower($row->status); @endphp
                            <tr class="hover:bg-slate-800/20 text-slate-200 transition">
                                <td class="px-5 py-3.5 font-bold text-slate-200">
                                    {{ \Carbon\Carbon::parse($row->bulan_tagihan)->isoFormat('MMMM YYYY') }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 text-[11px]">
                                    {{ \Carbon\Carbon::parse($row->tgl_jatuh_tempo)->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-200 font-extrabold">
                                    Rp {{ number_format($row->jumlah_tagihan, 0, ',', '.') }}
                                end
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($status === 'lunas')
                                        <span class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">Lunas</span>
                                    @elseif($status === 'menunggu_validasi')
                                        <span class="px-2.5 py-0.5 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">Diproses</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center flex justify-center items-center">
                                    @if($status === 'belum_bayar')
                                        <button @click="
                                                    uploadData.id = '{{ $row->id_tagihan }}';
                                                    uploadData.bulan = '{{ \Carbon\Carbon::parse($row->bulan_tagihan)->isoFormat('MMMM YYYY') }}';
                                                    showUploadModal = true;
                                                " 
                                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold rounded-lg transition cursor-pointer">
                                            📤 Bayar
                                        </button>
                                    @elseif($status === 'menunggu_validasi')
                                        <span class="text-amber-500/80 text-[11px] font-medium italic inline-flex items-center space-x-1">
                                            <span>⏳</span> <span>Audit Admin</span>
                                        </span>
                                    @else
                                        <span class="text-emerald-400 text-base">🏆</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500 italic">
                                    📭 Belum ada rekaman histori tagihan atau pembayaran yang diterbitkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div x-show="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" x-cloak x-transition>
        <div @click.away="showUploadModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-800 pb-2.5">
                <h5 class="text-sm font-extrabold text-white">Upload Bukti Transfer</h5>
                <button @click="showUploadModal = false" class="text-slate-400 hover:text-white font-bold text-lg">&times;</button>
            </div>
            
            <form action="{{ route('penghuni.tagihan.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="id_tagihan" :value="uploadData.id">
                
                <p class="text-xs font-semibold text-slate-400">Pembayaran untuk periode: <span x-text="uploadData.bulan" class="text-blue-400 font-extrabold"></span></p>
                
                <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-4 text-center space-y-1">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Silakan Transfer ke Rekening:</span>
                    <h4 class="text-base font-black text-white uppercase tracking-tight">{{ $bank->nama_bank }}</h4>
                    <h3 class="text-xl font-mono font-black text-blue-400 select-all tracking-wide py-0.5">{{ $bank->no_rekening }}</h3>
                    <p class="text-xs text-slate-400 font-semibold italic">a.n {{ $bank->atas_nama }}</p>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Foto Bukti / Struk Screenshot</label>
                    <input type="file" name="bukti" required accept="image/*"
                           class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-950 file:text-slate-300 file:cursor-pointer hover:file:bg-slate-800 transition">
                    <span class="text-[10px] text-slate-600 block mt-1">Format berkas: JPG, JPEG, PNG. Ukuran maksimal file 2MB.</span>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showUploadModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-md shadow-blue-600/10 transition">Kirim Bukti 🚀</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showProfileModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" x-cloak x-transition>
        <div @click.away="showProfileModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-800 pb-2.5">
                <h5 class="text-sm font-extrabold text-white">Edit Profil Saya</h5>
                <button @click="showProfileModal = false" class="text-slate-400 hover:text-white font-bold text-lg">&times;</button>
            </div>
            
            <form action="{{ route('penghuni.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4"
                  x-data="{ 
                      avatarPreview: '{{ Auth::user()->foto_user ? asset('storage/' . Auth::user()->foto_user) : '' }}',
                      onAvatarChange(e) {
                          const file = e.target.files[0];
                          if (!file) return;
                          const reader = new FileReader();
                          reader.readAsDataURL(file);
                          reader.onload = (event) => { this.avatarPreview = event.target.result; };
                      }
                  }">
                @csrf
                @method('PATCH')

                <div class="flex flex-col items-center justify-center space-y-2">
                    <div class="w-20 h-20 rounded-full border-2 border-slate-800 bg-slate-950 overflow-hidden flex items-center justify-center text-2xl font-bold shadow-inner">
                        <template x-if="avatarPreview">
                            <img :src="avatarPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!avatarPreview">
                            <span>👤</span>
                        </template>
                    </div>
                    <label class="px-3 py-1 bg-slate-950 border border-slate-800 rounded-full text-[11px] font-bold text-slate-300 hover:text-white transition cursor-pointer relative">
                        <span>📷 Ganti Foto</span>
                        <input type="file" name="foto" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" @change="onAvatarChange">
                    </label>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" required
                               class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">No. HP (WhatsApp)</label>
                            <input type="text" name="no_hp" value="{{ Auth::user()->no_hp }}" required
                                   class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">NIK KTP</label>
                            <input type="number" name="nik_ktp" value="{{ $penghuni->nik_ktp }}" required
                                   class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                        </div>
                    </div>
                    <div class="border-t border-slate-800/60 pt-3">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Password Baru (Opsional)</label>
                        <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin diganti"
                               class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showProfileModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-md shadow-blue-600/10 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>