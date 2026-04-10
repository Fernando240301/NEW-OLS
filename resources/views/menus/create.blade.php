@extends('adminlte::page')

@section('title', 'Tambah Menu')

@section('content_header')
    <h1>Tambah Menu</h1>
@stop

@section('content')

    <form method="POST" action="{{ route('menus.store') }}">
        @csrf

        <div class="form-group">
            <label>Nama Menu</label>
            <input type="text" name="name" class="form-control">
        </div>

        <div class="form-group">
            <label>Menu Key</label>
            <input type="text" name="menu_key" class="form-control">
        </div>

        <div class="form-group">
            <label>Menu Type</label>
            <select name="menu_type" class="form-control">

                <option value="menu">Menu (bisa diakses role)</option>
                <option value="parent">Parent Menu (hanya struktur)</option>

            </select>
        </div>

        <div class="form-group">
            <label>Icon</label>
            <input type="text" name="icon" class="form-control">
        </div>

        <div class="form-group">
            <label>Route</label>
            <input type="text" name="route" class="form-control">
        </div>

        <div class="form-group">
            <label>URL</label>
            <input type="text" name="url" class="form-control">
        </div>

        <div class="form-group">
            <label>Parent Menu</label>
            <select name="parent_id" class="form-control">

                <option value="">-- ROOT MENU --</option>

                @foreach ($parents as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach

            </select>
        </div>

        <div class="form-group">
            <label>Order</label>
            <input type="number" name="order_no" class="form-control">
        </div>

        <button class="btn btn-success">
            Simpan
        </button>

    </form>

@stop
