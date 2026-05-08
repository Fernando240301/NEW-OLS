@extends('adminlte::page')

@section('title', 'Edit Jenis Peralatan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Kategori</h3>
    </div>
    <div class="card-body">
       <form action="{{ route('kategori_peralatan.update', $item->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" name="nama" value="{{ $item->nama }}" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Alias</label>
        <input type="text" name="alias" value="{{ $item->alias }}" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success mt-2">Update</button>
    <a href="{{ route('kategori_peralatan.index') }}" class="btn btn-secondary mt-2">Batal</a>
</form>

    </div>
</div>
@stop
