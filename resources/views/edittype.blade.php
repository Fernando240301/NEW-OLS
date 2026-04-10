@extends('adminlte::page')

@section('title', 'Edit Jenis Peralatan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Peralatan</h3>
    </div>
    <div class="card-body">
       <form action="{{ route('tambahtype.update', $item->id) }}" method="POST">
    @csrf
    @method('PUT')
            <div class="form-group">
                <label>Type Alat</label>
               <input type="text" name="type"
       value="{{ old('type', $item->type) }}"
       class="form-control"
       required>
            </div>
            <div class="form-group">
                <label>Peralatan</label>
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
            <button type="submit" class="btn btn-success mt-2">Update</button>
            <a href="{{ route('typeperalatan') }}" class="btn btn-secondary mt-2">Batal</a>
        </form>
    </div>
</div>
@stop
