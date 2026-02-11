<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PPJB</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        .no-border td {
            border: none;
            padding: 2px 4px;
        }

        .header-table td {
            border: 1px solid #000;
            text-align: center;
            font-weight: bold;
        }

        .logo {
            width: 80px;
            text-align: center;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>
<body>

{{-- HEADER --}}
<table class="header-table">
    <tr>
        <td class="logo">
            <img src="{{ public_path('logo.png') }}" width="70">
        </td>
        <td>
            <div class="title">PT. Marka Inspektindo Technical</div>
            <div class="title">(MARINDOTECH)</div>
        </td>
        <td width="25%" style="font-size:10px;">
            MIT-F05-02a<br>
            Revisi 02<br>
            {{ \Carbon\Carbon::parse($ppjb->tanggal_permohonan)->format('d-m-Y') }}
        </td>
    </tr>
</table>

<table>
    <tr>
        <td class="subtitle">PERMOHONAN PENGADAAN BARANG/JASA</td>
    </tr>
</table>

{{-- INFORMASI --}}
<table class="no-border mt-10">
    <tr>
        <td width="55%">
            <table class="no-border">
                <tr><td>Kepada</td><td>: Direktur Utama</td></tr>
                <tr><td>Dari</td><td>: Dept. Operasional</td></tr>
                <tr><td>Tanggal Permohonan</td><td>: {{ $ppjb->tanggal_permohonan }}</td></tr>
                <tr><td>No. Project</td><td>: {{ $ppjb->project }}</td></tr>
                <tr><td>Pekerjaan</td><td>: {{ $ppjb->pekerjaan }}</td></tr>
                <tr><td>PIC</td><td>: {{ $ppjb->PIC }}</td></tr>
            </table>
        </td>
        <td width="45%">
            <table class="no-border">
                <tr><td>No. PPJB</td><td>: {{ $ppjb->nosurat }}</td></tr>
                <tr><td>Refer to Project No</td><td>: {{ $ppjb->project }}</td></tr>
                <tr><td>Tanggal Dibutuhkan</td><td>: {{ $ppjb->tanggal_dibutuhkan }}</td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- TABEL BARANG --}}
<table class="mt-10">
    <thead>
        <tr class="text-center bold">
            <th>No</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Uraian / Spesifikasi Barang</th>
            <th>Estimasi Harga</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach ($ppjb->details as $i => $d)
        @php $total += $d->total; @endphp
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td class="text-center">{{ $d->qty }}</td>
            <td class="text-center">{{ $d->satuan }}</td>
            <td>{{ $d->uraian }}</td>
            <td class="text-right">{{ number_format($d->harga,0,',','.') }}</td>
            <td class="text-right">{{ number_format($d->total,0,',','.') }}</td>
            <td>{{ $d->keterangan }}</td>
        </tr>
        @endforeach
        <tr class="bold">
            <td colspan="5" class="text-right">Total</td>
            <td class="text-right">Rp. {{ number_format($total,0,',','.') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>

{{-- CATATAN --}}
<table class="mt-10">
    <tr>
        <td class="bold">CATATAN</td>
    </tr>
    <tr>
        <td>
            <ol style="margin:5px 0 5px 15px;">
                <li>Training dan Sertifikasi project</li>
                <li>Pengurusan dokumen work permit / perijinan</li>
                <li>CSMS, HSE Plan</li>
                <li>Meals dan transport bandara</li>
                <li>Transport lokal / pribadi</li>
                <li>Akomodasi (Tiket Pesawat, KA, Bis, Hotel, Taxi)</li>
                <li>Kalibrasi & Sertifikasi Peralatan Proyek</li>
            </ol>
        </td>
    </tr>
</table>

{{-- FOOTER --}}
<table class="no-border mt-20">
    <tr>
        <td width="65%"></td>
        <td class="text-center">
            Jakarta, {{ \Carbon\Carbon::parse($ppjb->tanggal_permohonan)->format('d F Y') }}<br>
            <b>Pemohon</b><br><br><br><br>
            ______________________
        </td>
    </tr>
    <tr>
    <td>Status PPJB</td>
    <td>: <b>{{ $ppjb->status }}</b></td>
</tr>

</table>

<table class="mt-10">
    <tr>
        <td style="font-size:10px;">
            &gt; Rp. 5 Juta Persetujuan Direktur Utama<br><br>
            Catatan I : Pada saat Manager Operasi sudah tanda tangan dan berkeyakinan penuh maka PCC harus mengalokasikan pada RPUM dan Form PPJB tetap di routing QA dan Direksi.<br>
            Catatan II : Bila Man Ops tidak ada dan dibutuhkan secepatnya, maka QA ambil alih kewenangan.<br>
            Catatan III : Bila semua pejabat diatas tidak ada maka Manager Marketing atau SE ambil kewenangan.
        </td>
    </tr>
    
</table>

</body>
</html>
