<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Work Assignment Form</title>

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
                MIT-F18-02<br>
                Revisi 00<br>
                25-07-2019
            </td>
        </tr>
        <tr>
            <td colspan="3" class="center title">WORK ASSIGNMENT FORM</td>
        </tr>
    </table>

    <table style="margin-top: 2px">
        <tr>
            <td class="isi" width="58%">
                Project Number : {{ $workflow['project_number'] }}
            </td>
            <td width="42%" class="isi">CLIENT DATA</td>
        </tr>

        <tr>
            <td class="isi">
                Client Name : {{ $project->client_name }}
            </td>
            <td class="detail-client" rowspan="7">
                Office Address : <br>
                <b>{{ $workflow['lokasi_kantor'] }}</b><br><br>
                Site Address : <br>
                <b>{{ $workflow['lokasi_lapangan'] }}</b> <br><br>
                Contact Person Client : <br>
                <b>{{ $workflow['contact_person'] }} ({{ $workflow['no_hp'] }})</b> <br><br>
                Email : <br>
                <b>{{ $workflow['email'] }}</b>
            </td>
        </tr>

        <tr>
            <td class="isi">
                Contract Number : {{ $workflow['no_kontrak'] }}
            </td>
        </tr>

        <tr>
            <td class="isi">
                Validitas Contract :
            </td>
        </tr>

        <tr>
            <td class="client">
                &nbsp;&nbsp;&nbsp;- Issued Contract : {{ $issuedDate }}
            </td>
        </tr>

        <tr>
            <td class="client">
                &nbsp;&nbsp;&nbsp;- Expired Contract : {{ $expiredDate }}
            </td>
        </tr>

        <tr>
            <td class="isi" style="border-bottom: none">
                Project Name :
            </td>
        </tr>

        <tr>
            <td class="project" style="border-top: none">
                {{ $workflow['projectname'] }}
            </td>
        </tr>
    </table>

    @php
        $total = $scope->count();

        // jumlah kolom (tiap 10 item = 1 kolom)
        $columns = max(1, ceil($total / 10));

        // font menyesuaikan jumlah kolom
        if ($columns == 1) {
            $fontSize = '11pt';
        } elseif ($columns == 2) {
            $fontSize = '10pt';
        } elseif ($columns == 3) {
            $fontSize = '9pt';
        } else {
            $fontSize = '8pt';
        }

        // pecah data per kolom
        $chunks = $scope->chunk(ceil($total / $columns));
    @endphp

    <table style="margin-top: 2px">
        <tr>
            <td colspan="{{ $columns }}" class="isi">
                Scope of Work :
            </td>
        </tr>

        <tr>
            @foreach ($chunks as $chunk)
                <td class="sow"
                    style="font-size: {{ $fontSize }}; width: {{ 100 / $columns }}%; min-height: 150px;">
                    @foreach ($chunk as $row)
                        {{ $loop->parent->iteration == 1
                            ? $loop->iteration
                            : $loop->iteration + ($loop->parent->iteration - 1) * count($chunk) }}.
                        <b>{{ $row->tipeRel->nama ?? '-' }}</b>
                        ({{ $row->kategoriRel->nama ?? '-' }}, {{ $row->jumlah }})
                        <br>
                    @endforeach
                </td>
            @endforeach
        </tr>
    </table>

    @php
        // layanan & penanggung
        $services = [
            'Mob/Demob' => $workflow['mobdemob'] ?? null,
            'Akomodasi' => $workflow['akomodasi'] ?? null,
            'Lokal Transport' => $workflow['lokaltransport'] ?? null,
            'Meals' => $workflow['meals'] ?? null,
        ];

        // kelompokkan berdasarkan penanggung
        $grouped = [];
        foreach ($services as $name => $by) {
            if (!empty($by) && $by !== '-') {
                $grouped[$by][] = $name;
            }
        }
    @endphp

    <table style="margin-top: 2px">
        <tr>
            <td width="100%" class="isi">
                Term and Condition :
            </td>
        </tr>

        <tr>
            <td class="sow" style="min-height: 100px">
                {{-- lokasi uji PSV --}}
                @if (!empty($workflow['lokasiujipsv']) && $workflow['lokasiujipsv'] !== '-')
                    Lokasi Pengujian PSV di {{ $workflow['lokasiujipsv'] }}<br>
                @endif

                {{-- layanan & penanggung --}}
                @foreach ($grouped as $by => $items)
                    @php
                        $last = array_pop($items);
                        $list = $items ? implode(', ', $items) . ' dan ' . $last : $last;
                    @endphp

                    {{ $list }} oleh {{ $by }}<br>
                @endforeach

                {{-- PIDP --}}
                @if (!empty($workflow['pidp']))
                    {!! nl2br(e($workflow['pidp'])) !!}
                @endif
            </td>
        </tr>
    </table>

    <table style="margin-top: 2px">
        <tr>
            <td class="isi"><i>DETAIL INFORMATION, SEE IN ATTACHMENT</i></td>
        </tr>
    </table>

    <table style="margin-top: 2px">
        <tr>
            <td class="ttd">Issued / generated by</td>
            <td class="ttd" colspan="2">Knowledge by</td>
        </tr>
        <tr>
            <td width="35%" class="ttd">Manager Marketing</td>
            <td width="35%" class="ttd">Manager Operasi</td>
            <td width="30%" class="ttd">Manager Keuangan</td>
        </tr>

        <tr>
            {{-- MM --}}
            <td class="ttd">
                @if ($project->apv_mm == 1)
                    <img src="{{ public_path('uploadfile/ttd/ttd_dea.png') }}" width="150">
                @else
                    <img src="{{ public_path('uploadfile/ttd/kosong.png') }}" width="150">
                @endif
            </td>

            {{-- MO --}}
            <td class="ttd">
                @if ($project->apv_mo == 1)
                    <img src="{{ public_path('uploadfile/ttd/ttd_pak_rony.png') }}" width="150">
                @else
                    <img src="{{ public_path('uploadfile/ttd/kosong.png') }}" width="150">
                @endif
            </td>

            {{-- MF --}}
            <td class="ttd">
                @if ($project->apv_mf == 1)
                    <img src="{{ public_path('uploadfile/ttd/ttd_fitri.png') }}" width="150">
                @else
                    <img src="{{ public_path('uploadfile/ttd/kosong.png') }}" width="150">
                @endif
            </td>
        </tr>

        <tr>
            <td class="isi">
                Date :
                {{ $project->date_mm ? \Carbon\Carbon::parse($project->date_mm)->format('d M Y') : '-' }}
            </td>
            <td class="isi">
                Date :
                {{ $project->date_mo ? \Carbon\Carbon::parse($project->date_mo)->format('d M Y') : '-' }}
            </td>
            <td class="isi">
                Date :
                {{ $project->date_mf ? \Carbon\Carbon::parse($project->date_mf)->format('d M Y') : '-' }}
            </td>
        </tr>

        <tr>
            <td style="font-weight: bold; font-size: 9pt; text-align: center;" colspan="3">
                This form is used to communicate specific work assignment instruction to project team members
            </td>
        </tr>
    </table>

</body>

</html>
