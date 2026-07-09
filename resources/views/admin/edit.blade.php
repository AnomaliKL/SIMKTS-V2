@extends('layouts.app')

@section('title', 'Edit Profil Saya - SIMKTS')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-black text-white tracking-tight">Edit Profil Saya</h2>
        <p class="text-slate-400 text-xs font-medium mt-1">Perbarui informasi identitas akun personal dan kata sandi berkala Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800/60 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6">
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
                      x-data="{ 
                          imageUrl: '{{ Auth::user()->foto_user ? asset('storage/' . Auth::user()->foto_user) : '' }}',
                          fileChosen(event) {
                              const file = event.target.files[0];
                              if (!file) return;
                              const reader = new FileReader();
                              reader.readAsDataURL(file);
                              reader.onload = (e) => { this.imageUrl = e.target.result; };
                          }
                      }">
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col items-center justify-center space-y-3 pb-4 border-b border-slate-800/60">
                        <div class="relative inline-block group">
                            
                            <template x-if="imageUrl">
                                <img :src="imageUrl" 
                                     class="w-28 h-28 rounded-full border-4 border-slate-800 object-cover shadow-xl group-hover:border-blue-500 transition duration-300">
                            </template>

                            <template x-if="!imageUrl">
                                <div class="w-28 h-28 rounded-full bg-slate-950 border-2 border-dashed border-slate-800 flex items-center justify-center text-slate-600 shadow-inner group-hover:border-blue-500/50 transition duration-300">
                                    <span class="text-4xl">👤</span>
                                </div>
                            </template>

                            <label for="fotoInput" 
                                   class="absolute bottom-1 right-1 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2.5 shadow-xl border border-slate-900 cursor-pointer transition transform active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </label>
                        </div>
                        <div class="text-center">
                            <span class="text-[11px] text-slate-400 font-bold block">Klik ikon kamera untuk ganti foto profil</span>
                            <span class="text-[9px] text-slate-500 block font-medium mt-0.5">Format: JPG, JPEG, PNG (Maks 2MB)</span>
                        </div>
                        <input type="file" name="foto" id="fotoInput" class="hidden" accept="image/*" @change="fileChosen">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                   class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                            @error('name') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">No. Handphone (WhatsApp)</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', Auth::user()->no_hp) }}" required
                                   class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                            @error('no_hp') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email (Login Utama)</label>
                        <input type="email" value="{{ Auth::user()->email }}" readonly
                               class="w-full text-xs bg-slate-950 border border-slate-800/40 rounded-xl p-3 text-slate-500 cursor-not-allowed select-none focus:outline-none">
                        <span class="text-[10px] text-slate-600 block mt-1">● Email ini dikunci secara permanen sebagai parameter hak akses utama sistem.</span>
                    </div>

                    <div class="border-t border-slate-800/60 my-6 pt-4">
                        <div class="flex items-center space-x-2 text-blue-400 font-extrabold text-xs mb-4 uppercase tracking-wider">
                            <span>🛡️</span>
                            <span>Ganti Kata Sandi</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Password Baru (Opsional)</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah"
                                       class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                @error('password') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" placeholder="Tulis ulang password baru"
                                       class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-lg shadow-blue-600/20 transition duration-200 cursor-pointer">
                            <span>💾</span>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 shadow-sm space-y-4">
            <h5 class="text-xs font-extrabold text-white uppercase tracking-wider border-b border-slate-800 pb-2.5">Otoritas Sesi</h5>
            
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs font-medium">
                    <span class="text-slate-500">Status Akun :</span>
                    <span class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">
                        Aktif
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs font-medium">
                    <span class="text-slate-500">Hak Akses Role :</span>
                    <span class="text-slate-300 font-bold capitalize">{{ Auth::user()->role }}</span>
                </div>
                <div class="flex items-center justify-between text-xs font-medium">
                    <span class="text-slate-500">Terdaftar Sejak :</span>
                    <span class="text-slate-400 font-semibold text-[11px]">
                        {{ \Carbon\Carbon::parse(Auth::user()->created_at)->isoFormat('DD MMM YYYY') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection