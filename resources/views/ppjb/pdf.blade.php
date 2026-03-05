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

        td {
    border: 1px solid #000;
}

.approval-table td {
    vertical-align: bottom;
}

.diketahui-row td {
    height: 70px;
}

.disetujui-row td {
    height: 110px;
}


.approval-table .text-center {
    vertical-align: bottom;
}
    </style>
</head>
<body>

{{-- HEADER --}}
<table class="header-table">
    <tr>
        @php
    $path = public_path('images/logo.png');

    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } else {
        $base64 = null;
    }
@endphp

<td class="logo">
    @if($base64)
        <img src="{{ $base64 }}" width="70">
    @else
        LOGO NOT FOUND
    @endif
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
        <td class="bold">CATATAN :</td>
    </tr>
    <tr>
        <td style="border-top:none;">

            <table class="no-border" style="width:100%;">
                <tr>
                    <td width="50%" style="vertical-align:top;">
                        1. Training dan Sertifikasi project : 5101-001-01-01<br>
                        2. Pengurusan dokumen work permit / perijinan : 5101-001-01-02<br>
                        3. CSMS, HSE Plan : 5101-001-01-03<br>
                        4. Meals dan transport bandara : 5101-001-02-01<br>
                        5. Transport lokal / pribadi : 5101-001-02-02<br>
                        6. Akomodasi (Tiket Pesawat, KA, Bis, Hotel, Taxi) : 5101-001-02-03<br>
                        7. Kalibrasi & Sertifikasi Peralatan Proyek : 5101-001-02-04
                    </td>

                    <td width="50%" style="vertical-align:top;">
                        8. Material/Peralatan Habis dan APD : 5101-001-02-05<br>
                        9. SUB Agen (Subkon) : 5101-001-02-07<br>
                        10. Biaya Fotokopi & jilid report inspektor : 5101-001-03-02<br>
                        11. Biaya kantor & Alat kantor : 6101-001-02-01<br>
                        12. Iuran Web dan Server : 6101-001-04-01<br>
                        13. Asuransi / BPJS Kesehatan : 6101-001-06-10<br>
                        14. Perawatan Gedung dan Bangunan : 6101-001-02-02
                    </td>
                </tr>
            </table>

            <br><br>

            {{-- TTD PEMOHON --}}
            <div style="text-align:right;">
                Jakarta, {{ \Carbon\Carbon::parse($ppjb->tanggal_permohonan)->format('d F Y') }}<br>
                <b>Pemohon</b><br><br>

                @if($ppjb->pemohon_signature)
                    <img src="{{ public_path('storage/'.$ppjb->pemohon_signature) }}" height="60"><br>
                @else
                    <br><br><br>
                @endif

                <b>{{ $ppjb->pemohon_nama ?? '____________________' }}</b>
            </div>

        </td>
    </tr>
</table>
{{-- CATATAN TAMBAHAN --}}
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
    {{-- ✅ TAMBAHKAN DI SINI --}}
    @php
    $manager = $ppjb->approvals->where('level', 1)->where('is_approved', 1)->first();
    $qa = $ppjb->approvals->where('level', 2)->where('is_approved', 1)->first();
    $direktur = $ppjb->approvals->where('level', 3)->where('is_approved', 1)->first();
    $dirut = $ppjb->approvals->where('level', 4)->where('is_approved', 1)->first();
    @endphp


    {{-- APPROVAL SECTION --}}
<table class="mt-20 approval-table">

    {{-- HEADER DIKETAHUI --}}
    <tr>
        <td colspan="2" width="40%" class="text-center bold">
            Diketahui Oleh :
        </td>
        <td width="60%" class="bold">Catatan :</td>
    </tr>

    <tr class="diketahui-row">
<td width="20%" class="text-center">

@if($manager)

    @php
    $managerPath = public_path('images/signatures/manager.png');

    if (file_exists($managerPath)) {
        $type = pathinfo($managerPath, PATHINFO_EXTENSION);
        $data = file_get_contents($managerPath);
        $managerBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } else {
        $managerBase64 = null;
    }
@endphp


    @if($managerBase64)
    <img src="{{ $managerBase64 }}" height="50">
    <div style="font-size:9px;">
            {{ \Carbon\Carbon::parse($dirut->approved_at)->format('d-m-Y') }}
        </div>
@endif


@endif

<div class="bold">Manager</div>
</td>



        <td width="20%" class="text-center">
            @if($ppjb->manager2_signature)
                <img src="{{ public_path('images/signatures/manager.png') }}" height="50">
            @endif
            <div class="bold">Manager</div>
        </td>

        <td width="60%"></td>
    </tr>

    {{-- HEADER DISETUJUI --}}
    <tr>
        <td colspan="2" class="text-center bold">
            Disetujui Oleh :
        </td>
        <td class="bold">Catatan :</td>
    </tr>

    <tr class="disetujui-row">
        <td width="20%" class="text-center">

@if($direktur)

    @php
    $direkturPath = public_path('images/signatures/direktur.png');

    if (file_exists($direkturPath)) {
        $type = pathinfo($direkturPath, PATHINFO_EXTENSION);
        $data = file_get_contents($direkturPath);
        $direkturBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } else {
        $direkturBase64 = null;
    }
@endphp


    @if($direkturBase64)
    <img src="{{ $direkturBase64 }}" height="50">
    <div style="font-size:9px;">
            {{ \Carbon\Carbon::parse($dirut->approved_at)->format('d-m-Y') }}
        </div>
@endif
@endif

<div class="bold">Direktur</div>
        <td width="20%" class="text-center">

@if($dirut)

    @php
    $dirutPath = public_path('images/signatures/direkturutama.png');

    if (file_exists($dirutPath)) {
        $type = pathinfo($dirutPath, PATHINFO_EXTENSION);
        $data = file_get_contents($dirutPath);
        $dirutBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } else {
        $dirutBase64 = null;
    }
    @endphp

    @if($dirutBase64)
        <img src="{{ $dirutBase64 }}" height="50"><br>
        <div style="font-size:9px;">
            {{ \Carbon\Carbon::parse($dirut->approved_at)->format('d-m-Y') }}
        </div>
    @endif

@endif

<div class="bold">Direktur Utama</div>
</td>


        <td></td>
    </tr>

</table>

</body>
</html>
