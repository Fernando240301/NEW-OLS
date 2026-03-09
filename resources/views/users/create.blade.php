@extends('adminlte::page')

@section('title', 'Tambah User')

@section('content_header')
    <h1>Tambah User</h1>
@stop

@section('content')

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="card">
            <div class="card-body">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control">
                </div>

                <div class="form-group">
                    <label>Fullname</label>
                    <input type="text" name="fullname" class="form-control">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="form-group">
                    <label>Role</label>

                    <select name="rolesid" class="form-control">

                        @foreach ($roles as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach

                    </select>

                </div>

                <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" name="telepon" class="form-control">
                </div>

                <div class="form-group">
                    <label>Kantor</label>
                    <input type="text" name="kantor" class="form-control">
                </div>

            </div>
        </div>

        <button class="btn btn-success">
            Simpan
        </button>

    </form>

@stop
