@extends('adminlte::page')

@section('title', 'Project List')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@endsection


@section('plugins.Datatables', true)


@section('content_header')
    <h1 style="text-align: center;">PROJECT LIST</h1>
@endsection

@section('content')
    <div class="card">
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
                        <th>Aksi</th>
                        <th>Project Number</th>
                        <th>Project Name</th>
                        <th>Contract Number</th>
                        <th>Client Name</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@push('js')
<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#clientTable').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        autoWidth: false,

        ajax: '{{ route("project_list.data") }}',

        columns: [
            {
                data: 'workflowid',
                render: function(data) {
                    return `<a href="/project-list/${data}/detail" class="btn btn-sm btn-primary">Detail</a>`;
                },
                orderable: false,
                searchable: false
            },
            { data: 'project_number' },
            { data: 'projectname' },
            { data: 'contract_number' },
            { data: 'client_name' }
        ],

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: {
                previous: "‹",
                next: "›"
            }
        }
    });
});
</script>
@endpush
@push('css')
    <style>
        

        /* Modal footer */
        .modal-footer {
            justify-content: space-between;
        }

        /* kolom aksi */
        .aksi-cell {
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            vertical-align: middle !important;
        }
    </style>
@endpush
