@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@endsection

@section('title', 'Surat Instruksi Kerja')

@section('plugins.Datatables', true)

@section('plugins.Select2', true)


@section('content_header')
    <h1 style="text-align: center; color: blue;">{{ $workflowdata['projectname'] }}</h1>
@endsection

@section('content')
    <hr>
    <x-project-menu :workflowid="$app_workflow->workflowid" active="project" />

    <hr>
    <div class="card">
        <div class="card-header">
            <div class="btn-group">
                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-plus"></i> Tambah Dokumen
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('sik.create', $app_workflow->workflowid) }}">
                        <i class="fas fa-certificate text-success"></i> New Certification
                    </a>

                    <a class="dropdown-item" href="#">
                        <i class="fas fa-history text-warning"></i> Extend
                    </a>
                </div>
            </div>
        </div>


        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <table class="table table-bordered table-striped" id="clientTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">No. SIK</th>
                        <th width="20%">Inspector</th>
                        <th width="25%">Keterangan</th>
                        <th width="15%">Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(function() {
            $('#clientTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });
    </script>
@endpush
