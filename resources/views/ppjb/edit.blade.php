@extends('adminlte::page')

@section('title', 'EDIT PPJB ')

@section('content_header')
    <h1>Tambah Prospect</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Form Prospect</h3>
    </div>

    <form action="{{ route('ppjb.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- INI WAJIB --}}
        <div class="card-body">
            <div class="form-group">
                <label>No. PPJB</label>
                <input type="text" name="nosurat"
                       class="form-control @error('nosurat') is-invalid @enderror"
                       value="{{ old('nosurat', $item->nosurat) }}"
                       placeholder="Contoh: Generator Set">
                @error('nosurat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Dari</label>
                <input type="text" name="dari"
                       class="form-control @error('dari') is-invalid @enderror"
                       value="{{ old('dari', $item->dari) }}">
                @error('dari')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Tanggal Permohonan</label>
                <input type="date" name="tanggal_permohonan"
                       class="form-control @error('tanggal_permohonan') is-invalid @enderror"
                       value="{{ old('tanggal_permohonan', $item->tanggal_permohonan) }}">
                @error('tanggal_permohonan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Tanggal Dibutuhkan</label>
                <input type="date" name="tanggal_dibutuhkan"
                       class="form-control @error('tanggal_dibutuhkan') is-invalid @enderror"
                       value="{{ old('tanggal_dibutuhkan', $item->tanggal_dibutuhkan) }}">
                @error('tanggal_dibutuhkan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Project No.</label>
                <input type="text" name="project"
                       class="form-control @error('project') is-invalid @enderror"
                       value="{{ old('project', $item->project) }}">
                @error('project')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>PIC</label>
                <input type="text" name="PIC"
                       class="form-control @error('PIC') is-invalid @enderror"
                       value="{{ old('PIC', $item->PIC) }}">
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
    <label>File Saat Ini</label><br>

    @foreach($prospect->files ?? [] as $file)
        <a href="{{ asset('storage/'.$file) }}" target="_blank">
            {{ basename($file) }}
        </a><br>
    @endforeach
</div>
        </div>
        <div class="card-footer">
            <a href="{{ route('typeperalatan') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary float-right">Simpan</button>
        </div>
    </form>
</div>
@stop
