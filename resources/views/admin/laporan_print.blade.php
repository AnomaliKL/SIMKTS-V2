<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan - SIMKTS</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        body{
            font-family:'Times New Roman',serif;
            color:#000;
            background:#fff;
            margin:0;
            padding:20px;
            padding-top:80px;
        }

        .header{
            text-align:center;
            margin-bottom:30px;
            border-bottom:2px solid #000;
            padding-bottom:20px;
        }

        .header h1{
            margin:0;
            font-size:24px;
            text-transform:uppercase;
        }

        .header p{
            margin:4px 0;
            font-size:14px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table th,
        table td{
            border:1px solid #000;
            padding:8px 10px;
            font-size:14px;
        }

        table th{
            background:#efefef;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        .footer-sign{
            width:220px;
            float:right;
            margin-top:60px;
            text-align:center;
        }

        .navbar-print{

            position:fixed;
            top:0;
            left:0;
            right:0;

            background:#333;
            color:#fff;

            padding:15px 20px;

            display:flex;
            justify-content:space-between;
            align-items:center;

            z-index:9999;

            font-family:Arial;

        }

        .btn{

            border:none;
            border-radius:5px;
            padding:8px 18px;
            cursor:pointer;
            font-weight:bold;
            color:#fff;

        }

        .btn-back{

            background:#dc3545;

        }

        .btn-print{

            background:#0d6efd;

        }

        @media print{

            .navbar-print{

                display:none;

            }

            body{

                padding-top:0;

            }

            @page{

                size:A4;
                margin:2cm;

            }

        }

    </style>

</head>
<body>

    <div class="navbar-print">

        <strong>Preview Laporan Keuangan</strong>
        <div>
            <button type="button" onclick="window.location.href='{{ route('admin.laporan') }}'" class="btn btn-back">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </button>

            <button onclick="window.print()" class="btn btn-print">
                <i class="bi bi-printer"></i>
                Cetak PDF
            </button>
        </div>
    </div>

    <div class="header">
        <h1>KONTRAKAN 3 SAUDARA</h1>
        <p>Dusun II Cipeundeuy, Subang Jawa Barat</p>
        <p>
            Telp: 0896-0852-0151 |
            Email: admin@gmail.com
        </p>
    </div>

    <h2 class="text-center">LAPORAN KEUANGAN BULANAN</h2>
    <p class="text-center">Periode :
    <strong>
        {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}
    </strong>
    </p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kamar</th>
                <th>Nama Penghuni</th>
                <th>Status</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
    <tbody>
    @foreach($tagihans as $row)
        <tr>
            <td class="text-center">
                {{ $loop->iteration }}
            </td>
            <td>
                Kamar {{ $row->penghuni->kamar->no_kamar }}
            </td>
            <td>
                {{ $row->penghuni->nama_lengkap }}
            </td>
            <td>
                {{ strtoupper($row->status) }}
            </td>
            <td class="text-right">
                {{ number_format($row->jumlah_tagihan,0,',','.') }}
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-right">
                Total Pemasukan (Lunas)
            </th>
            <th class="text-right">
                {{ number_format($totalMasuk,0,',','.') }}
            </th>
        </tr>
        <tr>
            <th colspan="4" class="text-right">
                Total Tunggakan (Piutang)
            </th>
            <th class="text-right" style="color:red">
                {{ number_format($totalTunggakan,0,',','.') }}
            </th>
        </tr>
    </tfoot>
</table>

<div class="footer-sign">
    <p>
        Subang,
        {{ now()->translatedFormat('d F Y') }}
    </p>
    <br><br><br>
    <p>
        <strong>
            ( {{ Auth::user()->name }} )
        </strong>
        <br>
        Administrator
    </p>
</div>
</body>
</html>