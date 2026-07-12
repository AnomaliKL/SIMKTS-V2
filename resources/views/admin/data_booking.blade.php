@extends('layouts.app')

@section('title', 'Request Booking Masuk - SIMKTS')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-black text-white tracking-tight">Request Booking Masuk</h2>
        <p class="text-slate-400 text-xs font-medium mt-1">Validasi permintaan sewa kamar kontrakan dari calon penghuni yang berstatus pending.</p>
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
                        <th class="px-5 py-3">Tgl Request</th>
                        <th class="px-5 py-3">Nama Pemohon</th>
                        <th class="px-5 py-3">Kamar</th>
                        <th class="px-5 py-3">Mulai Ngekos</th>
                        <th class="px-5 py-3">NIK (Draft)</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-xs font-medium">
                    @forelse($bookings as $row)
                        <tr class="hover:bg-slate-800/20 text-slate-200 transition">
                            <td class="px-5 py-3.5 text-slate-500 leading-normal text-[11px]">
                                <div class="font-bold text-slate-400">{{ \Carbon\Carbon::parse($row->tgl_pengajuan)->isoFormat('DD MMM YYYY') }}</div>
                                <div class="text-[10px] mt-0.5">{{ \Carbon\Carbon::parse($row->tgl_pengajuan)->format('H:i') }} WIB</div>
                            </td>

                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-200 tracking-tight">
                                    {{ $row->user->name }}
                                </div>

                                <a href="https://wa.me/{{ $row->user->no_hp }}" target="_blank"
                                class="text-[10px] text-emerald-400 hover:underline inline-flex items-center space-x-1 mt-0.5 font-semibold">
                                    <span>💬</span>
                                    <span>{{ $row->user->no_hp }}</span>
                                </a>
                            </td>

                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 bg-blue-500/10 text-blue-400 border border-blue-500/10 text-[10px] font-bold rounded-md shadow-inner">
                                    Kamar {{ $row->nomor_kamar }}
                                </span>
                            </td>

                            <td class="px-5 py-3.5 text-amber-400 font-bold">
                                {{ \Carbon\Carbon::parse($row->tgl_mulai_kos)->isoFormat('DD MMM YYYY') }}
                            </td>

                            <td class="px-5 py-3.5 font-mono text-slate-400 tracking-wider">
                                {{ $row->nik_ktp }}
                            </td>

                            <td class="px-5 py-3.5 text-center space-y-1 sm:space-y-0 sm:space-x-1.5 flex flex-col sm:flex-row justify-center items-center">
                                <form action="{{ route('admin.booking.approve', $row->id_booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button" class="btn-terima text-[10px] font-bold bg-emerald-500/10 hover:bg-emerald-500 text-emerald-400 hover:text-white border border-emerald-500/20 px-3 py-1.5 rounded-full cursor-pointer transition duration-150">
                                        ✓ Terima
                                    </button>
                                </form>

                                <form action="{{ route('admin.booking.reject', $row->id_booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button" class="btn-tolak text-[10px] font-bold bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 px-3 py-1.5 rounded-full cursor-pointer transition duration-150">
                                        ✕ Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-slate-500 italic">
                                <div class="text-3xl mb-2 opacity-40">📥</div>
                                <span class="text-xs">Belum ada permintaan sewa (booking) baru yang masuk.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        // 1. TRIGGER AKSI TERIMA BOOKING
        const targetTerima = e.target.closest('.btn-terima');
        if (targetTerima) {
            e.preventDefault();
            Swal.fire({
                title: 'Terima Booking?',
                text: "Pemohon akan menjadi penghuni aktif dan tagihan bulan pertama akan dibuat.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981', // Emerald-500
                cancelButtonColor: '#64748b',  // Slate-500
                confirmButtonText: 'Ya, Terima!',
                cancelButtonText: 'Batal',
                background: '#0f172a',
                color: '#fff',
                customClass: { popup: 'border border-slate-800 rounded-2xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    targetTerima.closest('form').submit();
                }
            });
        }

        // 2. TRIGGER AKSI TOLAK BOOKING
        const targetTolak = e.target.closest('.btn-tolak');
        if (targetTolak) {
            e.preventDefault();
            Swal.fire({
                title: 'Tolak Permintaan Sewa?',
                text: "Permintaan Sewa Akan Dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Rose-500
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal',
                background: '#0f172a',
                color: '#fff',
                customClass: { popup: 'border border-slate-800 rounded-2xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    targetTolak.closest('form').submit();
                }
            });
        }
    });
</script>
@endsection