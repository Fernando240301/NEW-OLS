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
            <td width="60%" style="border-right:none; border-bottom: none;"></td>
            <td width="15%" style="border-left:none;border-right:none; border-bottom: none;">Nomor</td>
            <td width="2%" style="border-left:none;border-right:none; border-bottom: none;">:</td>
            <td style="border-left:none; border-bottom: none;">{{ $arr['no_sik'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border-right:none;border-top:none;"></td>
            <td style="border-left:none;border-right:none;border-top:none;">Tanggal</td>
            <td style="border-left:none;border-right:none;border-top:none;">:</td>
            <td style="border-left:none;border-top:none;">
                {{ \Carbon\Carbon::parse($arr['tanggal_sik'])->translatedFormat('d F Y') ?? '-' }}
            </td>
        </tr>
    </table>

    {{-- ================= CLIENT ================= --}}
    <table>
        <tr>
            <td width="20%" style="border-right: none;">Client / End User</td>
            <td width="2%" style="border-left: none; border-right: none;">:</td>
            <td width="38%" style="border-left: none;">{{ $namaclient }}</td>
            <td width="40%" style="border-left: none; vertical-align: middle; font-size: 14pt;" rowspan="2"
                align="center"><b>{{ $raw['project_number'] ?? '-' }}</b></td>
        </tr>
        <tr>
            <td style="border-right: none;">Contact Person</td>
            <td style="border-left: none; border-right: none;">:</td>
            <td style="border-left: none;">{{ $arr['contact_person'] ?? '-' }}</td>
        </tr>
    </table>

    {{-- ================= PERALATAN ================= --}}
    <table>
        <tr>
            <td width="25%" align="center"><b>Objek Inspeksi</b></td>
            <td width="25%" align="center"><b>Tipe Objek Inspeksi</b></td>
            <td width="25%" align="center"><b>Kategori Inspeksi</b></td>
            <td width="25%" align="center"><b>Jumlah</b></td>
        </tr>

        @foreach ($arr['peralatan'] ?? [] as $i => $alat)
            <tr>
                <td align="center"></td>
                <td align="center">
                    {{ DB::table('ref_tipe_peralatan')->where('id', $alat['type_peralatan'])->value('nama') }}
                </td>
                <td align="center"></td>
                <td align="center">{{ $alat['jumlah'] ?? '-' }}</td>
            </tr>
        @endforeach

    </table>

    {{-- ================= INSPECTOR ================= --}}
    <table>
        <tr>
            <td colspan="9" style="font-size: 9pt; font-weight: bold;"><i>Bersama ini menugaskan saudara :</i></td>
        </tr>
        <tr>
            {{-- NAMA --}}
            <td width="8%" style="border-right:none; font-size: 8pt;">Nama</td>
            <td width="2%" style="border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td width="23%" style="border-left:none; border-right:none; font-size: 8pt;">{{ $nama }}</td>

            {{-- LOKASI --}}
            <td width="12%" style="border-right:none; border-left: none; font-size: 8pt;">Lokasi Kerja</td>
            <td width="2%" style="border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td width="23%" style="border-left:none; border-right:none; font-size: 8pt;">
                {{ $arr['location_job'] ?? '-' }}
            </td>

            {{-- TGL MULAI --}}
            <td width="10%" style="border-right:none; border-left: none; font-size: 8pt;">Tgl. Mulai</td>
            <td width="2%" style="border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td width="18%" style="border-left:none; font-size: 8pt;">
                {{ \Carbon\Carbon::parse($arr['date_start'])->translatedFormat('F d, Y') }}
            </td>
        </tr>

        <tr>
            <td style="border-right:none; font-size: 8pt;">Jabatan</td>
            <td style="border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td style="border-left:none; border-right:none; font-size: 8pt;">{{ $arr['pilihan_jabatan_project'] }}</td>

            <td style="border-right:none; border-left: none; font-size: 8pt;">Area</td>
            <td style="border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td style="border-left:none; border-right:none; font-size: 8pt;">
                {{ $arr['area'] ?? '-' }}
            </td>

            <td style="border-right:none; border-left: none; font-size: 8pt;">Tgl. Akhir</td>
            <td style="border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td style="border-left:none; font-size: 8pt;">
                {{ \Carbon\Carbon::parse($arr['date_end'])->translatedFormat('F d, Y') }}
            </td>
        </tr>

        <tr>
            <td style="border-right:none; font-size: 8pt;">NIP</td>
            <td style="border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td style="border-left:none; border-right:none; font-size: 8pt;">{{ $nip }}</td>

            <td style="border-right:none; border-left: none; font-size: 8pt;"></td>
            <td style="border-left:none;border-right:none; font-size: 8pt;"></td>
            <td style="border-left:none; border-right:none; font-size: 8pt;"></td>

            <td style="border-right:none; border-left: none; font-size: 8pt;">Durasi</td>
            <td style="border-left:none;border-right:none; font-size: 8pt;">:</td>
            <td style="border-left:none; font-size: 8pt;">
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
            <td colspan="8" style="font-weight:bold;">
                <i>Untuk melaksanakan tugas sebagai berikut :</i>
            </td>
        </tr>

        <tr>
            <td colspan="2" align="center"><b>Persiapan Inspeksi</b></td>
            <td colspan="2" align="center"><b>Pemeriksaan Lapangan</b></td>
            <td colspan="2" align="center"><b>Pelaporan Inspeksi</b></td>
            <td colspan="2" align="center"><b>Pengurusan Migas</b></td>
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
    </table>
</body>

</html>
