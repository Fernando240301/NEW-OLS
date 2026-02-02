@extends('adminlte::page')

@section('title', 'Work Assignment')

@section('plugins.Datatables', true)

@section('content_header')
    <h1 style="text-align: center;">WORK ASSIGNMENT</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('work_assignment.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
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
                        <th width="20%">Aksi</th>
                        <th width="25%">Project Number</th>
                        <th width="10%">Nama Project</th>
                        <th width="10%">Client Name</th>
                        <th width="25%">Contract Number</th>
                        <th width="10%">Createdby</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td>
                                <a href="#" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="#" method="POST"
                                    style="display:inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>


                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-archive"></i>
                                </button>
                            </td>
                            <td>{{ $row->workflowid }}</td>
                            <td>{{ $row->projectname }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
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
