@extends('adminlte::page')

@section('title', 'Tambah PO')

@section('content_header')
    <h1>Tambah Purchase Order</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Input PO</h3>
        <div class="card-tools">
            <a href="{{ route('daftarpo.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('daftarpo.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No PR</label>
                        <input type="text" name="pr_number" class="form-control" value="{{ old('pr_number') }}" required>
                    </div>

                    <div class="form-group">
                <label>Nama Pengaju</label>
                <select name="nama_pengaju" class="form-control" required>
                    <option value="">-- Pilih Nama Pengaju --</option>

                    <option value="Dea Popirawati" 
                        {{ (isset($po) && $po->nama_pengaju == 'Dea Popirawati') ? 'selected' : '' }}>
                        Dea Popirawati
                    </option>

                    <option value="Rony Dwi Cahyono" 
                        {{ (isset($po) && $po->nama_pengaju == 'Rony Dwi Cahyono') ? 'selected' : '' }}>
                        Rony Dwi Cahyono
                    </option>

                    <option value="Maulina" 
                        {{ (isset($po) && $po->nama_pengaju == 'Maulina') ? 'selected' : '' }}>
                        Maulina
                    </option>

                    <option value="Beiby Septi Arynsa" 
                        {{ (isset($po) && $po->nama_pengaju == 'Beiby Septi Arynsa') ? 'selected' : '' }}>
                        Beiby Septi Arynsa
                    </option>
                </select>
            </div>

                    <div class="form-group">
                        <label>Kepada</label>
                        <input type="text" name="to" class="form-control" value="{{ old('to') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ship To</label>
                        <input type="text" name="ship_to" class="form-control" value="{{ old('ship_to') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Ship Date</label>
                        <input type="date" name="ship_date" class="form-control" value="{{ old('ship_date') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Qty</label>
                        <input type="number" name="qty" class="form-control" value="{{ old('qty') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Unit</label>
                        <input type="text" name="unit" class="form-control" value="{{ old('unit') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Unit Price</label>
                        <input type="number" name="unit_price" class="form-control" value="{{ old('unit_price') }}" step="0.01" required>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('daftarpo.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection