@extends('adminlte::page')

@section('title', 'Edit Role')

@section('content_header')
    <h1>Edit Role</h1>
@stop

@section('content')

    <form method="POST" action="{{ route('roles.update', $role->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Role Name</label>
            <input type="text" name="name" class="form-control" value="{{ $role->name }}">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ $role->description }}</textarea>
        </div>

        <hr>

        <h4>Menu Access</h4>

        <div class="row">

            @foreach ($menus as $menu)
                <div class="col-md-3">

                    <label>

                        <input type="checkbox" name="menus[]" value="{{ $menu->id }}"
                            @if (in_array($menu->id, $roleMenus)) checked @endif>

                        {{ $menu->name }}

                    </label>

                </div>
            @endforeach

        </div>

        <br>

        <button class="btn btn-success">
            Update
        </button>

    </form>

@stop
