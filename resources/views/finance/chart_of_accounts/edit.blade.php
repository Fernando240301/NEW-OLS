@extends('adminlte::page')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h5 class="mb-4">
                <i class="fas fa-edit text-primary"></i>
                Edit Chart Of Account
            </h5>

            <form action="{{ route('chart-of-accounts.update', $account->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Code</label>
                    <input type="text" class="form-control" value="{{ $account->code }}" disabled>
                </div>

                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $account->name) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label>Category</label>
                    <select name="account_category_id" class="form-control">
                        <option value="">-- None --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ $account->account_category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input"
                        {{ $account->is_active ? 'checked' : '' }}>
                    <label class="form-check-label">Active</label>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>

                <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </form>

        </div>
    </div>
@endsection
