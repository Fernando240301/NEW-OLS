@extends('adminlte::page')

@section('title', 'Project List')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@endsection


@section('plugins.Datatables', true)

@section('plugins.Select2', true)


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
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td class="aksi-cell">
                                <a href="{{ route('project_list.detail', $row->workflowid) }}" class="btn btn-primary">
                                    Detail
                                </a>
                            </td>
                            <td>{{ $row->project_number }}</td>
                            <td>{{ $row->projectname }}</td>
                            <td>{{ $row->contract_number }}</td>
                            <td>{{ $row->client_name }}</td>
                        </tr>
                    @endforeach
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

@push('css')
    <style>
        /* Samakan tinggi input & select */
        #scopeTable .form-control,
        #scopeTable .select2-container .select2-selection--single {
            height: 38px;
            padding: 6px 10px;
            font-size: 14px;
        }

        /* Tengahin konten cell */
        #scopeTable td,
        #scopeTable th {
            vertical-align: middle !important;
        }

        /* Select2 full width */
        #scopeTable .select2-container {
            width: 100% !important;
        }

        /* Rapihin tombol hapus */
        #scopeTable .btnRemoveRow {
            padding: 4px 8px;
        }

        /* Lebarin kolom input angka */
        #scopeTable input[type="number"],
        #scopeTable input.harga-input {
            text-align: right;
        }

        /* Header table lebih clean */
        #scopeTable thead th {
            background: #f8f9fa;
            font-weight: 600;
        }

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
