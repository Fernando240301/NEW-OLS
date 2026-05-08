@extends('adminlte::page')

@section('title', 'Extend SIK')

@section('content')

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-history text-warning"></i> Extend SIK</h3>
        </div>

        <form
            action="{{ route('sik.storeExtend', [
                'projectId' => $projectId,
                'id' => $sik->workflowid,
            ]) }}"
            method="POST">
            @csrf

            <div class="card-body">

                {{-- ================= DATA SIK ================= --}}
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>DATA SIK</strong>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Nomor SIK</label>
                            <input type="text" class="form-control" value="{{ $workflowdata['no_sik'] }}" readonly>
                        </div>
                    </div>
                </div>

                {{-- ================= DATA INSPECTOR ================= --}}
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>DATA INSPECTOR</strong>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Nama Inspector</label>
                            <input type="text" class="form-control" value="{{ $inspectorName ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>

                {{-- ================= DATA PERALATAN ================= --}}
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>DATA PERALATAN</strong>
                    </div>
                    <div class="card-body">

                        @php
                            $peralatan = $workflowdata['peralatan'] ?? [];
                        @endphp

                        @if (count($peralatan) > 0)

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="10%">No</th>
                                            <th>Type Peralatan</th>
                                            <th width="20%">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($peralatan as $index => $item)
                                            @php
                                                $namaTipe = DB::table('ref_tipe_peralatan')
                                                    ->where('id', $item['type_peralatan'])
                                                    ->value('nama');
                                            @endphp

                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $namaTipe ?? '-' }}</td>
                                                <td>{{ $item['jumlah'] }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Tidak ada data peralatan.
                            </div>
                        @endif

                    </div>
                </div>

                {{-- ================= EXTEND DATE ================= --}}
                <div class="card card-outline card-warning">
                    <div class="card-header py-2">
                        <strong>PERIODE EXTEND</strong>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Tanggal Mulai Baru</label>
                            <input type="date" name="date_start" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Selesai Baru</label>
                            <input type="date" name="date_end" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Durasi</label>
                            <input type="text" name="durasi" class="form-control" readonly>
                        </div>

                    </div>
                </div>

            </div>

            <div class="card-footer text-right">
                <button class="btn btn-warning">
                    <i class="fas fa-history"></i> Extend SIK
                </button>

                <a href="{{ route('project_list.sik', $projectId) }}" class="btn btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>

@endsection


@section('js')
    <script>
        $(document).ready(function() {

            function hitungDurasi() {
                let start = $('input[name="date_start"]').val();
                let end = $('input[name="date_end"]').val();

                if (start && end) {
                    let s = new Date(start);
                    let e = new Date(end);

                    if (e >= s) {
                        let diff = (e - s) / (1000 * 60 * 60 * 24) + 1;
                        $('input[name="durasi"]').val(diff + ' hari');
                    } else {
                        $('input[name="durasi"]').val('');
                    }
                }
            }

            $('input[name="date_start"], input[name="date_end"]')
                .on('change', hitungDurasi);

        });
    </script>
@endsection
