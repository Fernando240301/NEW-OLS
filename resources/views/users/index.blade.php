@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <h1>Users</h1>
@stop


@section('content')

    <div class="card">

        <div class="card-header">

            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah User
            </a>

        </div>


        <div class="card-body">

            <table id="usersTable" class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>


                <tbody>

                    @foreach ($users as $u)
                        <tr>

                            <td>{{ $u->username }}</td>

                            <td>{{ $u->fullname }}</td>

                            <td>{{ $u->role->name ?? '-' }}</td>

                            <td>

                                @if ($u->active)
                                    <span class="badge badge-success">
                                        Active
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        Inactive
                                    </span>
                                @endif

                            </td>

                            <td>

                                <a href="{{ route('users.edit', $u->userid) }}" class="btn btn-warning btn-sm">

                                    <i class="fas fa-edit"></i>

                                </a>


                                <form action="{{ route('users.destroy', $u->userid) }}" method="POST"
                                    style="display:inline">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

@stop


@section('js')

    <script>
        $(function() {

            $('#usersTable').DataTable({

                "responsive": true,
                "autoWidth": false,
                "pageLength": 10,

            });

        });
    </script>

@stop
