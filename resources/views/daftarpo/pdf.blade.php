<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .bordered {
            border: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .no-border td {
            border: none;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .header-box td {
            border: 1px solid #000;
            padding: 10px;
        }

        .note-box {
            border: 1px solid #000;
            padding: 8px;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<table class="header-box">
    <tr>
        <td width="20%" class="center">
            @if(isset($base64) && $base64)
                <img src="{{ $base64 }}" width="80" style="display:block; margin:0 auto;">
            @endif
        </td>

        <td width="60%" class="center">
            <div style="font-size:16px; font-weight:bold;">
                PT. Marka Inspektindo Technical
            </div>
            <div style="font-size:14px;">
                (MARINDOTECH)
            </div>
        </td>

        <td width="20%" class="center" style="font-size:11px;">
            MIT-F05-06<br>
            Revisi 02<br>
            24-05-2018
        </td>
    </tr>
</table>

<br>

<table>
    <tr>
        <td class="center" style="font-size:18px; font-weight:bold; padding:10px;">
            PURCHASE ORDER
        </td>
    </tr>
</table>

<br>

<!-- INFO -->
<br><br>



<!-- REF -->
<table class="no-border">
    <tr>
        <td class="center">
            Ref Penawaran No. {{ $daftarpo->project }}
        </td>
    </tr>
</table>

<br>

<!-- DETAIL TABLE -->
<table>
    <thead>
        <tr class="center bold">
            <th width="5%">No</th>
            <th width="35%">Description</th>
            <th width="10%">QTY</th>
            <th width="10%">UNIT</th>
            <th width="20%">Unit Price (IDR)</th>
            <th width="20%">Total (IDR)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="center">1</td>
            <td>
                {{ $daftarpo->description }}
            </td>
            <td class="center">{{ $daftarpo->qty }}</td>
            <td class="center">{{ $daftarpo->unit }}</td>
            <td class="right">
                {{ number_format($daftarpo->harga,0,',','.') }}
            </td>
            <td class="right">
                {{ number_format($daftarpo->harga * $daftarpo->qty,0,',','.') }}
            </td>
        </tr>
    </tbody>
</table>

@php
    $subtotal = $daftarpo->harga * $daftarpo->qty;
    $ppn = $subtotal * 0.11;
    $total = $subtotal + $ppn;
@endphp

<br>

<!-- TOTAL -->
<table>
    <tr>
        <td width="60%" rowspan="3"></td>
        <td width="20%">Sub Total</td>
        <td width="20%" class="right">
            {{ number_format($subtotal,0,',','.') }}
        </td>
    </tr>
    <tr>
        <td>PPn 11%</td>
        <td class="right">
            {{ number_format($ppn,0,',','.') }}
        </td>
    </tr>
    <tr>
        <td class="bold">Total</td>
        <td class="right bold">
            {{ number_format($total,0,',','.') }}
        </td>
    </tr>
</table>

<br>

<!-- NOTE -->
<div class="note-box">
    <strong>Note :</strong>
    <ul>
        <li>Invoice diterbitkan sesuai dengan timesheet setelah laporan diterima</li>
        <li>Pembayaran dilakukan setelah invoice dan faktur pajak diterima maksimal 45 hari kerja</li>
    </ul>
</div>
<br><br>
@php
    $submitted = $daftarpo->approvals->where('level',1)->first();
    $known     = $daftarpo->approvals->where('level',3)->first();
    $director  = $daftarpo->approvals->where('level',4)->first();
@endphp
<table style="width:100%; border-collapse: collapse; text-align:center; margin-top:20px;">
    <tr>

        {{-- Submitted --}}
        <td width="25%" style="border:1px solid #000; padding:15px;">
            Submitted by<br><br>

            @if($submitted && $submitted->is_approved && $submitted->signature)
                <img src="{{ $submitted->signature }}" height="60"><br>
            @else
                <br><br><br>
            @endif

            <strong>Operations Director</strong><br>
            Alby Diantono
        </td>

        {{-- Known --}}
        <td width="25%" style="border:1px solid #000; padding:15px;">
            Known by<br><br>

            @if($known && $known->is_approved && $known->signature)
                <img src="{{ $known->signature }}" height="60"><br>
            @else
                <br><br><br>
            @endif

            <strong>Finance Manager</strong><br>
            Alfitri Tunjung
        </td>

        {{-- Director --}}
        <td width="25%" style="border:1px solid #000; padding:15px;">
            Approved by<br><br>

            @if($director && $director->is_approved && $director->signature)
                <img src="{{ $director->signature }}" height="60"><br>
            @else
                <br><br><br>
            @endif

            <strong>President Director</strong><br>
            M. Nuzul Purwiyanto
        </td>

        {{-- Vendor --}}
        <td width="25%" style="border:1px solid #000; padding:15px;">
            Approved by<br><br><br>
            {{ $daftarpo->to }}
        </td>

    </tr>
</table>
</body>
</html>
