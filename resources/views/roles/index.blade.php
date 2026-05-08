@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <h1>Role Management</h1>
@stop

@section('content')

    <a href="{{ route('roles.create') }}" class="btn btn-primary mb-3">
        Tambah Role
    </a>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>ID</th>
                <th>Role</th>
                <th>Description</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->description }}</td>

                    <td>

                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus role ini?')">
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

@stop
