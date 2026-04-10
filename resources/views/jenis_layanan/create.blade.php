@extends('adminlte::page')

@section('title', 'Tambah Jenis Layanan')

@section('content_header')
    <h1>Tambah Jenis Layanan</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Form Jenis Layanan</h3>
        </div>

        <form action="{{ route('jenis_layanan.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Layanan</label>
                    <input type="text" name="nama_layanan" class="form-control @error('nama_layanan') is-invalid @enderror"
                        value="{{ old('nama_layanan') }}">
                    @error('nama_layanan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Alias</label>
                    <input type="text" name="alias" class="form-control @error('alias') is-invalid @enderror"
                        value="{{ old('alias') }}">
                    @error('alias')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('jenis_layanan.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
                <button type="submit" class="btn btn-primary float-right">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@stop
