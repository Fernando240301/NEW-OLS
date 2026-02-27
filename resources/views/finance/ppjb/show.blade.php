@extends('adminlte::page')

@section('title', 'PPJB')

@section('content')

    <style>
        body {
            font-family: Tahoma;
            font-size: 13px;
        }

        .table-border td,
        .table-border th {
            border: 1px solid #000;
            padding: 4px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 15px;
        }
    </style>

    <div class="text-center mb-3">
        <img src="{{ asset('uploadfile/logo/logo_marindo.png') }}" width="60">
        <div class="title">PT. Marka Inspektindo Technical</div>
        <div class="subtitle">(MARINDOTECH)</div>
    </div>

    <div class="text-center mb-4">
        <b><u>PERMOHONAN PENGADAAN BARANG / JASA</u></b>
    </div>

    <table width="100%" class="mb-4">
        <tr>
            <td width="15%"><b>Kepada</b></td>
            <td width="35%">: {{ $ppjb->kepada ?? 'Direktur Utama' }}</td>
            <td width="15%"><b>No PPJB</b></td>
            <td width="35%">: {{ $ppjb->no_ppjb }}</td>
        </tr>
        <tr>
            <td><b>Dari</b></td>
            <td>: {{ $ppjb->dari }}</td>
            <td><b>Project</b></td>
            <td>: {{ $ppjb->project_no }}</td>
        </tr>
        <tr>
            <td><b>Tanggal</b></td>
            <td>: {{ \Carbon\Carbon::parse($ppjb->tanggal_permohonan)->translatedFormat('d F Y') }}</td>
            <td><b>Dibutuhkan</b></td>
            <td>: {{ \Carbon\Carbon::parse($ppjb->tanggal_dibutuhkan)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><b>Pekerjaan</b></td>
            <td colspan="3">: {{ $ppjb->pekerjaan }}</td>
        </tr>
        <tr>
            <td><b>PIC</b></td>
            <td colspan="3">: {{ $ppjb->pic }}</td>
        </tr>
    </table>

    <table width="100%" class="table-border">
        <thead>
            <tr align="center">
                <th width="5%">No</th>
                <th width="10%">Qty</th>
                <th width="10%">Satuan</th>
                <th width="30%">Uraian</th>
                <th width="15%">Harga</th>
                <th width="15%">Subtotal</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $grand = 0;
            @endphp
            @foreach ($ppjb->details as $detail)
                @php
                    $subtotal = $detail->qty * $detail->harga;
                    $grand += $subtotal;
                @endphp
                <tr>
                    <td align="center">{{ $no++ }}</td>
                    <td align="center">{{ $detail->qty }}</td>
                    <td align="center">{{ $detail->satuan }}</td>
                    <td>{{ $detail->uraian }}</td>
                    <td align="right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td>{{ $detail->keterangan }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" align="right"><b>Total</b></td>
                <td align="right"><b>{{ number_format($grand, 0, ',', '.') }}</b></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <table width="100%" class="table-border text-center">
        <tr>
            <th>Pemohon</th>
            <th>Manager</th>
            <th>Direktur</th>
        </tr>
        <tr height="120px">
            <td>
                @if ($ppjb->status != 'draft')
                    <img src="{{ asset('uploadfile/ttd/kosong.png') }}" width="100"><br>
                @endif
                {{ $ppjb->pic }}
            </td>
            <td>
                @if ($ppjb->status == 'approved')
                    <img src="{{ asset('uploadfile/ttd/kosong.png') }}" width="100"><br>
                @endif
                Manager
            </td>
            <td>
                @if ($ppjb->status == 'approved')
                    <img src="{{ asset('uploadfile/ttd/kosong.png') }}" width="100"><br>
                @endif
                Direktur
            </td>
        </tr>
    </table>

@stop
