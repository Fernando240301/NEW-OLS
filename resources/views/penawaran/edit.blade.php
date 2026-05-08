@extends('adminlte::page')

@section('content')
<div class="container">
    <h3>Edit Penawaran</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('penawaran.update', $penawaran->id) }}" method="POST"  enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>No Surat</label>
            <input type="text" class="form-control" value="{{ $penawaran->nosurat }}" readonly>
        </div>

        <div class="mb-3">
            <label>Judul</label>
            <input type="text" name="judul" class="form-control" 
                   value="{{ old('judul', $penawaran->judul ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label>Nama Client</label>
            <input type="text" class="form-control" 
                   value="{{ old('namaclient', $penawaran->namaclient) }}" readonly>
        </div>
        <div class="mb-3">
    <label>File Lama</label><br>

    @if($penawaran->surat)
        <a href="{{ asset('storage/' . $penawaran->surat) }}" target="_blank" class="btn btn-sm btn-info">
            Lihat File Lama
        </a>
    @else
        <span class="text-muted">Tidak ada file</span>
    @endif
</div>

<div class="mb-3">
    <label>Upload File Baru</label>
    <input type="file" name="surat" class="form-control">
    <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
</div>

        <div class="mt-4">
            <a href="{{ route('penawaran.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>

    </form>
</div>
@endsection