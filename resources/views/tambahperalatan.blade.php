@extends('adminlte::page')

@section('title', 'Tambah Jenis Peralatan')

@section('content_header')
    <h1>Tambah Jenis Peralatan</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Form Jenis Peralatan</h3>
    </div>

    <form action="{{ route('tambahperalatan.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Nama Peralatan</label>
                <input type="text" name="nama_peralatan"
                       class="form-control @error('nama_peralatan') is-invalid @enderror"
                       value="{{ old('nama_peralatan') }}"
                       placeholder="Contoh: Generator Set">
                @error('nama_peralatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card-footer">
            <a href="{{ route('tambahperalatan') }}" class="btn btn-secondary">
                Kembali
            </a>
            <button type="submit" class="btn btn-primary float-right">
                Simpan
            </button>
        </div>
    </form>
</div>
@stop
