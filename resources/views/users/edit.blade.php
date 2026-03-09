@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <h1>Edit User</h1>
@stop


@section('content')

    <form method="POST" action="{{ route('users.update', $user->userid) }}">

        @csrf
        @method('PUT')

        <div class="card">

            <div class="card-body">

                <div class="form-group">

                    <label>Username</label>

                    <input type="text" class="form-control" value="{{ $user->username }}" readonly>

                </div>


                <div class="form-group">

                    <label>Fullname</label>

                    <input type="text" name="fullname" class="form-control" value="{{ $user->fullname }}">

                </div>


                <div class="form-group">

                    <label>Password (kosongkan jika tidak diubah)</label>

                    <input type="password" name="password" class="form-control">

                </div>


                <div class="form-group">

                    <label>Role</label>

                    <select name="rolesid" class="form-control">

                        @foreach ($roles as $id => $name)
                            <option value="{{ $id }}" @if ($user->rolesid == $id) selected @endif>

                                {{ $name }}

                            </option>
                        @endforeach

                    </select>

                </div>


                <div class="form-group">

                    <label>Telepon</label>

                    <input type="text" name="telepon" class="form-control" value="{{ $user->telepon }}">

                </div>


                <div class="form-group">

                    <label>Kantor</label>

                    <input type="text" name="kantor" class="form-control" value="{{ $user->kantor }}">

                </div>


                <div class="form-group">

                    <label>Status</label>

                    <select name="active" class="form-control">

                        <option value="1" @if ($user->active) selected @endif>
                            Active
                        </option>

                        <option value="0" @if (!$user->active) selected @endif>
                            Inactive
                        </option>

                    </select>

                </div>

            </div>

        </div>


        <button class="btn btn-success">

            <i class="fas fa-save"></i>
            Update

        </button>

    </form>

@stop
