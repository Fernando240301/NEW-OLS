@extends('adminlte::page')

@section('title', 'Edit Prospect')

@section('content_header')
    <h1>Edit Prospect</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Form Edit Prospect</h3>
    </div>

    <form action="{{ route('prospect.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul"
                       class="form-control @error('judul') is-invalid @enderror"
                       value="{{ old('judul', $item->judul) }}">
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Klient</label>
                <input type="text" name="klient"
                       class="form-control @error('klient') is-invalid @enderror"
                       value="{{ old('klient', $item->klient) }}">
                @error('klient')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Jenis Peralatan</label>
                <select name="id_peralatan"
                        class="form-control @error('id_peralatan') is-invalid @enderror">
                    <option value="">-- Pilih Alat --</option>
                    @foreach ($jenis as $j)
                        <option value="{{ $j->id }}"
                            {{ old('id_peralatan', $item->alat) == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
                @error('id_peralatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Catatan</label>
                <textarea name="catatan"
                          class="form-control @error('catatan') is-invalid @enderror"
                          rows="3">{{ old('catatan', $item->catatan) }}</textarea>
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Status</label>
                <input type="text" name="status"
                       class="form-control @error('status') is-invalid @enderror"
                       value="{{ old('status', $item->status) }}">
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Sales</label>
                <input type="text" name="sales"
                       class="form-control @error('sales') is-invalid @enderror"
                       value="{{ old('sales', $item->sales) }}">
                @error('sales')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
    <label>Tanggal</label>
    <input type="date" name="tanggal"
           class="form-control @error('tanggal') is-invalid @enderror"
           value="{{ old('tanggal', optional($item->tanggal)->format('Y-m-d')) }}">
    @error('tanggal')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

             @if ($item->file)
    <div class="form-group">
        <label>File Saat Ini</label>
        <ul>
            @foreach ($item->file as $f)
                <li>
                    <a href="{{ asset('storage/'.$f) }}" target="_blank">
                        {{ basename($f) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
        <div class="form-group">
    <label>Upload File Baru</label>
    <input type="file" name="file[]" multiple
           class="form-control @error('file.*') is-invalid @enderror">
    @error('file.*')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">
        Kosongkan jika tidak ingin mengganti file
    </small>
</div>

        </div>

        <div class="card-footer">
            <a href="{{ route('prospect.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary float-right">Simpan</button>
        </div>
    </form>
</div>
@stop
