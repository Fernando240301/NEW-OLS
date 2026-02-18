@extends('adminlte::page')

@section('title', 'Create Account Type')

@section('content')

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="mb-4">
                <h4 class="font-weight-bold mb-1">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Tambah Account Type
                </h4>
                <small class="text-muted">
                    Tambahkan tipe akun baru untuk struktur ERP
                </small>
            </div>

            <form action="{{ route('account-types.store') }}" method="POST">
                @csrf

                <div class="row">

                    {{-- CODE --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kode</label>
                            <input type="text" name="code" value="{{ old('code') }}"
                                class="form-control @error('code') is-invalid @enderror" placeholder="Contoh: ASSET">

                            @error('code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- NAME --}}
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Asset">

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- NORMAL BALANCE --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Normal Balance</label>
                            <select name="normal_balance"
                                class="form-control @error('normal_balance') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="debit" {{ old('normal_balance') == 'debit' ? 'selected' : '' }}>
                                    Debit
                                </option>
                                <option value="credit" {{ old('normal_balance') == 'credit' ? 'selected' : '' }}>
                                    Credit
                                </option>
                            </select>

                            @error('normal_balance')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                </div>

                <hr>

                {{-- BUTTON --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('account-types.index') }}" class="btn btn-light mr-2">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fas fa-save mr-1"></i>
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

@endsection
