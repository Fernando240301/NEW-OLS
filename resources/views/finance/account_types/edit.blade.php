@extends('adminlte::page')

@section('title', 'Edit Account Type')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <div class="mb-4">
                <h4 class="font-weight-bold mb-1">Edit Account Type</h4>
                <p class="text-muted mb-0">
                    Update data tipe akun untuk struktur ERP
                </p>
            </div>

            <form action="{{ route('account-types.update', $accountType->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-semibold">Kode</label>
                        <input type="text" name="code" value="{{ old('code', $accountType->code) }}"
                            class="form-control @error('code') is-invalid @enderror" placeholder="Contoh: KAS_BANK">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-semibold">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $accountType->name) }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Kas/Bank">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="font-weight-semibold">Normal Balance</label>
                        <select name="normal_balance" class="form-control @error('normal_balance') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="debit"
                                {{ old('normal_balance', $accountType->normal_balance) == 'debit' ? 'selected' : '' }}>
                                Debit
                            </option>
                            <option value="credit"
                                {{ old('normal_balance', $accountType->normal_balance) == 'credit' ? 'selected' : '' }}>
                                Credit
                            </option>
                        </select>
                        @error('normal_balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('account-types.index') }}" class="btn btn-light">
                        ← Kembali
                    </a>

                    <button type="submit" class="btn btn-primary px-4">
                        💾 Update
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection
