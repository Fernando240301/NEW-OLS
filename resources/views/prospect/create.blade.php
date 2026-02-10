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

    <form action="{{ route('prospect.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul"
                       class="form-control @error('judul') is-invalid @enderror"
                       value="{{ old('judul') }}"
                       placeholder="Contoh: Generator Set">
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Client</label>
                <input type="text" name="klient"
                       class="form-control @error('klient') is-invalid @enderror"
                       value="{{ old('klient') }}"
                       placeholder="Contoh: PT.....">
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Dropdown peralatan -->
            <div class="form-group">
                <label>Alat</label>
    <select name="id_peralatan" class="form-control @error('id_peralatan') is-invalid @enderror">
    <option value="">-- Pilih Peralatan --</option>
    @foreach($jenis as $peralatan)
        <option value="{{ $peralatan->id }}" 
            {{ old('id_peralatan') == $peralatan->id ? 'selected' : '' }}>
            {{ $peralatan->nama ?? $peralatan->id }}
        </option>
    @endforeach
</select>

                @error('id_peralatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <input type="text" name="catatan"
                       class="form-control @error('catatan') is-invalid @enderror"
                       value="{{ old('catatan') }}"
                       placeholder="Contoh: PT.....">
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Status</label>
                <input type="text" name="status"
                       class="form-control @error('catatan') is-invalid @enderror"
                       value="{{ old('catatan') }}"
                       placeholder="Contoh: PT.....">
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Sales</label>
                <input type="text" name="sales"
                       class="form-control @error('sales') is-invalid @enderror"
                       value="{{ old('sales') }}"
                       placeholder="SE/SR, DLL">
                @error('sales')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        
        
        

        <div class="card-footer">
            <a href="{{ route('typeperalatan') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary float-right">Simpan</button>
        </div>
    </form>
</div>
@stop
