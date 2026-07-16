@extends('layouts.app')

@section('title', 'Pengaturan Sistem - SIMKTS')

@section('content')
    <div class="space-y-6" x-data="{
        bank: '{{ old('nama_bank', $bank->nama_bank ?? '') }}',
        norek: '{{ old('no_rekening', $bank->no_rekening ?? '') }}',
        an: '{{ old('atas_nama', $bank->atas_nama ?? '') }}'
    }">

        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Pengaturan Sistem</h2>
            <p class="text-slate-400 text-xs font-medium mt-1">Konfigurasi parameter global, kredensial Gmail server, dan
                informasi rekening pembayaran kontrakan.</p>
        </div>

        @if (session('success'))
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

        <!-- Form Utama yang membungkus Info Rekening dan Konfigurasi Gmail -->
        <form action="{{ route('admin.pengaturan.update') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

                <!-- Kiri: Info Rekening Utama -->
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-800 flex items-center space-x-2 bg-slate-900">
                        <span class="text-xs">🏦</span>
                        <h5 class="text-xs font-extrabold text-white uppercase tracking-wider">Info Rekening Utama</h5>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama
                                Bank</label>
                            <input type="text" name="nama_bank" x-model="bank" required
                                class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">No.
                                Rekening</label>
                            <input type="text" name="no_rekening" x-model="norek" required
                                class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Atas
                                Nama (A/N)</label>
                            <input type="text" name="atas_nama" x-model="an" required
                                class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                        </div>
                    </div>
                </div>

                <!-- Kanan: Konfigurasi Notifikasi Gmail -->
                <div class="bg-slate-900 border border-slate-800/60 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-800 flex items-center space-x-2 bg-slate-900">
                        <span class="text-xs">🔒</span>
                        <h5 class="text-xs font-extrabold text-white uppercase tracking-wider">Konfigurasi Notifikasi Gmail
                        </h5>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email
                                Pengirim (Gmail)</label>
                            <input type="email" name="smtp_email" value="{{ old('smtp_email', $bank->smtp_email ?? '') }}"
                                required
                                class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition"
                                placeholder="contoh@gmail.com">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Gmail
                                App Password</label>
                            <input type="password" name="smtp_app_password"
                                value="{{ old('smtp_app_password', $bank->smtp_app_password ?? '') }}" required
                                class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition"
                                placeholder="•••• •••• •••• ••••">
                            <span class="text-[10px] text-slate-500 mt-1 block leading-normal">
                                *Masukkan 16 digit kode "App Password" dari Google Account Anda, bukan password email biasa.
                            </span>
                        </div>

                        <!-- Mengisi kekosongan tinggi agar layout seimbang -->
                        <div class="hidden md:block py-3"></div>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit & Preview ditaruh di bawah Grid secara penuh -->
            <div class="mt-6 space-y-6">
                <button type="submit"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-lg shadow-blue-600/20 transition duration-200 cursor-pointer">
                    Simpan Semua Perubahan
                </button>

                <!-- Preview Rekening Aktif di bagian bawah -->
                <div
                    class="bg-slate-900 border border-dashed border-slate-800 rounded-2xl p-6 text-center space-y-3 shadow-inner relative overflow-hidden group">
                    <div
                        class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(59,130,246,0.03),transparent_60%)]">
                    </div>

                    <div class="relative z-10 space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Preview Rekening
                            Aktif</span>

                        <h3 class="text-xl font-black text-white tracking-tight pt-2"
                            x-text="bank.trim() !== '' ? bank : 'Nama Bank'"></h3>

                        <h4 class="font-mono text-lg font-extrabold text-blue-400 select-all"
                            x-text="norek.trim() !== '' ? norek : '0000000000'"></h4>

                        <p class="text-xs text-slate-400 font-medium italic"
                            x-text="an.trim() !== '' ? 'a.n ' + an : 'a.n Pemilik Rekening'"></p>
                    </div>

                    <div class="pt-4 border-t border-slate-800/60 max-w-md mx-auto text-center relative z-10">
                        <span class="text-[10px] text-slate-600 leading-normal block">
                            *Info rekening ini akan otomatis tampil di halaman tagihan dashboard penghuni sebagai tujuan
                            transfer resmi.
                        </span>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
