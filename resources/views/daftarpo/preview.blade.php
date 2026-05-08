<!DOCTYPE html>
<html>
<head>
    <title>Preview PO</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .container {
            width: 900px;
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border: 1px solid #000;
            padding: 5px;
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

        .header-table td {
            vertical-align: middle;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        .no-border-all td {
            border: none;
        }

        .signature td {
            height: 100px;
            vertical-align: bottom;
            text-align: center;
        }

    </style>
</head>
<body>

<div class="container">

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td style="width:15%" class="center">
                <img src="{{ asset('logo.png') }}" height="60">
            </td>
            <td class="center bold">
                PT. Marka Inspektindo Technical<br>
                (MARINDOTECH)
            </td>
            <td style="width:20%" class="center">
                MIT-F05-06<br>
                Revisi 02<br>
                24-05-2018
            </td>
        </tr>
    </table>

    {{-- TITLE --}}
    <table>
        <tr>
            <td class="title">PURCHASE ORDER</td>
        </tr>
    </table>

    {{-- INFO --}}
    <table class="no-border-all">
        <tr>
            <td style="width:50%">
                To : {{ $po->to }} <br>
                {{ $po->address }}
            </td>

            <td>
                PO. Number : {{ $po->no_po }}
            </td>
        </tr>

        <tr>
            <td>
                Attention : {{ $po->attention ?? '-' }}
            </td>

            <td>
                Date : {{ \Carbon\Carbon::parse($po->date)->format('d F Y') }}
            </td>
        </tr>
    </table>

    {{-- SHIP --}}
    <table>
        <tr>
            <td>
                Ship To : {{ $po->ship_to }}
            </td>
        </tr>
        <tr>
            <td>
                Ship Date : 
                {{ $po->ship_date ? \Carbon\Carbon::parse($po->ship_date)->format('d F Y') : '-' }}
            </td>
        </tr>
    </table>

    <br>

    {{-- REF --}}
    <div class="center">
        Ref. Surat Penawaran No. {{ $po->pr_number ?? '-' }}
    </div>

    {{-- TABLE ITEM --}}
    <table>
        <thead class="center bold">
            <tr>
                <th style="width:5%">No</th>
                <th>Description</th>
                <th style="width:8%">QTY</th>
                <th style="width:10%">UNIT</th>
                <th style="width:15%">Unit Price (IDR)</th>
                <th style="width:15%">Total (IDR)</th>
            </tr>
        </thead>

        <tbody>
            @php
                $subtotal = 0;
                $items = json_decode($po->description, true) ?? [];
            @endphp

            @forelse($items as $i => $item)
                @php
                    $total = $item['qty'] * $item['price'];
                    $subtotal += $total;
                @endphp
                <tr>
                    <td class="center">{{ $i+1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="center">{{ $item['qty'] }}</td>
                    <td class="center">{{ $item['unit'] }}</td>
                    <td class="right">{{ number_format($item['price'],0,',','.') }}</td>
                    <td class="right">{{ number_format($total,0,',','.') }}</td>
                </tr>
            @empty
                <tr>
                    <td class="center">1</td>
                    <td>{{ $po->description }}</td>
                    <td class="center">{{ $po->qty }}</td>
                    <td class="center">{{ $po->unit }}</td>
                    <td class="right">{{ number_format($po->unit_price,0,',','.') }}</td>
                    <td class="right">{{ number_format($po->qty * $po->unit_price,0,',','.') }}</td>
                </tr>
                @php
                    $subtotal = $po->qty * $po->unit_price;
                @endphp
            @endforelse
        </tbody>
    </table>

    {{-- TOTAL --}}
    @php
        $ppn = $subtotal * 0.11;
        $grand = $subtotal + $ppn;
    @endphp

    <table class="no-border">
        <tr>
            <td style="width:70%"></td>
            <td>
                Sub Total : {{ number_format($subtotal,0,',','.') }}<br>
                PPn 11% : {{ number_format($ppn,0,',','.') }}<br>
                <b>Total : {{ number_format($grand,0,',','.') }}</b>
            </td>
        </tr>
    </table>

    {{-- NOTE --}}
    <table>
        <tr>
            <td>
                Note : Pembayaran akan kami proses setelah pekerjaan selesai dan invoice asli, alat, dan sertifikat kalibrasi diterima maksimal 30 hari kerja
            </td>
        </tr>
    </table>

    <br>

  {{-- SIGNATURE --}}
<table style="width:100%; border-collapse: collapse; margin-top:20px;">
    <tr>

        {{-- MARKETING --}}
        <td style="border:1px solid #000; text-align:center; width:25%; height:160px; vertical-align:bottom;">
            <b>Submitted by</b><br><br>

            <div style="height:70px;">
                @if($po->marketing_status == 'approved')
                    <img src="{{ public_path('ttd/marketing.png') }}" height="60">
                @elseif($po->marketing_status == 'rejected')
                    <span style="color:red;">REJECTED</span>
                @endif
            </div>

            <b>Marketing Manager</b><br>
            {{ $po->marketing_manager ?? $po->nama_pengaju }}
        </td>


        {{-- FINANCE --}}
        <td style="border:1px solid #000; text-align:center; width:25%; height:160px; vertical-align:bottom;">
            <b>Known by</b><br><br>

            <div style="height:70px;">
                @if($po->finance_status == 'approved')
                    <img src="{{ public_path('ttd/finance.png') }}" height="60">
                @elseif($po->finance_status == 'rejected')
                    <span style="color:red;">REJECTED</span>
                @endif
            </div>

            <b>Finance Manager</b><br>
            {{ $po->finance_manager ?? '-' }}
        </td>


        {{-- DIREKTUR --}}
        <td style="border:1px solid #000; text-align:center; width:25%; height:160px; vertical-align:bottom;">
            <b>Approved by</b><br><br>

            <div style="height:70px;">
                @if($po->direktur_status == 'approved')
                    <img src="{{ public_path('ttd/direktur.png') }}" height="60">
                @elseif($po->direktur_status == 'rejected')
                    <span style="color:red;">REJECTED</span>
                @endif
            </div>

            <b>President Director</b><br>
            {{ $po->direktur_utama ?? '-' }}
        </td>


        {{-- CLIENT --}}
        <td style="border:1px solid #000; text-align:center; width:25%; height:160px; vertical-align:bottom;">
            <b>Approved by</b><br><br>

            <div style="height:70px;">
                {{-- kosong / bisa isi ttd client nanti --}}
            </div>

            <b>{{ strtoupper($po->to) }}</b>
        </td>

    </tr>
</table>

</body>
</html>