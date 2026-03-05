@extends('adminlte::page')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-layer-group text-primary"></i>
                        Chart Of Accounts
                    </h4>
                    <small class="text-muted">
                        Master data Chart of Account
                    </small>
                </div>

                <div class="d-flex align-items-center" style="gap:10px;">

                    {{-- Import Button --}}
                    <button class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#importModal">
                        <i class="fas fa-file-excel"></i> Import
                    </button>

                    {{-- Tambah COA --}}
                    <a href="{{ route('chart-of-accounts.create') }}" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-plus"></i> Tambah
                    </a>

                </div>

            </div>

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="alert alert-success shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="200">Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th width="120">Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $account)
                            @include('finance.chart_of_accounts.partials.row', [
                                'account' => $account,
                                'level' => 0,
                            ])
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada data COA
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>


    {{-- ===============================
    IMPORT MODAL
================================ --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-excel text-success"></i>
                        Import Chart Of Accounts
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                </div>

                <form action="{{ route('chart-of-accounts.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload File Excel (.xlsx / .xls)</label>
                            <input type="file" name="file" class="form-control" required>
                            <small class="text-muted">
                                Pastikan format sesuai dengan template COA.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">
                            <i class="fas fa-upload"></i> Import
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
