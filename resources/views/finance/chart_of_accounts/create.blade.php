@extends('adminlte::page')
@section('plugins.Select2', true)

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="mb-4">
                <h4 class="mb-1">
                    <i class="fas fa-plus text-primary"></i> Tambah COA
                </h4>
                <small class="text-muted">Tambahkan akun baru ke dalam struktur Chart of Account</small>
            </div>

            <form action="{{ route('chart-of-accounts.store') }}" method="POST">
                @csrf

                <div class="row">

                    {{-- PARENT --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Parent</label>
                            <select name="parent_id" id="parentSelect" class="form-control select2">
                                <option value="">-- Root Header (Manual Code) --</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}">
                                        {{ $parent->code }} - {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- CODE --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Kode Akun</label>
                            <input type="text" name="code" id="codeInput" class="form-control">
                            <small class="text-muted">
                                Jika pilih parent → kode otomatis
                            </small>
                        </div>
                    </div>

                    {{-- NAME --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Akun</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>

                    {{-- CATEGORY --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Kategori</label>
                            <select name="account_category_id" class="form-control">
                                <option value="">-- Optional --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <div class="mt-4 text-right">
                    <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-outline-secondary mr-2">
                        Kembali
                    </a>

                    <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {

            $('#parentSelect').select2({
                width: '100%',
                placeholder: "Cari parent akun...",
                allowClear: true
            });

            const codeInput = $('#codeInput');

            $('#parentSelect').on('change', function() {

                let parentId = $(this).val();

                if (!parentId) {
                    codeInput.prop('readonly', false).val('');
                    return;
                }

                $.get("{{ url('chart-of-accounts/generate-code') }}/" + parentId, function(data) {
                    codeInput.val(data.code).prop('readonly', true);
                });

            });

            $('form').on('submit', function() {

                const btn = $('#submitBtn');

                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            });

        });
    </script>
@endsection

@push('css')
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 4px 10px;
        }

        .select2-selection__rendered {
            line-height: 28px !important;
        }

        .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
@endpush
