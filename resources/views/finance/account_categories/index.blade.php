@extends('adminlte::page')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-layer-group text-primary"></i>
                        Account Categories
                    </h4>
                    <small class="text-muted">
                        Master data kategori akun untuk struktur ERP
                    </small>
                </div>

                <a href="{{ route('account-categories.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus"></i> Tambah Category
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="categories-table" class="table table-hover align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Account Type</th>
                            <th width="130">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->code ?? '-' }}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $category->type->name }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('account-categories.edit', $category->id) }}"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('account-categories.destroy', $category->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus data?')">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endsection

@section('js')

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#categories-table').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                previous: "Prev",
                next: "Next"
            },
            zeroRecords: "Data tidak ditemukan"
        }
    });
});
</script>

@endsection
