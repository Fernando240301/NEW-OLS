@extends('adminlte::page')

@section('title', 'Edit Jenis Layanan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Layanan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('jenis_layanan.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nama Layanan</label>
                    <input type="text" name="nama_layanan" value="{{ $item->nama_layanan }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alias</label>
                    <input type="text" name="alias" value="{{ $item->alias }}" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success mt-2">Update</button>
                <a href="{{ route('jenis_layanan.index') }}" class="btn btn-secondary mt-2">Batal</a>
            </form>

        </div>
    </div>
@stop
