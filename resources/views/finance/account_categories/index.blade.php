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
                <table class="table table-hover align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Account Type</th>
                            <th width="130">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
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
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada data Category
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categories->links() }}
            </div>

        </div>
    </div>
@endsection
