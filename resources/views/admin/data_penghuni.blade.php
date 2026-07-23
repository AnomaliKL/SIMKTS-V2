@extends('layouts.app')

@section('title', 'Data Penghuni - SIMKTS')

@push('styles')
    <!-- CSS intl-tel-input (Lokal/CDN khusus Data Penghuni) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intl-tel-input.css">
    <style>
        .iti {
            width: 100% !important;
            display: block !important;
        }

        .iti input[type="tel"] {
            padding-left: 90px !important;
        }

        .iti__selected-flag {
            background-color: transparent !important;
            padding-left: 12px !important;
        }

        .iti__selected-dial-code {
            color: #cbd5e1 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
        }

        .iti__dropdown-content,
        div.iti__dropdown-content,
        .iti__country-list {
            background-color: #0f172a !important;
            color: #cbd5e1 !important;
            border: none !important;
        }

        .iti__dropdown-content {
            border: 1px solid #1e293b !important;
            border-radius: 8px !important;
            padding: 0 !important;
            overflow: hidden !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
            top: 100% !important;
            bottom: auto !important;
            margin-top: 4px !important;
        }

        .iti__country-list {
            z-index: 9999 !important;
            width: 100% !important;
            min-width: 300px !important;
            max-height: 250px !important;
            margin: 0 !important;
        }

        .iti__country {
            padding: 8px 12px !important;
        }

        .iti__country:hover {
            background-color: #1e293b !important;
            color: #ffffff !important;
        }

        .iti__country-name,
        .iti__dial-code {
            color: #cbd5e1 !important;
        }

        .iti__search-container {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6" x-data="{
        search: '',
        showAddModal: false,
        showEditModal: false,
    
        // Master Array Kamar Kosong dari JSON Laravel
        kamarKosong: {{ json_encode($kamar_kosong) }},
    
        // Reactive state penampung parameter Edit Form
        editForm: {
            id_penghuni: '',
            id_user: '',
            nama: '',
            nik: '',
            hp: '',
            email: '',
            id_kamar_lama: '',
            nomor_kamar_lama: '',
            id_kamar_pilih: '',
            tanggal_asli: '',
            tanggal_tampil: ''
        },
    
        // Fungsi inisialisasi pemicu mutasi tanggal otomatis jika pindah kamar
        onKamarChange() {
            if (this.editForm.id_kamar_pilih !== this.editForm.id_kamar_lama) {
                const today = new Date();
                this.editForm.tanggal_tampil = today.toISOString().split('T')[0];
            } else {
                this.editForm.tanggal_tampil = this.editForm.tanggal_asli;
            }
        }
    }">

        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-center md:text-left w-full">
                <h2 class="text-2xl font-black text-white tracking-tight">Data Penghuni</h2>
                <p class="text-slate-400 text-xs font-medium mt-1">Daftar rekaman penyewa / kontrakan aktif di Tiga Saudara
                    saat ini.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto shrink-0">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-xs">🔍</span>
                    <input type="text" x-model="search" placeholder="Cari Nama / Kamar..."
                        class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-2.5 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                </div>

                <button @click="showAddModal = true"
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/10 flex items-center justify-center space-x-2 transition cursor-pointer text-nowrap">
                    <span>👤➕</span> <span>Input Penghuni</span>
                </button>
            </div>
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
        @if (session('error'))
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
        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        title: 'Gagal Validasi!',
                        html: `{!! implode('<br>', $errors->all()) !!}`,
                        icon: 'error',
                        background: '#0f172a',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                });
            </script>
        @endif

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden flex flex-col shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-950/40 border-b border-slate-800/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3">Nama Penghuni</th>
                            <th class="px-5 py-3">Kamar</th>
                            <th class="px-5 py-3">Kontak / NIK</th>
                            <th class="px-5 py-3">Tgl Masuk</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-xs font-medium">
                        @forelse($penghunis as $row)
                            <tr x-show="search === '' || '{{ strtolower($row->nama_lengkap . ' ' . ($row->kamar->no_kamar ?? '')) }}'.includes(search.toLowerCase())"
                                x-transition.opacity class="hover:bg-slate-800/20 text-slate-200 transition">

                                <td class="px-5 py-3.5 font-bold tracking-tight text-slate-100">
                                    {{ $row->nama_lengkap }}
                                </td>

                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-0.5 bg-blue-500/10 text-blue-400 border border-blue-500/10 text-[10px] font-bold rounded-md shadow-inner">
                                        Kamar {{ $row->kamar->no_kamar }}
                                    </span>
                                </td>

                                <td class="px-5 py-3.5 space-y-0.5">
                                    <div class="text-slate-300 font-semibold inline-flex items-center space-x-1">
                                        <span class="text-xs text-emerald-500">💬</span>
                                        <span>{{ $row->no_hp }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-mono tracking-wider">NIK:
                                        {{ $row->nik_ktp }}</div>
                                </td>

                                <td class="px-5 py-3.5 text-slate-400 text-[11px]">
                                    {{ \Carbon\Carbon::parse($row->tgl_masuk)->isoFormat('DD MMM YYYY') }}
                                </td>

                                <td class="px-5 py-3.5">
                                    @if (strtolower($row->status_huni) === 'aktif')
                                        <span
                                            class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 bg-slate-800 text-slate-500 border border-slate-700 text-[10px] font-bold rounded-full uppercase tracking-wide">
                                            Keluar
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-center space-x-1 flex justify-center items-center">
                                    <button
                                        @click="
                                            editForm.id_penghuni = '{{ $row->id_penghuni }}';
                                            editForm.id_user = '{{ $row->id_user }}';
                                            editForm.nama = `{{ $row->nama_lengkap }}`;
                                            editForm.nik = '{{ $row->nik_ktp }}';
                                            editForm.hp = '{{ $row->no_hp }}';
                                            editForm.email = '{{ $row->email }}';
                                            editForm.id_kamar_lama = '{{ $row->id_kamar }}';
                                            editForm.nomor_kamar_lama='{{ $row->kamar->no_kamar }}';
                                            editForm.id_kamar_pilih = '{{ $row->id_kamar }}';
                                            editForm.tanggal_asli = '{{ $row->tgl_masuk }}';
                                            editForm.tanggal_tampil = '{{ $row->tgl_masuk }}';
                                            showEditModal = true;
                                            $nextTick(() => { if(window.editPhoneInput) window.editPhoneInput.setNumber('+' + '{{ $row->no_hp }}'); });"
                                        class="p-2 text-amber-400 hover:text-slate-950 bg-amber-500/10 hover:bg-amber-400 border border-amber-500/20 rounded-xl cursor-pointer transition">
                                        📝
                                    </button>

                                    <form action="{{ route('admin.penghuni.checkout', $row->id_penghuni) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button type="button"
                                            class="btn-checkout p-2 text-rose-400 hover:text-white bg-rose-500/10 hover:bg-rose-500 border border-rose-500/20 rounded-xl cursor-pointer transition"
                                            title="Check-out">
                                            🚪💨
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-500 italic">
                                    📭 Belum ada rekaman data penyewa penghuni kos yang aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL ADD PENGHUNI -->
        <div x-show="showAddModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak
            x-transition>
            <div @click.away="showAddModal = false"
                class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl overflow-hidden shadow-2xl transform transition-all p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                    <h5 class="text-base font-extrabold text-white flex items-center space-x-2">
                        <span>📋</span> <span>Input Penghuni Baru</span>
                    </h5>
                    <button @click="showAddModal = false"
                        class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
                </div>
                <form id="formAddPenghuni" action="{{ route('admin.penghuni.store') }}" method="POST" class="space-y-4"
                    x-data="{
                        tipeUser: 'baru',
                        selectedUserNama: '',
                        onUserLamaChange(e) {
                            const opt = e.target.options[e.target.selectedIndex];
                            this.selectedUserNama = (opt && opt.value !== '') ? opt.getAttribute('data-nama') : '';
                        }
                    }">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <h6 class="text-xs font-black text-blue-400 uppercase tracking-wider">Data Pribadi</h6>

                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kategori
                                    Pendaftar</label>
                                <div class="grid grid-cols-2 gap-2 bg-slate-950 p-1 rounded-xl border border-slate-800">
                                    <label
                                        class="flex items-center justify-center p-2 rounded-lg text-xs font-bold cursor-pointer transition"
                                        :class="tipeUser === 'baru' ? 'bg-blue-600 text-white' :
                                            'text-slate-400 hover:text-white'">
                                        <input type="radio" name="jenis_user" value="baru" x-model="tipeUser"
                                            @click="selectedUserNama = ''" class="hidden"> Akun Baru
                                    </label>
                                    <label
                                        class="flex items-center justify-center p-2 rounded-lg text-xs font-bold cursor-pointer transition"
                                        :class="tipeUser === 'lama' ? 'bg-blue-600 text-white' :
                                            'text-slate-400 hover:text-white'">
                                        <input type="radio" name="jenis_user" value="lama" x-model="tipeUser"
                                            class="hidden"> Cari User Lama
                                    </label>
                                </div>
                            </div>

                            <div x-show="tipeUser === 'lama'" x-transition>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pilih
                                    Akun Pengunjung</label>
                                <select name="id_user" :required="tipeUser === 'lama'"
                                    @change="onUserLamaChange($event)"
                                    class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 [color-scheme:dark]">
                                    <option value="">-- Pilih Akun Terdaftar --</option>
                                    @foreach ($pengunjung as $u)
                                        <option value="{{ $u->id }}" data-nama="{{ $u->name }}">
                                            {{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama
                                    Lengkap</label>
                                <input type="text" name="nama" required x-bind:value="selectedUserNama"
                                    :readonly="tipeUser === 'lama'"
                                    :class="tipeUser === 'lama' ?
                                        'bg-slate-900 border-slate-800/60 text-slate-400 cursor-not-allowed select-none' :
                                        'bg-slate-950 border border-slate-800 text-slate-200'"
                                    class="w-full text-xs border rounded-xl p-3 focus:outline-none focus:border-blue-500 transition">
                            </div>

                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nomor
                                    NIK KTP</label>
                                <input type="number" name="nik" required
                                    class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">No.
                                        HP (WhatsApp)</label>
                                    <div
                                        class="relative rounded-xl bg-slate-950 border border-slate-800 focus-within:border-blue-500 transition">
                                        <input type="tel" id="add_phone_input" required
                                            class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                        <input type="hidden" id="add_full_phone" name="hp">
                                    </div>
                                </div>
                                <div x-show="tipeUser === 'baru'">
                                    <label
                                        class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email
                                        Akun</label>
                                    <input type="email" name="email" :required="tipeUser === 'baru'"
                                        class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 md:border-l md:border-slate-800/80 md:pl-6">
                            <h6 class="text-xs font-black text-emerald-400 uppercase tracking-wider">Data Kamar Sewa</h6>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pilih
                                    Blok Kamar</label>
                                <select name="id_kamar" required
                                    class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 font-bold focus:outline-none focus:border-blue-500 [color-scheme:dark] transition">
                                    <option value="">-- Pilih Kamar Kosong --</option>
                                    <template x-for="k in kamarKosong" :key="k.id_kamar">
                                        <option :value="k.id_kamar"
                                            x-text="`Kamar ${k.no_kamar} (Rp ${new Intl.NumberFormat('id-ID').format(k.harga_sewa)}/bln)`">
                                        </option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal
                                    Masuk</label>
                                <input type="date" name="tgl_masuk" required
                                    class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 [color-scheme:dark] transition">
                            </div>
                            <div class="pt-2">
                                <div
                                    class="p-3.5 bg-slate-950/60 rounded-xl border border-slate-800 text-[10px] text-slate-500 leading-relaxed font-medium">
                                    🔐 <strong>Pengamanan Kredensial:</strong> Jika memilih Akun Baru, sistem otomatis akan
                                    membuatkan password default berupa <strong class="text-slate-300">123456</strong> untuk
                                    login perdana.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-800">
                        <button type="button" @click="showAddModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-md shadow-blue-600/10 transition">
                            Daftarkan Penghuni 🚀
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT PENGHUNI -->
        <div x-show="showEditModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak
            x-transition>
            <div @click.away="showEditModal = false"
                class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl overflow-hidden shadow-2xl transform transition-all p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                    <h5 class="text-base font-extrabold text-white flex items-center space-x-2">
                        <span>📝</span> <span>Edit Data & Kamar Penghuni</span>
                    </h5>
                    <button @click="showEditModal = false"
                        class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
                </div>

                <form id="formEditPenghuni" :action="'/admin/penghuni/' + editForm.id_penghuni" method="POST"
                    class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_user" :value="editForm.id_user">
                    <input type="hidden" name="kamar_lama" :value="editForm.id_kamar_lama">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <h6 class="text-xs font-black text-blue-400 uppercase tracking-wider">Data Pribadi</h6>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama
                                    Lengkap</label>
                                <input type="text" name="nama" x-model="editForm.nama" required
                                    class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nomor
                                    NIK KTP</label>
                                <input type="number" name="nik" x-model="editForm.nik" required
                                    class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">No.
                                        HP (WhatsApp)</label>
                                    <div
                                        class="relative rounded-xl bg-slate-950 border border-slate-800 focus-within:border-blue-500 transition">
                                        <input type="tel" id="edit_phone_input" required
                                            class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                        <input type="hidden" id="edit_full_phone" name="hp" x-model="editForm.hp">
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email
                                        (Terikat)</label>
                                    <input type="email" x-model="editForm.email" readonly
                                        class="w-full text-xs bg-slate-950 border border-slate-800/40 rounded-xl p-3 text-slate-500 cursor-not-allowed select-none focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 md:border-l md:border-slate-800/80 md:pl-6">
                            <h6 class="text-xs font-black text-emerald-400 uppercase tracking-wider">Data Alokasi Sewa</h6>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kamar
                                    Hunian (Opsi Pindah)</label>
                                <select name="id_kamar" x-model="editForm.id_kamar_pilih" @change="onKamarChange()"
                                    class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 font-bold focus:outline-none focus:border-blue-500 [color-scheme:dark] transition">
                                    <option :value="editForm.id_kamar_lama"
                                        x-text="`Tetap di Kamar ${editForm.nomor_kamar_lama}`"></option>
                                    <template x-for="k in kamarKosong" :key="k.id_kamar">
                                        <option :value="k.id_kamar"
                                            x-text="`Pindah ke ${k.no_kamar} (Rp ${new Intl.NumberFormat('id-ID').format(k.harga_sewa)})`">
                                        </option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal
                                    Masuk / Mutasi Kamar</label>
                                <input type="date" name="tgl_masuk" x-model="editForm.tanggal_tampil" required
                                    readonly
                                    class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-400 cursor-not-allowed select-none focus:outline-none [color-scheme:dark]">
                                <span class="text-[10px] text-slate-500 block mt-1.5 leading-normal">
                                    *Jika memutuskan mutasi pindah kamar, parameter tanggal sewa otomatis dimutasi ke <span
                                        class="text-amber-500 font-bold">Hari Ini</span> untuk penyesuaian siklus tagihan
                                    baru.
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-800">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black rounded-full shadow-md shadow-amber-500/10 transition">
                            Update Data 💾
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intl-tel-input.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Inisialisasi intlTelInput Modal Add
            const addPhoneField = document.querySelector("#add_phone_input");
            const addFullPhone = document.querySelector("#add_full_phone");
            if (addPhoneField && window.intlTelInput) {
                const addPhoneInput = window.intlTelInput(addPhoneField, {
                    initialCountry: "id",
                    allowDropdown: true,
                    separateDialCode: true,
                    countrySearch: false,
                    dropdownContainer: document.body,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
                });

                addPhoneField.addEventListener("input", function() {
                    if (addPhoneField.value.startsWith("0")) addPhoneField.value = addPhoneField.value
                        .substring(1);
                });

                document.querySelector("#formAddPenghuni").addEventListener("submit", function() {
                    if (addPhoneField.value.startsWith("0")) addPhoneField.value = addPhoneField.value
                        .substring(1);
                    addFullPhone.value = addPhoneInput.getNumber().replace('+', '');
                });
            }

            // 2. Inisialisasi intlTelInput Modal Edit
            const editPhoneField = document.querySelector("#edit_phone_input");
            const editFullPhone = document.querySelector("#edit_full_phone");
            if (editPhoneField && window.intlTelInput) {
                window.editPhoneInput = window.intlTelInput(editPhoneField, {
                    initialCountry: "id",
                    allowDropdown: true,
                    separateDialCode: true,
                    countrySearch: false,
                    dropdownContainer: document.body,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
                });

                editPhoneField.addEventListener("input", function() {
                    if (editPhoneField.value.startsWith("0")) editPhoneField.value = editPhoneField.value
                        .substring(1);
                });

                document.querySelector("#formEditPenghuni").addEventListener("submit", function() {
                    if (editPhoneField.value.startsWith("0")) editPhoneField.value = editPhoneField.value
                        .substring(1);
                    editFullPhone.value = window.editPhoneInput.getNumber().replace('+', '');
                });
            }

            // 3. SweetAlert Checkout
            document.addEventListener('click', function(e) {
                const btnCheckout = e.target.closest('.btn-checkout');
                if (btnCheckout) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Check-out Penghuni?',
                        text: "Status hunian penyewa ini akan dinonaktifkan, status kamar beralih kembali menjadi Kosong, dan role akun user diturunkan menjadi Pengunjung kembali.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Check-out!',
                        cancelButtonText: 'Batal',
                        background: '#0f172a',
                        color: '#fff',
                        customClass: {
                            popup: 'border border-slate-800 rounded-2xl shadow-2xl'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btnCheckout.closest('form').submit();
                        }
                    });
                }
            });
        });
    </script>
@endpush
