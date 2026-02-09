@extends('adminlte::page')

@section('title', 'Tambah Document Penawaran')

@section('content_header')
    <h1>Upload Document Penawaran</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Upload Document Penawaran</h3>
    </div>

    <form action="{{ route('penawaran.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-group">
            <label>No Surat</label>
            <input type="text"
           name="nosurat"
           class="form-control"
           value="{{ $noSurat }}"
           readonly>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
    <label>Upload Surat Penawaran (Word)</label>
    <input type="file"
           name="surat"
           class="form-control @error('surat') is-invalid @enderror"
           accept=".doc,.docx">
    @error('surat')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
        </div>
 </div>

        <div class="card-footer">
            <a href="{{ route('penawaran.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary float-right">Simpan</button>
        </div>
    </form>
</div>
@stop
