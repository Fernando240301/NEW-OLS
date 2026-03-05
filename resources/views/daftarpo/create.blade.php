@extends('adminlte::page')

@section('title', 'Tambah Daftar PO')

@section('content_header')
    <h1>Tambah Daftar PO</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Form DAFTAR PO</h3>
    </div>
    <form action="{{ route('daftarpo.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Nama Pengaju</label>
                <input type="text" name="namapengaju"
                       class="form-control @error('namapengaju') is-invalid @enderror"
                       value="{{ old('namapengaju') }}"
                       placeholder=".....">
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>PR. Number</label>
                <input type="text" name="project"
                       class="form-control @error('project') is-invalid @enderror"
                       value="{{ old('project') }}"
                       placeholder="....">
                @error('project')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Dropdown peralatan -->
            <div class="form-group">
                <label>To</label>
                <input type="text" name="to"
                       class="form-control @error('to') is-invalid @enderror"
                       value="{{ old('to') }}"
                       placeholder="Masukan To.....">
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="textarea" name="adress"
                       class="form-control @error('adress') is-invalid @enderror"
                       value="{{ old('adress') }}">
                @error('project')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date"
                       class="form-control @error('date') is-invalid @enderror"
                       value="{{ old('date') }}">
                @error('Date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Attention</label>
                <input type="text" name="attention"
                       class="form-control @error('attention') is-invalid @enderror"
                       value="{{ old('attention') }}">
                @error('PIC')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Ship To</label>
                <input type="text" name="shipto"
                       class="form-control @error('shipto') is-invalid @enderror"
                       value="{{ old('shipto') }}">
                @error('shipto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Ship Date</label>
                <input type="date" name="shipdate"
                       class="form-control @error('shipdate') is-invalid @enderror"
                       value="{{ old('shipdate') }}">
                @error('Date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>description</label>
                <input type="text" name="description"
                       class="form-control @error('description') is-invalid @enderror"
                       value="{{ old('description') }}">
                @error('QTY')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>QTY</label>
                <input type="text" name="qty"
                       class="form-control @error('qty') is-invalid @enderror"
                       value="{{ old('qty') }}">
                @error('QTY')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>UNIT</label>
                <input type="text" name="unit"
                       class="form-control @error('unit') is-invalid @enderror"
                       value="{{ old('unit') }}">
                @error('unit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
    <label>Harga</label>
    <input type="number"
           name="harga"
           class="form-control"
           step="0.01"
           required>
</div>


        </div>
        <div class="card-footer">
            <a href="{{ route('daftarpo.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary float-right">Simpan</button>
        </div>
    </form>
</div>
@stop
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

