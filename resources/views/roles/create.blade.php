@extends('adminlte::page')

@section('title', 'Tambah Role')

@section('content_header')
    <h1>Tambah Role</h1>
@stop

@section('content')

    <form method="POST" action="{{ route('roles.store') }}">
        @csrf

        <div class="card">
            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">
                        <label>Role Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                </div>

            </div>
        </div>


        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">Menu Access</h5>
            </div>

            <div class="card-body">

                @foreach ($menus as $parent)
                    <div class="menu-section mb-4">

                        <h5 class="text-primary border-bottom pb-2">
                            {{ $parent->name }}
                        </h5>


                        {{-- jika parent adalah menu langsung --}}
                        @if ($parent->children->count() == 0)
                            <div class="row mb-2">

                                <div class="col-md-3">

                                    <div class="custom-control custom-checkbox">

                                        <input type="checkbox" class="custom-control-input" id="menu{{ $parent->id }}"
                                            name="menus[]" value="{{ $parent->id }}">

                                        <label class="custom-control-label" for="menu{{ $parent->id }}">
                                            {{ $parent->name }}
                                        </label>

                                    </div>

                                </div>

                            </div>
                        @endif


                        {{-- jika punya children --}}
                        @if ($parent->children->count())
                            <div class="row">

                                @foreach ($parent->children as $child)
                                    @if ($child->children->count())
                                        <div class="col-12 mt-2">

                                            <strong class="text-muted">
                                                {{ $child->name }}
                                            </strong>

                                            <div class="row mt-2">

                                                @foreach ($child->children as $sub)
                                                    <div class="col-md-3 mb-2">

                                                        <div class="custom-control custom-checkbox">

                                                            <input type="checkbox" class="custom-control-input"
                                                                id="menu{{ $sub->id }}" name="menus[]"
                                                                value="{{ $sub->id }}">

                                                            <label class="custom-control-label"
                                                                for="menu{{ $sub->id }}">
                                                                {{ $sub->name }}
                                                            </label>

                                                        </div>

                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    @else
                                        <div class="col-md-3 mb-2">

                                            <div class="custom-control custom-checkbox">

                                                <input type="checkbox" class="custom-control-input"
                                                    id="menu{{ $child->id }}" name="menus[]"
                                                    value="{{ $child->id }}">

                                                <label class="custom-control-label" for="menu{{ $child->id }}">
                                                    {{ $child->name }}
                                                </label>

                                            </div>

                                        </div>
                                    @endif
                                @endforeach

                            </div>
                        @endif

                    </div>
                @endforeach

            </div>

        </div>


        <div class="mt-3">
            <button class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>

    </form>

@stop
