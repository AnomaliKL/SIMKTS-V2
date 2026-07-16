@extends('layouts.app')

@section('title', 'Kelola Data Tagihan - SIMKTS')

@section('content')
    <div class="space-y-6" x-data="{
        showPreviewModal: false,
        previewImg: '',
        previewNama: ''
    }">

        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-white tracking-tight">Data Tagihan</h2>
                <p class="text-slate-400 text-xs font-medium mt-1">Sirkulasi tagihan bulanan massal, validasi kas masuk, dan
                    audit bukti transfer penyewa.</p>
            </div>

            <form action="{{ route('admin.tagihan.generate') }}" method="POST" id="formGenerate" class="w-full md:w-auto">
                @csrf
                <!-- 🛠️ Tambahkan input hidden ini untuk mengirim parameter bulan ke Controller -->
                <input type="hidden" name="bulan" id="hiddenBulanTarget" value="{{ request('bulan', date('Y-m')) }}">

                <button type="button" id="btnGenerate"
                    class="w-full md:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-lg shadow-blue-600/10 flex items-center justify-center space-x-2 transition cursor-pointer text-nowrap">
                    <span>🪄</span> <span>Generate Tagihan</span>
                </button>
            </form>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-4 shadow-sm">
            <form method="GET" class="flex flex-col md:flex-row md:items-end gap-3">
                <div class="w-full md:w-1/4">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status
                        Pembayaran</label>
                    <select name="status"
                        class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-slate-200 focus:outline-none focus:border-blue-500 [color-scheme:dark] transition">
                        <option value="">-- Semua Status --</option>
                        <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar
                        </option>
                        <option value="menunggu_validasi" {{ request('status') === 'menunggu_validasi' ? 'selected' : '' }}>
                            Menunggu Validasi</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <div class="w-full md:w-1/4">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Bulan
                        Tagihan</label>
                    <input type="month" name="bulan" value="{{ request('bulan') }}"
                        class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-slate-200 focus:outline-none focus:border-blue-500 [color-scheme:dark] transition">
                </div>

                <div class="flex items-center space-x-2 pt-2 md:pt-0">
                    <button type="submit"
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                        🔍 Filter
                    </button>
                    @if (request('status') || request('bulan'))
                        <a href="{{ route('admin.tagihan.index') }}"
                            class="px-4 py-2.5 bg-slate-950 hover:bg-slate-900 border border-slate-800 text-slate-400 hover:text-slate-200 text-xs font-bold rounded-xl transition">
                            🔄 Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden flex flex-col shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-950/40 border-b border-slate-800/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3">Periode</th>
                            <th class="px-5 py-3">Penghuni</th>
                            <th class="px-5 py-3">Kamar</th>
                            <th class="px-5 py-3">Jumlah</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Bukti</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-xs font-medium">
                        @forelse($tagihans as $row)
                            @php $status = strtolower($row->status); @endphp
                            <tr class="hover:bg-slate-800/20 text-slate-200 transition">
                                <td class="px-5 py-3.5 font-bold tracking-tight text-slate-200">
                                    {{ \Carbon\Carbon::parse($row->bulan_tagihan)->isoFormat('MMMM YYYY') }}
                                </td>

                                <td class="px-5 py-3.5 text-slate-300">
                                    {{ $row->nama_lengkap }}
                                </td>

                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-0.5 bg-slate-800 border border-slate-700 text-slate-300 text-[10px] font-bold rounded-md shadow-inner">
                                        Kamar {{ $row->nomor_kamar }}
                                    </span>
                                </td>

                                <td class="px-5 py-3.5 font-mono text-slate-300 font-extrabold">
                                    Rp {{ number_format($row->jumlah_tagihan, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-3.5">
                                    @if ($status === 'lunas')
                                        <span
                                            class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">
                                            Lunas
                                        </span>
                                    @elseif($status === 'menunggu_validasi')
                                        <span
                                            class="px-2.5 py-0.5 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide animate-pulse">
                                            Perlu Cek
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">
                                            Belum Bayar
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5">
                                    @if ($row->bukti_bayar)
                                        <button type="button"
                                            @click="
                                                previewImg = '{{ asset('storage/' . $row->bukti_bayar) }}';
                                                previewNama = `{{ $row->nama_lengkap }}`;
                                                showPreviewModal = true;
                                            "
                                            class="px-3 py-1 bg-slate-950 hover:bg-slate-800 text-blue-400 hover:text-blue-300 border border-slate-800 text-[10px] font-bold rounded-full transition duration-150 cursor-pointer">
                                            👁️ Lihat Bukti
                                        </button>
                                    @else
                                        <span class="text-slate-600 italic text-[11px]">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-center flex justify-center items-center space-x-1">
                                    @if ($status === 'menunggu_validasi')
                                        <form action="{{ route('admin.tagihan.validate', $row->id_tagihan) }}"
                                            method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="button"
                                                class="btn-validasi p-1.5 text-emerald-400 hover:text-white bg-emerald-500/10 hover:bg-emerald-500 border border-emerald-500/20 rounded-xl cursor-pointer transition">
                                                ✓
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.tagihan.reject', $row->id_tagihan) }}" method="POST"
                                            class="inline form-tolak-massal">
                                            @csrf @method('PATCH')
                                            <!-- Input hidden untuk menampung teks dari SweetAlert -->
                                            <input type="hidden" name="alasan_ditolak" class="input-alasan-hidden">

                                            <button type="button"
                                                class="btn-tolak p-1.5 text-rose-400 hover:text-white bg-rose-500/10 hover:bg-rose-500 border border-rose-500/20 rounded-xl cursor-pointer transition">
                                                ✕
                                            </button>
                                        </form>
                                    @elseif($status === 'belum_bayar')
                                        <form action="{{ route('admin.tagihan.validate', $row->id_tagihan) }}"
                                            method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="button"
                                                class="btn-validasi px-3 py-1 text-[10px] font-bold text-slate-300 hover:text-white bg-slate-950 border border-slate-800 hover:border-slate-700 rounded-lg cursor-pointer transition">
                                                Set Lunas
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-emerald-400 text-lg select-none">🏆</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-500 italic">
                                    📭 Tidak ada rekaman transaksi tagihan sesuai filter parameter yang dicari.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="showPreviewModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" x-cloak
            x-transition>
            <div @click.away="showPreviewModal = false"
                class="bg-slate-900 border border-slate-800 w-full max-w-xl rounded-2xl overflow-hidden shadow-2xl transform transition-all p-5 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800/80 pb-2.5">
                    <h5 class="text-xs font-extrabold text-white flex items-center space-x-2">
                        <span>🖼️</span> <span>Bukti Transfer:</span> <span x-text="previewNama"
                            class="text-blue-400"></span>
                    </h5>
                    <button @click="showPreviewModal = false"
                        class="text-slate-400 hover:text-white text-lg font-bold focus:outline-none">&times;</button>
                </div>

                <div
                    class="bg-slate-950 rounded-xl p-3 border border-slate-900 flex justify-center items-center shadow-inner overflow-hidden max-h-[70vh]">
                    <img :src="previewImg" class="max-w-full max-h-[60vh] object-contain rounded-lg shadow-2xl"
                        alt="Bukti Upload">
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. TRIGGER SUBMIT EVENT GENERATE MASSAL
            const btnGen = document.getElementById('btnGenerate');
            if (btnGen) {
                btnGen.addEventListener('click', () => {
                    Swal.fire({
                        title: 'Generate Tagihan Massal?',
                        text: "Sistem akan membuat tagihan bulan ini untuk semua penghuni aktif.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Eksekusi!',
                        cancelButtonText: 'Batal',
                        background: '#0f172a',
                        color: '#fff',
                        customClass: {
                            popup: 'border border-slate-800 rounded-2xl'
                        }
                    }).then((res) => {
                        if (res.isConfirmed) document.getElementById('formGenerate').submit();
                    });
                });
            }

            // 2. DELEGASI GLOBAL CONFIRMATION AKSI TERIMA & TOLAK
            document.addEventListener('click', (e) => {
                const actValidate = e.target.closest('.btn-validasi');
                if (actValidate) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validasi Pembayaran?',
                        text: "Bukti Pembayaran Valid.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Validasi!',
                        cancelButtonText: 'Batal',
                        background: '#0f172a',
                        color: '#fff',
                        customClass: {
                            popup: 'border border-slate-800 rounded-2xl'
                        }
                    }).then((res) => {
                        if (res.isConfirmed) actValidate.closest('form').submit();
                    });
                }

                // const actReject = e.target.closest('.btn-tolak');
                // if (actReject) {
                //     e.preventDefault();
                //     Swal.fire({
                //         title: 'Tolak Berkas Bukti?',
                //         text: "Bukti transfer tidak valid",
                //         icon: 'error',
                //         showCancelButton: true,
                //         confirmButtonColor: '#ef4444',
                //         cancelButtonColor: '#64748b',
                //         confirmButtonText: 'Ya, Tolak!',
                //         cancelButtonText: 'Batal',
                //         background: '#0f172a',
                //         color: '#fff',
                //         customClass: {
                //             popup: 'border border-slate-800 rounded-2xl'
                //         }
                //     }).then((res) => {
                //         if (res.isConfirmed) actReject.closest('form').submit();
                //     });
                // }
                const actReject = e.target.closest('.btn-tolak');
                if (actReject) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Tolak Berkas Bukti?',
                        text: "Masukkan alasan penolakan bukti transfer ini:",
                        input: 'textarea', // 🛠️ Menampilkan kolom text area input di dalam SweetAlert
                        inputPlaceholder: 'Contoh: Nominal transfer kurang / Gambar struk blur...',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Tolak & Kirim Email!',
                        cancelButtonText: 'Batal',
                        background: '#0f172a',
                        color: '#fff',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Anda wajib mengisi alasan penolakan!' // Mencegah form dikirim kosong
                            }
                        },
                        customClass: {
                            popup: 'border border-slate-800 rounded-2xl',
                            input: 'bg-slate-950 border border-slate-800 text-white text-xs rounded-xl focus:outline-none focus:border-blue-500'
                        }
                    }).then((res) => {
                        if (res.isConfirmed) {
                            const formParent = actReject.closest('form');
                            // Isi nilai input hidden dengan teks yang diketik admin di SweetAlert
                            formParent.querySelector('.input-alasan-hidden').value = res.value;
                            formParent.submit();
                        }
                    });
                }
            });
        });
    </script>
@endsection
