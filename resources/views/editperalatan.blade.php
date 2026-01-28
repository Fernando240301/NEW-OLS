@extends('adminlte::page')

@section('title', 'Edit Jenis Peralatan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Peralatan</h3>
    </div>
    <div class="card-body">
       <form action="{{ route('tambahperalatan.update', $item->id) }}" method="POST">
    @csrf
    @method('PUT')
            <div class="form-group">
                <label>Nama Peralatan</label>
               <input type="text" name="nama_peralatan" value="{{ $item->nama_peralatan }}" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success mt-2">Update</button>
            <a href="{{ route('jenisperalatan') }}" class="btn btn-secondary mt-2">Batal</a>
        </form>
    </div>
</div>
@stop
