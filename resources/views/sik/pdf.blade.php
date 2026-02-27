<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: Tahoma, sans-serif;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            border: 1px solid #000;
            vertical-align: top;
            padding: 6px;
        }

        .center {
            text-align: center;
            vertical-align: middle;
        }

        .bold {
            font-weight: bold;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
        }

        .small {
            font-size: 12pt;
        }

        .isi {
            font-size: 11pt;
            font-weight: bold;
            height: 10px;
            vertical-align: middle;
        }

        .ttd {
            font-size: 11pt;
            font-weight: bold;
            height: 10px;
            text-align: center;
            vertical-align: middle;
        }

        .project {
            font-size: 11pt;
            font-weight: bold;
            vertical-align: top;
            height: 40px;
            text-align: center;
        }

        .client {
            font-size: 11pt;
            vertical-align: middle;
        }

        .sow {
            font-size: 11pt;
            vertical-align: top;
        }

        .detail-client {
            font-size: 11pt;
            vertical-align: top;
        }

        .signature img {
            width: 120px;
        }

        .ttd-box {
            text-align: center;
            vertical-align: top;
            height: 120px;
            padding-top: 8px;
        }

        .ttd-title {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .ttd-img {
            height: 70px;
            margin: 8px 0;
        }

        .ttd-name {
            font-size: 10pt;
            font-weight: bold;
        }

        .ttd-jabatan {
            font-size: 9pt;
        }

        .tbl-peralatan {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .tbl-peralatan td,
        .tbl-peralatan th {
            border: 1px solid #000;
            padding: 0px;
        }

        .tbl-header {
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .tbl-title {
            font-style: italic;
            font-weight: bold;
            text-align: left;
            background-color: #f2f2f2;
        }

        .tbl-center {
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- ================= HEADER ================= --}}
    <table>
        <tr>
            <td width="15%" class="center">
                <img src="{{ public_path('uploadfile/logo/logo.png') }}" width="80">
            </td>
            <td width="60%" class="center title">
                PT MARKA INSPEKTINDO TECHNICAL<br>(MARINDOTECH)
            </td>
            <td width="25%" class="center small">
                MIT-F18-03<br>
                Revisi 00<br>
                24-07-2019
            </td>
        </tr>
        <tr>
            <td colspan="3" class="center title">SURAT INSTRUKSI KERJA</td>
        </tr>
    </table>

    {{-- ================= NOMOR & TANGGAL ================= --}}
    <table>
        <tr>
            <td width="60%" style="padding: 2px; border-right:none; border-bottom: none;"></td>
            <td width="15%" style="padding: 2px; border-left:none;border-right:none; border-bottom: none;">Nomor</td>
            <td width="2%" style="padding: 2px; border-left:none;border-right:none; border-bottom: none;">:</td>
            <td style="padding: 2px; border-left:none; border-bottom: none;">{{ $arr['no_sik'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 2px; border-right:none;border-top:none;"></td>
            <td style="padding: 2px; border-left:none;border-right:none;border-top:none;">Tanggal</td>
            <td style="padding: 2px; border-left:none;border-right:none;border-top:none;">:</td>
            <td style="padding: 2px; border-left:none;border-top:none;">
                {{ \Carbon\Carbon::parse($arr['tanggal_sik'])->translatedFormat('d F Y') ?? '-' }}
            </td>
        </tr>
    </table>

    {{-- ================= CLIENT ================= --}}
    <table>
        <tr>
            <td width="20%" style="padding: 2px; border-bottom: none; border-right: none;">&nbsp;Client / End User</td>
            <td width="2%" style="padding: 2px; border-bottom: none; border-left: none; border-right: none;">:</td>
            <td width="53%" style="padding: 2px; border-bottom: none; border-left: none;">{{ $namaclient }}</td>
            <td width="25%" style="padding: 2px; border-bottom: none; border-left: none; vertical-align: middle; font-size: 14pt;" rowspan="2"
                align="center"><b>{{ $raw['project_number'] ?? '-' }}</b></td>
        </tr>
        <tr>
            <td style="padding: 2px; border-top: none; border-right: none;">&nbsp;Contact Person</td>
            <td style="padding: 2px; border-top: none; border-left: none; border-right: none;">:</td>
            <td style="padding: 2px; border-top: none; border-left: none;">{{ $arr['contact_person'] ?? '-' }}</td>
        </tr>
    </table>

    {{-- ================= PERALATAN ================= --}}
    <table style="font-size: 9pt;">
        <tr>
            <td width="25%" align="center" style="padding: 4px;"><b>Objek Inspeksi</b></td>
            <td width="25%" align="center" style="padding: 4px;"><b>Tipe Objek Inspeksi</b></td>
            <td width="25%" align="center" style="padding: 4px;"><b>Kategori Inspeksi</b></td>
            <td width="25%" align="center" style="padding: 4px;"><b>Jumlah</b></td>
        </tr>

        @foreach ($arr['peralatan'] ?? [] as $i => $alat)
            <tr>
                @php
                    $typeId = (int) $alat['type_peralatan'];
                @endphp

                <td align="center" style="vertical-align: middle; padding: 4px;">
                    {{ $jenisMap[$typeId]->nama_jenis ?? '-' }}
                </td>

                <td align="center" style="vertical-align: middle; padding: 4px;">
                    {{ $jenisMap[$typeId]->nama_tipe ?? '-' }}
                </td>

                <td align="center" style="vertical-align: middle; padding: 4px;">
                    {{ $jenisMap[$typeId]->nama_kategori ?? '-' }}
                </td>
                <td align="center" style="vertical-align: middle; padding: 4px;">{{ $alat['jumlah'] ?? '-' }}</td>
            </tr>
        @endforeach

    </table>

    {{-- ================= INSPECTOR ================= --}}
    <table>
        <tr>
            <td colspan="9" style="font-size: 9pt; font-weight: bold; padding: 2px;"><i>Bersama ini menugaskan saudara :</i></td>
        </tr>
        <tr>
            {{-- NAMA --}}
            <td width="8%" style="padding: 2px; border-bottom: none; border-right:none; font-size: 8pt;">&nbsp;Nama</td>
            <td width="2%" style="padding: 2px; border-bottom: none; border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td width="23%" style="padding: 2px; border-bottom: none; border-left:none; border-right:none; font-size: 8pt;">
                {{ $nama }}</td>

            {{-- LOKASI --}}
            <td width="12%" style="padding: 2px; border-bottom: none; border-right:none; border-left: none; font-size: 8pt;">&nbsp;Lokasi
                Kerja</td>
            <td width="2%" style="padding: 2px; border-bottom: none; border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td width="23%" style="padding: 2px; border-bottom: none; border-left:none; border-right:none; font-size: 8pt;">
                {{ $arr['location_job'] ?? '-' }}
            </td>

            {{-- TGL MULAI --}}
            <td width="10%" style="padding: 2px; border-bottom: none; border-right:none; border-left: none; font-size: 8pt;">&nbsp;Tgl.
                Mulai</td>
            <td width="2%" style="padding: 2px; border-bottom: none; border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td width="18%" style="padding: 2px; border-bottom: none; border-left:none; font-size: 8pt;">
                {{ \Carbon\Carbon::parse($arr['date_start'])->translatedFormat('F d, Y') }}
            </td>
        </tr>

        <tr>
            <td style="padding: 2px; border-bottom: none; border-top: none; border-right:none; font-size: 8pt;">&nbsp;Jabatan</td>
            <td style="padding: 2px; border-bottom: none; border-top: none; border-left:none;border-right:none; font-size: 8pt;">:
            </td>
            <td style="padding: 2px; border-bottom: none; border-top: none; border-left:none; border-right:none; font-size: 8pt;">
                {{ $arr['pilihan_jabatan_project'] }}</td>

            <td style="padding: 2px; border-bottom: none; border-top: none; border-right:none; border-left: none; font-size: 8pt;">
                &nbsp;Area</td>
            <td style="padding: 2px; border-bottom: none; border-top: none; border-left:none;border-right:none; font-size: 8pt;">:
            </td>
            <td style="padding: 2px; border-bottom: none; border-top: none; border-left:none; border-right:none; font-size: 8pt;">
                {{ $arr['area'] ?? '-' }}
            </td>

            <td style="padding: 2px; border-bottom: none; border-top: none; border-right:none; border-left: none; font-size: 8pt;">
                &nbsp;Tgl. Akhir</td>
            <td style="padding: 2px; border-bottom: none; border-top: none; border-left:none;border-right:none; font-size: 8pt;">:
            </td>
            <td style="padding: 2px; border-bottom: none; border-top: none; border-left:none; font-size: 8pt;">
                {{ \Carbon\Carbon::parse($arr['date_end'])->translatedFormat('F d, Y') }}
            </td>
        </tr>

        <tr>
            <td style="padding: 2px; border-top: none; border-right:none; font-size: 8pt;">&nbsp;NIP</td>
            <td style="padding: 2px; border-top: none; border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td style="padding: 2px; border-top: none; border-left:none; border-right:none; font-size: 8pt;">{{ $nip }}</td>

            <td style="padding: 2px; border-top: none; border-right:none; border-left: none; font-size: 8pt;"></td>
            <td style="padding: 2px; border-top: none; border-left:none;border-right:none; font-size: 8pt;"></td>
            <td style="padding: 2px; border-top: none; border-left:none; border-right:none; font-size: 8pt;"></td>

            <td style="padding: 2px; border-top: none; border-right:none; border-left: none; font-size: 8pt;">&nbsp;Durasi</td>
            <td style="padding: 2px; border-top: none; border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td style="padding: 2px; border-top: none; border-left:none; font-size: 8pt;">
                {{ $arr['durasi'] }}
            </td>
        </tr>

    </table>

    {{-- ================= CHECKLIST ================= --}}
    @php
        $persiapan = $arr['persiapan'] ?? [];
        $lapangan = $arr['lapangan'] ?? [];
        $pelaporan = $arr['pelaporan'] ?? [];
        $migas = $arr['migas'] ?? [];

        function cek($val)
        {
            return $val === 'Yes' ? '[ v ]' : '[   ]';
        }
    @endphp

    <table style="font-size:8pt;">
        <tr>
            <td colspan="8" style="font-weight:bold; padding: 2px;">
                <i>Untuk melaksanakan tugas sebagai berikut :</i>
            </td>
        </tr>

        <tr>
            <td colspan="2" align="center" style="padding: 3px;"><b>Persiapan Inspeksi</b></td>
            <td colspan="2" align="center" style="padding: 3px;"><b>Pemeriksaan Lapangan</b></td>
            <td colspan="2" align="center" style="padding: 3px;"><b>Pelaporan Inspeksi</b></td>
            <td colspan="2" align="center" style="padding: 3px;"><b>Pengurusan Migas</b></td>
        </tr>

        <tr>
            <td>Review Dokumen Awal</td>
            <td align="center">{{ cek($persiapan['peri1'] ?? null) }}</td>

            <td>Pra - Inspeksi Meeting</td>
            <td align="center">{{ cek($lapangan['pl1'] ?? null) }}</td>

            <td>Sistematika Pelaporan</td>
            <td align="center">{{ cek($pelaporan['si1'] ?? null) }}</td>

            <td>Approval Konseptor</td>
            <td align="center">{{ cek($migas['pm1'] ?? null) }}</td>
        </tr>

        <tr>
            <td>Biaya Administrasi/Budget</td>
            <td align="center">{{ cek($persiapan['peri2'] ?? null) }}</td>

            <td>Verifikasi Dokumen Teknis</td>
            <td align="center">{{ cek($lapangan['pl2'] ?? null) }}</td>

            <td>Pemindahan Data Lapangan</td>
            <td align="center">{{ cek($pelaporan['si2'] ?? null) }}</td>

            <td>Approval Direktur Migas</td>
            <td align="center">{{ cek($migas['pm2'] ?? null) }}</td>
        </tr>

        <tr>
            <td>Perizinan Kerja</td>
            <td align="center">{{ cek($persiapan['peri3'] ?? null) }}</td>

            <td>Verifikasi Material / Alat</td>
            <td align="center">{{ cek($lapangan['pl3'] ?? null) }}</td>

            <td>Design Apraisal / Perhitungan</td>
            <td align="center">{{ cek($pelaporan['si3'] ?? null) }}</td>

            <td>Lainnya</td>
            <td align="center">{{ cek($migas['pm3'] ?? null) }}</td>
        </tr>

        <tr>
            <td>Peralatan Kerja</td>
            <td align="center">{{ cek($persiapan['peri4'] ?? null) }}</td>

            <td>Inspeksi Fabrikasi / Instalasi</td>
            <td align="center">{{ cek($lapangan['pl4'] ?? null) }}</td>

            <td>Analisa Laporan</td>
            <td align="center">{{ cek($pelaporan['si4'] ?? null) }}</td>

            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td></td>

            <td>Pengujian Fungsi</td>
            <td align="center">{{ cek($lapangan['pl5'] ?? null) }}</td>

            <td>Draft Sertifikat (COI dan Migas)</td>
            <td align="center">{{ cek($pelaporan['si5'] ?? null) }}</td>

            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td></td>

            <td>Pengisian Form Inspeksi</td>
            <td align="center">{{ cek($lapangan['pl6'] ?? null) }}</td>

            <td></td>
            <td></td>

            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td></td>

            <td>Laporan Awal</td>
            <td align="center">{{ cek($lapangan['pl7'] ?? null) }}</td>

            <td></td>
            <td></td>

            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td></td>

            <td>Closing Meeting</td>
            <td align="center">{{ cek($lapangan['pl8'] ?? null) }}</td>

            <td></td>
            <td></td>

            <td></td>
            <td></td>
        </tr>

        <tr>
            <td style="font-weight: bold; text-align: center; border-right: none;"><i>Catatan :</i></td>
            <td colspan="7" style="border-left: none;">{{ $arr['catatan_sik'] }}</td>
        </tr>
    </table>

    {{-- ================= TANDA TANGAN ================= --}}
    <table>
        <tr>
            <td width="25%" class="ttd-box">
                <div class="ttd-title">Pemberi Tugas</div>
                <img src="{{ public_path('uploadfile/ttd/ttd_OCM.png') }}" class="ttd-img">
                <div class="ttd-jabatan">Manager Operasional</div>
            </td>

            <td width="25%" class="ttd-box">
                <div class="ttd-title">Menyetujui</div>
                <img src="{{ public_path('uploadfile/ttd/TTD_KAK_MERY.png') }}" class="ttd-img">
                <div class="ttd-jabatan">QHSE Officer</div>
            </td>

            <td width="25%" class="ttd-box">
                <div class="ttd-title">Mengetahui</div>
                <img src="{{ public_path('uploadfile/ttd/kosong.png') }}" class="ttd-img">
                <div class="ttd-jabatan">Manager Keuangan</div>
            </td>

            <td width="25%" class="ttd-box">
                <div class="ttd-title">Mengetahui</div>
                <img src="{{ public_path('uploadfile/ttd/ttd_dea.png') }}" class="ttd-img">
                <div class="ttd-jabatan">Manager Marketing</div>
            </td>
        </tr>
    </table>

    {{-- ================= PERALATAN INSPEKSI ================= --}}
    <table class="tbl-peralatan" style="margin-top:5px;">
        <tr>
            <td colspan="7" class="tbl-title">
                &nbsp;&nbsp;&nbsp;Peralatan Inspeksi yang akan digunakan :
            </td>
        </tr>

        <tr class="tbl-header">
            <td width="5%">No</td>
            <td width="20%">Nama Peralatan</td>
            <td width="15%">Tag Number</td>
            <td width="10%">Type</td>
            <td width="15%">Serial Number</td>
            <td width="20%">Lama Pemakaian</td>
            <td width="15%">Kondisi Alat</td>
        </tr>

        {{-- Contoh 1 baris kosong --}}
        <tr>
            <td class="tbl-center">&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        {{-- Catatan --}}
        <tr>
            <td colspan="7" style="font-style: italic;">
                Catatan :
            </td>
        </tr>
    </table>


</body>

</html>
