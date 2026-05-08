@extends('adminlte::page')

@section('title', 'Edit Menu')

@section('content_header')
    <h1>Edit Menu</h1>
@stop

@section('content')

    <form method="POST" action="{{ route('menus.update', $menu->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama Menu</label>
            <input type="text" name="name" class="form-control" value="{{ $menu->name }}">
        </div>

        <div class="form-group">
            <label>Menu Key</label>
            <input type="text" name="menu_key" class="form-control" value="{{ $menu->menu_key }}">
        </div>

        <div class="form-group">
            <label>Icon</label>
            <input type="text" name="icon" class="form-control" value="{{ $menu->icon }}">
        </div>

        <div class="form-group">
            <label>Route</label>
            <input type="text" name="route" class="form-control" value="{{ $menu->route }}">
        </div>

        <div class="form-group">
            <label>URL</label>
            <input type="text" name="url" class="form-control" value="{{ $menu->url }}">
        </div>

        <div class="form-group">
            <label>Parent Menu</label>
            <select name="parent_id" class="form-control">

                <option value="">-- ROOT MENU --</option>

                @foreach ($parents as $id => $name)
                    <option value="{{ $id }}" @if ($menu->parent_id == $id) selected @endif>

                        {{ $name }}

                    </option>
                @endforeach

            </select>
        </div>

        <div class="form-group">
            <label>Order</label>
            <input type="number" name="order_no" class="form-control" value="{{ $menu->order_no }}">
        </div>

        <button class="btn btn-success">
            Update
        </button>

    </form>

@stop
