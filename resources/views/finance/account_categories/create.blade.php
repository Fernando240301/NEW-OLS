@extends('adminlte::page')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h4 class="mb-3">
                <i class="fas fa-plus-circle text-primary"></i>
                Tambah Account Category
            </h4>

            <form action="{{ route('account-categories.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Account Type</label>
                    <select name="account_type_id" class="form-control" required>
                        <option value="">-- Pilih Type --</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Kode (Opsional)</label>
                    <input type="text" name="code" class="form-control" placeholder="Contoh: 1100">
                </div>

                <div class="form-group">
                    <label>Nama Category</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mt-4">
                    <button class="btn btn-primary">
                        Simpan
                    </button>
                    <a href="{{ route('account-categories.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>
@endsection
