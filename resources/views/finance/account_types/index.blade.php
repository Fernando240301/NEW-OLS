@extends('adminlte::page')

@section('title', 'Account Types')

@section('content')

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="mb-4">
                <h4 class="font-weight-bold mb-1">
                    <i class="fas fa-layer-group text-primary mr-2"></i>
                    Account Types
                </h4>

                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <small class="text-muted">
                        Master data tipe akun untuk struktur ERP
                    </small>

                    <a href="{{ route('account-types.create') }}" class="btn btn-primary btn-sm shadow-sm mt-2 mt-md-0">
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Account Type
                    </a>
                </div>
            </div>


            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle">
                    <thead style="background:#f8f9fa;">
                        <tr class="text-muted">
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Normal Balance</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($types as $type)
                            <tr style="border-bottom:1px solid #f1f1f1;">
                                <td class="font-weight-bold text-primary">
                                    {{ $type->code }}
                                </td>

                                <td>
                                    {{ $type->name }}
                                </td>

                                <td>
                                    @if ($type->normal_balance == 'debit')
                                        <span class="badge badge-success px-3 py-2">
                                            Debit
                                        </span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2">
                                            Credit
                                        </span>
                                    @endif
                                </td>

                                <td class="text-right">
                                    <a href="{{ route('account-types.edit', $type->id) }}" class="text-warning mr-3"
                                        title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <form action="{{ route('account-types.destroy', $type->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Hapus data ini?')"
                                            style="border:none;background:none;color:#dc3545;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">

                                    <div style="opacity:.6;">
                                        <i class="fas fa-layer-group fa-3x mb-3"></i>
                                        <div class="mb-2">Belum ada Account Type</div>
                                        <small class="text-muted">
                                            Silakan tambahkan tipe akun terlebih dahulu
                                        </small>
                                    </div>

                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $types->links() }}
            </div>

        </div>
    </div>

@endsection
