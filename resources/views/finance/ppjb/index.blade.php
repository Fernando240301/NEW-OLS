@extends('adminlte::page')

@section('title', 'PPJB')

@section('plugins.Bootstrap', true)

@section('content_header')
    <h1>Daftar PPJB</h1>
@stop

@section('content')
    <style>
        .btn-sm {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('ppjb-new.create') }}" class="btn btn-primary">
            Buat PPJB
        </a>
        
        @php
            $allowedUsers = ['fernando', 'Ussif', 'Dillaf', 'Nisaf', 'Fitrif', 'Riflif', 'OCM'];
        @endphp
        
        &nbsp;
        
        @if(in_array(strtolower(Auth::user()->username), array_map('strtolower', $allowedUsers)))
            <button class="btn btn-success" data-toggle="modal" data-target="#modalRekap">
                Rekap PPJB
            </button>
        @endif

    </div>

    <table id="ppjbTable" class="table table-bordered table-striped">
        <thead class="table-light text-center">
            <tr>
                <th>No PPJB</th>
                <th>Tanggal</th>
                <th>PIC</th>
                <th>Project No</th>                
                <th>Status PPJB</th>
                <th>Status LPJB</th>
                <th>SIK</th>
                <th>Action PPJB</th>
                <th>Action LPJB</th>
            </tr>
        </thead>
    </table>

    @if(in_array(strtolower(Auth::user()->username), array_map('strtolower', $allowedUsers)))
        <div class="modal fade" id="modalRekap" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Rekap PPJB vs LPJB</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body" style="max-height:500px; overflow-y:auto;">

                        <table class="table table-bordered table-striped text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Tahun</th>
                                    <th>Bulan</th>
                                    <th>Total PPJB</th>
                                    <th>Total PPJB (Rp)</th>
                                    <th>Total LPJB</th>
                                    <th>Total LPJB (Rp)</th>
                                    <th>Total Tanpa LPJB</th>
                                    <th>Total Tanpa LPJB (Rp)</th>
                                    <th>Selisih</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($rekap as $r)
                                    <tr class="rekap-row" style="cursor:pointer;" data-bulan="{{ $r->bulan }}"
                                        data-tahun="{{ $r->tahun }}">
                                        {{-- TAHUN --}}
                                        <td>{{ $r->tahun }}</td>

                                        {{-- BULAN --}}
                                        <td>
                                            {{ \Carbon\Carbon::create()->month($r->bulan)->translatedFormat('F') }}
                                        </td>

                                        {{-- PPJB --}}
                                        <td>{{ $r->total_ppjb }}</td>
                                        <td>
                                            Rp {{ number_format($r->total_ppjb_nominal, 0, ',', '.') }}
                                        </td>

                                        {{-- LPJB --}}
                                        <td>{{ $r->total_lpjb }}</td>
                                        <td>
                                            Rp {{ number_format($r->total_lpjb_nominal, 0, ',', '.') }}
                                        </td>

                                        {{-- MIGAS --}}
                                        <td>{{ $r->total_tanpa_lpjb }}</td>
                                        <td>
                                            Rp {{ number_format($r->total_tanpa_lpjb_nominal, 0, ',', '.') }}
                                        </td>

                                        {{-- SELISIH --}}
                                        <td>
                                            <strong class="{{ $r->selisih > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($r->selisih, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>

                    </div>

                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        Detail PPJB - <span id="detailTitle"></span>
                        <br>

                        <small>
                            PPJB: <strong id="detailTotalPpjb">Rp 0</strong> |
                            LPJB: <strong id="detailTotalLpjb">Rp 0</strong> |
                            Selisih: <strong id="detailSelisih">Rp 0</strong>
                        </small>
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>No PPJB</th>
                                <th>Tanggal</th>
                                <th>PIC</th>
                                <th>PPJB</th>
                                <th>LPJB</th>
                                <th>Selisih</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="detailBody"></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

@stop

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).on('click', '.rekap-row', function() {

            let month = $(this).data('bulan');
            let year = $(this).data('tahun');

            $.get("{{ route('ppjb-new.rekap-detail') }}", {
                month: month,
                year: year
            }, function(res) {

                let html = '';
                let total = 0;
                let totalLpjb = 0;

                res.forEach(r => {

                    total += Number(r.total);
                    totalLpjb += Number(r.lpjb_total);

                    html += `
                    <tr class="${r.jenis_pengajuan === 'project_migas' ? 'table-warning' : ''}">
                        <td>${r.no_ppjb}</td>
                        <td>${formatTanggal(r.tanggal_permohonan)}</td>
                        <td>${r.pic ?? '-'}</td>
        
                        <td>Rp ${formatRupiah(r.total)}</td>
                        <td>Rp ${formatRupiah(r.lpjb_total)}</td>
        
                        <td>
                            <span class="${r.selisih > 0 ? 'text-danger' : 'text-success'}">
                                Rp ${formatRupiah(r.selisih)}
                            </span>
                        </td>
        
                        <td>
                            <span class="badge ${getStatusColor(r)}">
                                ${getStatusText(r)}
                            </span>
                        </td>
                    </tr>`;
                });

                let selisih = total - totalLpjb;

                $('#detailBody').html(html);
                $('#detailTitle').text(`Bulan ${getNamaBulan(month)} ${year}`);

                $('#detailTotalPpjb').text('Rp ' + formatRupiah(total));
                $('#detailTotalLpjb').text('Rp ' + formatRupiah(totalLpjb));

                $('#detailSelisih')
                    .text('Rp ' + formatRupiah(selisih))
                    .removeClass('text-success text-danger')
                    .addClass(selisih > 0 ? 'text-danger' : 'text-success');

                $('#modalRekap').modal('hide');
                $('#modalDetail').modal('show');
            });
        });

        // ===============================
        // HELPERS
        // ===============================
        function formatRupiah(num) {
            return Number(num).toLocaleString('id-ID');
        }

        function formatTanggal(date) {
            return new Date(date).toLocaleDateString('id-ID');
        }

        function getNamaBulan(month) {
            return [
                'Januari', 'Februari', 'Maret', 'April',
                'Mei', 'Juni', 'Juli', 'Agustus',
                'September', 'Oktober', 'November', 'Desember'
            ][month - 1];
        }

        function getStatusColor(r) {

            if (r.jenis_pengajuan === 'project_migas' && r.status === 'approved') {
                return 'bg-dark';
            }

            if (r.status === 'approved') return 'bg-success';
            if (r.status === 'draft') return 'bg-secondary';
            if (r.status.includes('waiting')) return 'bg-warning';

            return 'bg-danger';
        }

        function getStatusText(r) {

            if (r.jenis_pengajuan === 'project_migas' && r.status === 'approved') {
                return 'Closed';
            }

            return r.status;
        }
    </script>

    <script>
        $(function() {

            $('#ppjbTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ppjb-new.datatables') }}",

                columns: [
                    { data: 'no_ppjb', name: 'no_ppjb' },
                    { data: 'tanggal', name: 'tanggal_permohonan' },
                    { data: 'pic', name: 'pic' },
                    { data: 'project_no', orderable: false },                    
                    { data: 'status_ppjb', orderable: false, searchable: false },
                    { data: 'status_lpjb', orderable: false, searchable: false },
                    { data: 'sik', orderable: false, searchable: false },
                    { data: 'action_ppjb', orderable: false, searchable: false },
                    { data: 'action_lpjb', orderable: false, searchable: false }
                ],

                pageLength: 25,
                responsive: true
            });

        });
    </script>
@stop
