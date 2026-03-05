<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>PPJB</title>

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Tahoma, sans-serif;
            font-size: 8.8pt;
            margin: 5px 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
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

        .title {
            font-size: 11pt;
            font-weight: bold;
            vertical-align: middle;
            padding: 10px;
        }

        .small {
            font-size: 9pt;
            vertical-align: middle;
        }

        .catatan {
            font-size: 8pt;
            vertical-align: middle;
        }

        /* ===== BORDER TABLE ===== */

        .table-border th,
        .table-border td {
            border: 1px solid #000;
            padding: 2px 4px;
        }

        .table-border th {
            font-weight: bold;
            text-align: center;
        }

        .table-border tr {
            height: 17px;
        }

        /* ===== INFO SECTION ===== */

        .info-table td {
            font-size: 9pt;
            padding: 2px 3px;
            line-height: 1.15;
        }

        .signature-box {
            position: relative;
            height: 100px;
            text-align: center;
        }

        .signature-box img {
            margin-top: 10px;
            max-height: 60px;
        }

        .signature-label {
            position: absolute;
            bottom: 5px;
            left: 0;
            right: 0;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @php
        function ttdFile($approval)
        {
            if (!$approval || !$approval->user) {
                return null;
            }

            $username = strtolower($approval->user->username);
            $file = public_path('uploadfile/ttd/ttd_' . $username . '.png');

            return file_exists($file) ? $file : null;
        }

        $picApproval = $lpjb->approvals->where('role', 'PIC')->first();
        $pccApproval = $lpjb->approvals->where('role', 'PCC')->first();
        $managerApproval = $lpjb->approvals->where('role', 'Manager')->first();
        $financeApproval = $lpjb->approvals->where('role', 'Finance')->first();
        $directorApproval = $lpjb->approvals->where('role', 'Director')->first();
    @endphp

    {{-- ================= HEADER ================= --}}
    <table class="table-border">
        <tr>
            <td width="15%" class="center">
                <img src="{{ public_path('uploadfile/logo/logo.png') }}" width="65">
            </td>
            <td width="60%" class="center title">
                PT MARKA INSPEKTINDO TECHNICAL<br>(MARINDOTECH)
            </td>
            <td width="25%" class="center small">
                MIT-F05-03<br>
                Revisi 02<br>
                18/04/2019
            </td>
        </tr>
        <tr>
            <td colspan="3" class="center title">
                LAPORAN PERTANGGUNG JAWABAN BARANG/JASA (LPBJ)
            </td>
        </tr>
    </table>

    {{-- ================= INFO SECTION ================= --}}
    <table class="info-table" style="margin-top:6px; margin-bottom:6px;">
        <tr>
            <td width="20%"><b>Kepada</b></td>
            <td width="2%">:</td>
            <td width="28%">Direktur Utama</td>

            <td width="20%"><b>No. PPJB</b></td>
            <td width="2%">:</td>
            <td width="28%">{{ $lpjb->ppjb->no_ppjb }}</td>
        </tr>

        <tr>
            <td><b>Dari</b></td>
            <td>:</td>
            <td>{{ $lpjb->ppjb->dari }}</td>

            <td><b>Refer to Project No</b></td>
            <td>:</td>
            <td></td>
        </tr>

        <tr>
            <td><b>Tanggal Permohonan</b></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($lpjb->tanggal_permohonan)->translatedFormat('d F Y') }}</td>

            <td><b>Tanggal Dibutuhkan</b></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($lpjb->tanggal_dibutuhkan)->translatedFormat('d F Y') }}</td>
        </tr>

        <tr>
            <td><b>No. Project</b></td>
            <td>:</td>
            <td colspan="4">{{ $lpjb->ppjb->project_no }}</td>
        </tr>

        <tr>
            <td><b>Pekerjaan</b></td>
            <td>:</td>
            <td colspan="4">{{ $lpjb->ppjb->pekerjaan }}</td>
        </tr>

        <tr>
            <td><b>PIC</b></td>
            <td>:</td>
            <td colspan="4">{{ $lpjb->ppjb->pic }}</td>
        </tr>
    </table>

    {{-- ================= DETAIL TABLE ================= --}}
    <table class="table-border">
        <thead>
            <tr>
                <th rowspan="2" style="font-size: 8pt;" width="4%">No</th>
                <th rowspan="2" style="font-size: 8pt;" width="22%">Uraian / Spesifikasi Barang</th>

                <th colspan="4" style="font-size: 8pt;" width="">Budget</th>
                <th colspan="4" style="font-size: 8pt;" width="31%">Realisasi</th>

                <th rowspan="2" style="font-size: 8pt;" width="12%">Kurang/Lebih Bayar</th>
            </tr>
            <tr>
                <th style="font-size: 8pt;" width="5%">qty</th>
                <th style="font-size: 8pt;" width="6%">satuan</th>
                <th style="font-size: 8pt;" width="8%">harga</th>
                <th style="font-size: 8pt;" width="12%">subtotal</th>

                <th style="font-size: 8pt;" width="5%">qty</th>
                <th style="font-size: 8pt;" width="6%">satuan</th>
                <th style="font-size: 8pt;" width="8%">harga</th>
                <th style="font-size: 8pt;" width="12%">subtotal</th>
            </tr>
        </thead>

        <tbody>
            @php
                $no = 1;
                $totalBudget = 0;
                $totalReal = 0;
            @endphp

            @foreach ($lpjb->details as $d)
                @php
                    $budgetQty = $d->budget_qty ?? 0;
                    $budgetHarga = $d->budget_harga ?? 0;
                    $budgetSubtotal = $d->budget_subtotal ?? 0;

                    $realQty = $d->real_qty ?? 0;
                    $realHarga = $d->real_harga ?? 0;
                    $realSubtotal = $d->real_subtotal ?? 0;

                    $totalBudget += $budgetSubtotal;
                    $totalReal += $realSubtotal;

                    $selisih = $budgetSubtotal - $realSubtotal;
                @endphp

                <tr>
                    <td style="font-size:8pt;" class="center">{{ $no++ }}</td>
                    <td style="font-size:8pt;">{{ $d->uraian }}</td>

                    {{-- ===== BUDGET ===== --}}
                    <td style="font-size:8pt;" class="center">
                        {{ $budgetQty > 0 ? number_format($budgetQty, 0, ',', '.') : '-' }}
                    </td>

                    <td style="font-size:8pt;" class="center">
                        {{ $budgetQty > 0 ? $d->ppjbDetail->satuan ?? '-' : '-' }}
                    </td>

                    <td style="font-size:8pt;" class="right">
                        {{ $budgetHarga > 0 ? number_format($budgetHarga, 0, ',', '.') : '-' }}
                    </td>

                    <td style="font-size:8pt;" class="right">
                        {{ $budgetSubtotal > 0 ? number_format($budgetSubtotal, 0, ',', '.') : '-' }}
                    </td>

                    {{-- ===== REALISASI ===== --}}
                    <td style="font-size:8pt;" class="center">
                        {{ $realQty > 0 ? number_format($realQty, 0, ',', '.') : '-' }}
                    </td>

                    <td style="font-size:8pt;" class="center">
                        {{ $realQty > 0 ? $d->satuan ?? '-' : '-' }}
                    </td>

                    <td style="font-size:8pt;" class="right">
                        {{ $realHarga > 0 ? number_format($realHarga, 0, ',', '.') : '-' }}
                    </td>

                    <td style="font-size:8pt;" class="right">
                        {{ $realSubtotal > 0 ? number_format($realSubtotal, 0, ',', '.') : '-' }}
                    </td>

                    {{-- ===== SELISIH ===== --}}
                    <td style="font-size:8pt;" class="right">
                        {{ $selisih != 0 ? number_format($selisih, 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @endforeach

            {{-- ===== TOTAL ===== --}}
            <tr>
                <td colspan="2" class="right bold" style="font-size:8pt;">&nbsp;</td>
                <td colspan="3" class="right bold" style="font-size:8pt;">Total</td>

                <td class="right bold" style="font-size:8pt;">
                    {{ $totalBudget > 0 ? number_format($totalBudget, 0, ',', '.') : '-' }}
                </td>

                <td colspan="3" class="right bold" style="font-size:8pt;"></td>

                <td class="right bold" style="font-size:8pt;">
                    {{ $totalReal > 0 ? number_format($totalReal, 0, ',', '.') : '-' }}
                </td>

                <td class="right bold" style="font-size:8pt;">
                    {{ $totalBudget - $totalReal != 0 ? number_format($totalBudget - $totalReal, 0, ',', '.') : '-' }}
                </td>
            </tr>

        </tbody>
    </table>

    <table class="table-border">
        <tr>
            <td class="catatan" width="2%"
                style="padding-top: 5px; padding-bottom: 5px; border-right: none; border-bottom: none;">
                &nbsp;</td>
            <td class="catatan" width="60%"
                style="padding-top: 5px; padding-bottom: 5px; border-left: none; border-bottom: none; border-right: none">
                <b>CATATAN :</b>
            </td>
            <td class="catatan" width="36%"
                style="padding-top: 5px; padding-bottom: 5px; border-right: none; border-left: none; border-bottom: none;">
                &nbsp;</td>
            <td class="catatan" width="2%"
                style="padding-top: 5px; padding-bottom: 5px; border-left: none; border-bottom: none;">
                &nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" style="border-top: none; border-right: none;" colspan="2">
                &nbsp;</td>
            <td class="catatan"
                style="border-top: none; border-left: none; border-right: none; font-size: 10pt; padding: 10pt; text-align: center;">
                Jakarta, {{ \Carbon\Carbon::parse($lpjb->tanggal_permohonan)->translatedFormat('d F Y') }}
                <br><b>Pemohon</b><br>
                @if ($file = ttdFile($picApproval))
                    <img src="{{ $file }}" width="120"><br>
                @else
                    <br><br><br>
                @endif
                <u>{{ $picApproval->user->fullname ?? '' }}</u>
            </td>
            <td class="catatan" style="border-top: none; border-left: none;">
                &nbsp;</td>
        </tr>
    </table>

    <table class="table-border">
        <tr>
            <td colspan="3" class="center bold" height="20px" style="font-size: 11pt; vertical-align: middle;">
                Diperiksa oleh :
            </td>
        </tr>
        <tr>
            <td width="33%" class="center bold">
                <div style="height:70px; vertical-align:middle;">
                    @if ($file = ttdFile($pccApproval))
                        <img src="{{ $file }}" width="100">
                    @endif
                </div>
                <div style="border-top:1px solid #000; margin-top:5px; font-size: 11pt;">
                    <b>Project Cost Control</b>
                </div>
            </td>
            <td width="33%" class="center bold">
                <div style="height:70px; vertical-align:middle;">
                    @if ($file = ttdFile($managerApproval))
                        <img src="{{ $file }}" width="100">
                    @endif
                </div>
                <div style="border-top:1px solid #000; margin-top:5px; font-size: 11pt;">
                    <b>Manager</b>
                </div>
            </td>
            <td width="34%" class="center bold">
                <div style="height:70px; vertical-align:middle;">
                    @if ($file = ttdFile($financeApproval))
                        <img src="{{ $file }}" width="100">
                    @endif
                </div>
                <div style="border-top:1px solid #000; margin-top:5px; font-size: 11pt;">
                    <b>Manager Keuangan</b>
                </div>
            </td>
        </tr>
    </table>

    @php
        use Milon\Barcode\Facades\DNS2DFacade as DNS2D;

        $directorApproval = $lpjb->approvals->where('role', 'Director')->where('status', 'approved')->first();

        $dirUtamaQR = null;
        $dirQR = null;

        if ($directorApproval) {
            $qrContent =
                "LPJB: {$lpjb->no_lpjb}\n" .
                "Approved By: {$directorApproval->user->fullname}\n" .
                "User ID: {$directorApproval->user_id}\n" .
                "Tanggal: {$directorApproval->approved_at}";

            if ($directorApproval->user_id == 100219) {
                $dirUtamaQR = DNS2D::getBarcodePNG($qrContent, 'QRCODE');
            }

            if ($directorApproval->user_id == 100026) {
                $dirQR = DNS2D::getBarcodePNG($qrContent, 'QRCODE');
            }
        }
    @endphp
    <table class="table-border">
        <tr>
            <td colspan="2" class="center bold" height="20px" style="font-size: 11pt; vertical-align: middle;">
                Disetujui oleh :
            </td>
        </tr>
        <tr>
            <td width="50%" class="center bold">
                <div style="height:70px; vertical-align:middle;">
                    @if ($dirQR)
                        <img src="data:image/png;base64,{{ $dirQR }}" width="70">
                    @endif
                </div>
                <div style="border-top:1px solid #000; margin-top:5px; font-size: 11pt;">
                    <b>Direktur</b>
                </div>
            </td>
            <td width="50%" class="center bold">
                <div style="height:70px; vertical-align:middle;">
                    @if ($dirUtamaQR)
                        <img src="data:image/png;base64,{{ $dirUtamaQR }}" width="70">
                    @endif
                </div>
                <div style="border-top:1px solid #000; margin-top:5px; font-size: 11pt;">
                    <b>Direktur Utama</b>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>
