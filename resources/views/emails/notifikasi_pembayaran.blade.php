<!DOCTYPE html>
<html>

<head>
    <title>Notifikasi SIMKTS</title>
</head>

<body style="font-family: sans-serif; background-color: #f4f4f5; padding: 20px;">
    <div
        style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; border: 1px solid #e4e4e7;">
        <h2>Halo, {{ $tagihan->penghuni->user->name ?? $tagihan->penghuni->nama_lengkap }}</h2>

        @if ($status === 'terima')
            <p style="color: #16a34a; font-weight: bold; font-size: 16px;">Pembayaran Anda Telah Divalidasi!</p>
            <p>Bukti transfer untuk periode
                <strong>{{ \Carbon\Carbon::parse($tagihan->bulan_tagihan)->isoFormat('MMMM YYYY') }}</strong> dinyatakan
                VALID dan berstatus LUNAS.
            </p>
        @else
            <p style="color: #dc2626; font-weight: bold; font-size: 16px;">Perhatian: Pembayaran Anda Ditolak Admin</p>
            <p>Bukti transfer untuk periode
                <strong>{{ \Carbon\Carbon::parse($tagihan->bulan_tagihan)->isoFormat('MMMM YYYY') }}</strong> dinyatakan
                TIDAK VALID.
            </p>

            <div
                style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin: 15px 0; border-radius: 4px;">
                <strong style="color: #991b1b;">Alasan Penolakan:</strong>
                <!-- Mengambil langsung dari object tagihan atau variabel alasan -->
                <p style="margin: 5px 0 0 0; color: #7f1d1d;">
                    "{{ $alasan ?? ($tagihan->alasan_ditolak ?? 'Bukti transfer tidak jelas/salah.') }}"
                </p>
            </div>

            <p>Silakan login kembali ke dashboard SIMKTS Anda untuk melakukan upload ulang bukti transfer yang benar.
            </p>
        @endif

        <hr style="border: 0; border-top: 1px solid #e4e4e7; margin: 20px 0;">
        <small style="color: #71717a;">Email ini dikirim secara otomatis oleh Sistem Informasi Manajemen Rumah Kost
            (SIMKTS).</small>
    </div>
</body>

</html>
