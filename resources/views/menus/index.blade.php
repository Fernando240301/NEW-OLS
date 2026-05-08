@extends('adminlte::page')

@section('title', 'Menus')

@section('content_header')
    <h1>Menu Management</h1>
@stop

@section('content')

    <a href="{{ route('menus.create') }}" class="btn btn-primary mb-3">
        Tambah Menu
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Parent</th>
                <th>Route</th>
                <th>URL</th>
                <th>Order</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($menus as $menu)
                <tr>
                    <td>{{ $menu->id }}</td>
                    <td>{{ $menu->name }}</td>
                    <td>{{ $menu->parent->name ?? '-' }}</td>
                    <td>{{ $menu->route }}</td>
                    <td>{{ $menu->url }}</td>
                    <td>{{ $menu->order_no }}</td>

                    <td>
                        <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus menu ini?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@stop
