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
                MIT-F05-02a<br>
                Revisi 02<br>
                28-04-2025
            </td>
        </tr>
        <tr>
            <td colspan="3" class="center title">
                PERMOHONAN PENGADAAN BARANG/JASA
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
            <td width="28%">{{ $ppjb->no_ppjb }}</td>
        </tr>

        <tr>
            <td><b>Dari</b></td>
            <td>:</td>
            <td>{{ $ppjb->dari }}</td>

            <td><b>Refer to Project No</b></td>
            <td>:</td>
            <td>-</td>
        </tr>

        <tr>
            <td><b>Tanggal Permohonan</b></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($ppjb->tanggal_permohonan)->translatedFormat('d F Y') }}</td>

            <td><b>Tanggal Dibutuhkan</b></td>
            <td>:</td>
            <td>
                @if ($ppjb->tanggal_mulai == $ppjb->tanggal_selesai)
                    {{ \Carbon\Carbon::parse($ppjb->tanggal_mulai)->translatedFormat('d F Y') }}
                @else
                    {{ \Carbon\Carbon::parse($ppjb->tanggal_mulai)->translatedFormat('d F Y') }}
                    s.d
                    {{ \Carbon\Carbon::parse($ppjb->tanggal_selesai)->translatedFormat('d F Y') }}
                @endif
            </td>
        </tr>

        <tr>
            <td><b>No. Project</b></td>
            <td>:</td>
            <td colspan="4">
                @php
                    $noProject = null;

                    if ($ppjb->workflow_id) {
                        $workflow = DB::table('app_workflow')->where('workflowid', $ppjb->workflow_id)->first();

                        if ($workflow) {
                            $pr = DB::table('app_workflow')->where('workflowid', $workflow->nworkflowid)->first();

                            if ($pr) {
                                $prData = json_decode($pr->workflowdata, true);
                                $noProject = $prData['project_number'] ?? ($pr->projectname ?? null);
                            }
                        }
                    }
                @endphp

                {{ $noProject. ' | ' . $pr->projectname ?? '-' }}
            </td>
        </tr>

        <tr>
            <td><b>Pekerjaan</b></td>
            <td>:</td>
            <td colspan="4">{{ $ppjb->pekerjaan }}</td>
        </tr>

        <tr>
            <td><b>PIC</b></td>
            <td>:</td>
            <td colspan="4">{{ $ppjb->pic }}</td>
        </tr>
    </table>

    {{-- ================= DETAIL TABLE ================= --}}
    <table class="table-border">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th width="5%" rowspan="2">QTY</th>
                <th width="8%" rowspan="2">Satuan</th>
                <th width="27%" rowspan="2">Uraian / Spesifikasi Barang</th>
                <th width="24%" colspan="2">Estimasi Harga Barang</th>
                <th width="22%" rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th width="10%">Harga</th>
                <th width="14%">Jumlah</th>
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
                    <td class="center">{{ $no++ }}</td>
                    <td class="center">{{ $detail->qty }}</td>
                    <td class="center">{{ $detail->satuan }}</td>
                    <td>{{ $detail->uraian }}</td>
                    <td class="right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td class="center">{{ $detail->keterangan }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4"></td>
                <td class="center bold">Total</td>
                <td class="right bold">Rp. {{ number_format($grand, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="table-border">
        <tr>
            <td class="catatan" style="padding-top: 5px; padding-bottom: 5px; border-right: none; border-bottom: none;">
                &nbsp;</td>
            <td class="catatan"
                style="padding-top: 5px; padding-bottom: 5px; border-left: none; border-bottom: none; border-right: none"
                colspan="4"><b>CATATAN :</b></td>
            <td class="catatan" style="padding-top: 5px; padding-bottom: 5px; border-left: none; border-bottom: none;">
                &nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" style="border-bottom: none; border-top: none; border-right: none;" width="2%">&nbsp;
            </td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none"
                width="35%">1. Training dan Sertifikasi project</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none"
                width="13%">: 5101-001-01-01</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none"
                width="35%">8. &nbsp;&nbsp;Material/Peralatan Habis dan APD</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none"
                width="13%">: 5101-001-02-05</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none;" width="2%">&nbsp;
            </td>
        </tr>
        <tr>
            <td class="catatan" style="border-bottom: none; border-top: none; border-right: none;">&nbsp;</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">2.
                Pengurusan dokumen work permit / perijinan </td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                5101-001-01-02</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">9.
                &nbsp;&nbsp;SUB Agen (Subkon)</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                5101-001-02-07</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none;">&nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" style="border-bottom: none; border-top: none; border-right: none;">&nbsp;</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">3.
                CSMS, HSE Plan</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                5101-001-01-03</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">10.
                Biaya Fotokopi & jilid report inspektor </td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                5101-001-03-02</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none;">&nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" style="border-bottom: none; border-top: none; border-right: none;">&nbsp;</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">
                4.
                Meals dan transport bandara</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                5101-001-02-01</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">
                11. Biaya kantor & Alat kantor</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                6101-001-02-01</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none;">&nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" style="border-bottom: none; border-top: none; border-right: none;">&nbsp;</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">
                5. Transport lokal / pribadi</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                5101-001-02-02</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">
                12. Iuran Web dan Server</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                6101-001-04-01</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none;">&nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" style="border-bottom: none; border-top: none; border-right: none;">&nbsp;</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">
                6. Akomodasi (Tiket Pesawat, KA, Bis, Hotel, Taxi) </td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                5101-001-02-03</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">
                13. Asuransi / BPJS Kesehatan</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                6101-001-06-10</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none;">&nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" style="border-bottom: none; border-top: none; border-right: none;">&nbsp;</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">
                7. Kalibrasi & Sertifikasi Peralatan Proyek</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                5101-001-02-04</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">
                14. Perawatan Gedung dan Bangunan</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none; border-right: none">:
                6101-001-02-02</td>
            <td class="catatan" style="border-bottom: none; border-top: none; border-left: none;">&nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" style="border-top: none; border-right: none;" colspan="3">
                &nbsp;</td>
            <td class="catatan"
                style="border-top: none; border-left: none; font-size: 10pt; padding: 10pt; text-align: center;"
                colspan="3">
                Jakarta, {{ \Carbon\Carbon::parse($ppjb->tanggal_permohonan)->translatedFormat('d F Y') }}
                @php
                    $picApproval = $ppjb->approvals->where('role', 'PIC')->where('status', 'approved')->first();

                    $picFile = null;

                    if ($picApproval && $picApproval->user) {
                        $username = strtolower($picApproval->user->username);
                        $picFile = public_path('uploadfile/ttd/ttd_' . $username . '.png');
                    }
                @endphp

                <br><b>Pemohon</b><br>

                @if ($picFile && file_exists($picFile))
                    <img src="{{ $picFile }}" width="120"><br>
                @else
                    <br><br><br>
                @endif

                <u>
                    {{ $picApproval->user->fullname ?? '' }}
                </u>
            </td>
        </tr>
    </table>

    <table class="table-border">
        <tr>
            <td class="catatan"
                style="padding-top: 5px; padding-bottom: 5px; border-right: none; border-bottom: none;">
                &nbsp;</td>
            <td class="catatan"
                style="padding-top: 10px; padding-bottom: 10px; border-left: none; border-bottom: none; border-right: none"
                colspan="2">
                > Rp. 5 Juta Persetujuan Direktur Utama</td>
            <td class="catatan"
                style="padding-top: 5px; padding-bottom: 5px; border-left: none; border-bottom: none;">
                &nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" width="2%"
                style=" vertical-align: top; border-right: none; border-top: none; border-bottom: none;">
                &nbsp;</td>
            <td class="catatan" width="10%"
                style=" vertical-align: top; border-left: none; border-top: none; border-bottom: none; border-right: none">
                Catatan I :</td>
            <td class="catatan" width="86%"
                style=" vertical-align: top; border-left: none; border-top: none; border-bottom: none; border-right: none; text-align: justify;">
                ada saat Manager Operasi sudah tandatangan, dan berkeyakinan penuh, maka PCC harus mengalokasikan
                pada RPUM dan Form PPBJ tetap di routing ke QA dan Direksi dan dana sudah dapat dicairkan ke pemohon.
                Bila
                Man Ops tidak ada dan dana dibutuhkan secepatnya, maka QA ambil alih kewenangan
            </td>
            <td class="catatan" width="2%"
                style=" vertical-align: top; border-left: none; border-top: none; border-bottom: none;">
                &nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" width="2%"
                style=" vertical-align: top; border-right: none; border-top: none; border-bottom: none;">
                &nbsp;</td>
            <td class="catatan" width="10%"
                style=" vertical-align: top; border-left: none; border-top: none; border-bottom: none; border-right: none">
                Catatan II :</td>
            <td class="catatan" width="86%"
                style=" vertical-align: top; border-left: none; border-top: none; border-bottom: none; border-right: none; text-align: justify;">
                Bila terjadi deviasi terhadap RAB - Ops maka QA menyampaikan justifikasinya dan berwenang menyetujui
                atau
                menolak deviasi. Bila Man Ops dan QA tdk ada, maka Direksi ambil alih kewenangan
            </td>
            <td class="catatan" width="2%"
                style=" vertical-align: top; border-left: none; border-top: none; border-bottom: none;">
                &nbsp;</td>
        </tr>
        <tr>
            <td class="catatan" width="2%" style=" vertical-align: top; border-right: none; border-top: none;">
                &nbsp;</td>
            <td class="catatan" width="10%"
                style=" vertical-align: top; border-left: none; border-top: none; border-right: none; padding-bottom: 10px;">
                Catatan III :</td>
            <td class="catatan" width="86%"
                style=" vertical-align: top; border-left: none; border-top: none; border-right: none; text-align: justify;">
                Bila semua pejabat diatas tidak ada maka Manager Marketing atau SE ambil kewenangan.
            </td>
            <td class="catatan" width="2%" style=" vertical-align: top; border-left: none; border-top: none;">
                &nbsp;</td>
        </tr>
    </table>
    <table class="table-border">

        <!-- HEADER DIKETAHUI -->
        <tr>
            <td colspan="2" class="center bold" width="35%">Diketahui Oleh :</td>
            <td class="bold" width="65%" style="border-bottom: none;">Catatan :</td>
        </tr>

        <!-- ISI DIKETAHUI -->
        @php
            $managerApproval = $ppjb->approvals->where('role', 'Manager')->where('status', 'approved')->first();

            $managerFile = null;

            if ($managerApproval && $managerApproval->user) {
                $username = strtolower($managerApproval->user->username);
                $managerFile = public_path('uploadfile/ttd/ttd_' . $username . '.png');
            }
        @endphp

        @php
            $financeApproval = $ppjb->approvals->where('role', 'Finance')->where('status', 'approved')->first();

            $financeFile = null;

            if ($financeApproval && $financeApproval->user) {
                $username = strtolower($financeApproval->user->username);
                $financeFile = public_path('uploadfile/ttd/ttd_' . $username . '.png');
            }
        @endphp

        <tr>
            <td width="17.5%" style="height:100px; text-align:center; vertical-align:bottom;">
                <div style="height:70px;">
                    @if ($managerFile && file_exists($managerFile))
                        <img src="{{ $managerFile }}" width="90">
                    @endif
                </div>
                <div style="border-top:1px solid #000; margin-top:5px;">
                    <b>Manager</b>
                </div>
            </td>

            <td width="17.5%" style="height:100px; text-align:center; vertical-align:bottom;">
                <div style="height:70px;">
                    @if ($financeFile && file_exists($financeFile))
                        <img src="{{ $financeFile }}" width="90">
                    @endif
                </div>
                <div style="border-top:1px solid #000; margin-top:5px;">
                    <b>Manager</b>
                </div>
            </td>

            <td style="border-top: none;"></td>
        </tr>

        <!-- HEADER DISETUJUI -->
        <tr>
            <td colspan="2" class="center bold">Disetujui Oleh :</td>
            <td class="bold" style="border-bottom: none;">Catatan :</td>
        </tr>

        <!-- ISI DISETUJUI -->
        @php
            use Milon\Barcode\Facades\DNS2DFacade as DNS2D;

            $directorApproval = $ppjb->approvals->where('role', 'Director')->where('status', 'approved')->first();

            $dirUtamaQR = null;
            $dirQR = null;

            if ($directorApproval) {
                $qrContent =
                    "PPJB: {$ppjb->no_ppjb}\n" .
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
        <tr>

            {{-- DIREKTUR --}}
            <td style="height:110px; text-align:center; vertical-align:bottom;">

                <div style="height:80px;">
                    @if ($dirQR)
                        <img src="data:image/png;base64,{{ $dirQR }}" width="80">
                    @endif
                </div>

                <div style="border-top:1px solid #000; margin-top:5px;">
                    <b>Direktur</b>
                </div>

            </td>

            {{-- DIREKTUR UTAMA --}}
            <td style="height:110px; text-align:center; vertical-align:bottom;">

                <div style="height:80px;">
                    @if ($dirUtamaQR)
                        <img src="data:image/png;base64,{{ $dirUtamaQR }}" width="80">
                    @endif
                </div>

                <div style="border-top:1px solid #000; margin-top:5px;">
                    <b>Direktur Utama</b>
                </div>

            </td>

            <td style="border-top:none;"></td>

        </tr>

    </table>

</body>

</html>
