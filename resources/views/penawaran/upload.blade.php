@extends('adminlte::page')

@section('title', 'Upload Surat Penawaran')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            Upload Surat - {{ $penawaran->surat }}
        </h3>
    </div>

    <form action="{{ route('penawaran.upload.store', $penawaran->id) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        <div class="card-body">
            <div class="form-group">
                <label>File Word</label>
                <input type="file"
                       name="surat"
                       class="form-control"
                       accept=".doc,.docx"
                       required>
            </div>
        </div>

        <div class="card-footer">
            <a href="{{ route('penawaran.index') }}"
               class="btn btn-secondary">Kembali</a>
            <button class="btn btn-primary float-right">
                Upload
            </button>
        </div>
    </form>
</div>
@stop
