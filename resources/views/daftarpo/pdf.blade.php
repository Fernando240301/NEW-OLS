<!DOCTYPE html>
<html>
<head>
    <title>Preview PO</title>

    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .container {
            width: 100%; /* ✅ FIX biar tidak kepotong */
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

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        .no-border-all td {
            border: none;
        }

        img {
            max-width: 100%;
        }

    </style>
</head>
<body>

<div class="container">

    {{-- HEADER --}}
    <table>
        <tr>
            <td style="width:15%" class="center">
                <!-- ✅ FIX LOGO -->
                <img src="{{ public_path('logo.png') }}" height="60">
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
                PO Number : {{ $po->no_po }}
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
            <td>Ship To : {{ $po->ship_to }}</td>
        </tr>
        <tr>
            <td>
                Ship Date :
                {{ $po->ship_date ? \Carbon\Carbon::parse($po->ship_date)->format('d F Y') : '-' }}
            </td>
        </tr>
    </table>

    <br>

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
                <th style="width:15%">Unit Price</th>
                <th style="width:15%">Total</th>
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
                Note : Pembayaran akan kami proses setelah pekerjaan selesai dan invoice asli diterima maksimal 30 hari kerja
            </td>
        </tr>
    </table>

    <br>

    {{-- SIGNATURE --}}
    <table style="margin-top:20px;">
        <tr>

            {{-- MARKETING --}}
            <td class="center" style="height:140px;">
                <b>Submitted by</b><br><br>

                @if($po->marketing_status == 'approved')
                    <img src="{{ public_path('ttd/marketing.png') }}" height="60"><br>
                @elseif($po->marketing_status == 'rejected')
                    <span style="color:red;">REJECTED</span><br>
                @else
                    <br><br>
                @endif

                <b>Marketing Manager</b><br>
                {{ $po->marketing_manager ?? $po->nama_pengaju }}
            </td>

            {{-- FINANCE --}}
            <td class="center">
                <b>Known by</b><br><br>

                @if($po->finance_status == 'approved')
                    <img src="{{ public_path('ttd/finance.png') }}" height="60"><br>
                @elseif($po->finance_status == 'rejected')
                    <span style="color:red;">REJECTED</span><br>
                @else
                    <br><br>
                @endif

                <b>Finance Manager</b><br>
                {{ $po->finance_manager ?? '-' }}
            </td>

            {{-- DIREKTUR --}}
            <td class="center">
                <b>Approved by</b><br><br>

                @if($po->direktur_status == 'approved')
                    <img src="{{ public_path('ttd/direktur.png') }}" height="60"><br>
                @elseif($po->direktur_status == 'rejected')
                    <span style="color:red;">REJECTED</span><br>
                @else
                    <br><br>
                @endif

                <b>President Director</b><br>
                {{ $po->direktur_utama ?? '-' }}
            </td>

            {{-- CLIENT --}}
            <td class="center">
                <b>Approved by</b><br><br><br>

                <b>{{ strtoupper($po->to) }}</b>
            </td>

        </tr>
    </table>

</div>

</body>
</html>