<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - SIMKTS</title>
    
    @vite(['resources/css/app.css'])

    <style>
        /* CSS RESET RESMI PRINT MEDIA */
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            padding-top: 80px;
        }

        /* HEADER DOKUMEN (KOP SURAT) */
        .kop-surat {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }

        .kop-surat h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .kop-surat p {
            margin: 4px 0 0;
            font-size: 13px;
        }

        /* STRUKTUR TABEL FORMAL */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
            font-size: 13px;
            color: #000;
        }

        th {
            background-color: #f3f4f6 !important;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* AREA TANDA TANGAN (SIGNATURE BLOCK) */
        .signature-container {
            margin-top: 60px;
            float: right;
            text-align: center;
            width: 220px;
            font-size: 13px;
        }

        /* BAR NAVIGASI LAYAR ATAS (Sembunyi Otomatis Saat Print) */
        .navbar-print-preview {
            background-color: #0f172a;
            border-bottom: 1px solid #1e293b;
            color: #f8fafc;
            padding: 12px 24px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: ui-sans-serif, system-ui, sans-serif;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        /* MEDIA QUERIES KONTROL CETAK */
        @media print {
            .navbar-print-preview {
                display: none !important;
            }

            body {
                padding-top: 0;
                background: #fff;
            }

            @page {
                size: A4;
                margin: 1.5cm 2cm;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="navbar-print-preview">
        <div class="text-xs font-bold tracking-wide uppercase text-slate-400 flex items-center space-x-2">
            <span class="inline-block w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
            <span>Pratinjau Cetak Laporan Keuangan</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="history.back()" 
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition duration-150 cursor-pointer">
                ⬅️ Kembali
            </button>
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition duration-150 shadow-md shadow-blue-600/10 cursor-pointer">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="sheet-content">
        <div class="kop-surat">
            <h1>KONTRAKAN 3 SAUDARA</h1>
            <p>Dusun II Cipeundeuy, Subang, Jawa Barat</p>
            <p>Telp: 0896-0852-0151 | Email: admin@gmail.com</p>
        </div>

        <h3 class="text-center" style="font-weight: bold; font-size: 16px; margin-bottom: 2px;">LAPORAN KEUANGAN BULANAN</h3>
        <p class="text-center" style="font-size: 13px; margin-top: 0;">
            Periode: <strong>{{ \Carbon\Carbon::parse($bulan_ini)->isoFormat('MMMM YYYY') }}</strong>
        </p>

        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="15%">Kamar</th>
                    <th>Nama Penghuni</th>
                    <th width="20%">Status</th>
                    <th class="text-right" width="25%">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $total_masuk = 0; 
                    $total_tunggakan = 0; 
                @endphp

                @forelse($tagihans as $index => $row)
                    @php
                        // Akumulasi total kas lunas vs piutang berjalan
                        if (strtolower($row->status) === 'lunas') {
                            $total_masuk += $row->jumlah_tagihan;
                        } else {
                            $total_tunggakan += $row->jumlah_tagihan;
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>Kamar {{ $row->nomor_kamar }}</td>
                        <td style="font-weight: bold;">{{ $row->nama_lengkap }}</td>
                        <td class="uppercase tracking-wider" style="font-size: 11px;">
                            {{ strtolower($row->status) === 'lunas' ? 'LUNAS' : 'BELUM BAYAR' }}
                        </td>
                        <td class="text-right font-mono">{{ number_format($row->jumlah_tagihan, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center italic" style="color: #6b7280; padding: 24px;">
                            Tidak ada data rekaman transaksi sirkulasi keuangan untuk periode bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            
            @if($tagihans->count() > 0)
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total Pemasukan (Lunas)</th>
                        <th class="text-right font-mono text-slate-900">{{ number_format($total_masuk, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Total Tunggakan (Piutang)</th>
                        <th class="text-right font-mono" style="color: #dc2626;">{{ number_format($total_tunggakan, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>

        <div class="signature-container">
            <p>Subang, {{ \Carbon\Carbon::now()->isoFormat('DD MMMM YYYY') }}</p>
            <br><br><br>
            <p>
                <strong>( {{ Auth::user()->name }} )</strong><br>
                <span style="color: #4b5563; font-size: 11px;">Administrator SIMKTS</span>
            </p>
        </div>
    </div>

</body>
</html>