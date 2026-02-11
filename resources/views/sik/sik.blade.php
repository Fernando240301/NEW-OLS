@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@endsection

@section('title', 'Surat Instruksi Kerja')

@section('plugins.Datatables', true)

@section('plugins.Select2', true)


@section('content_header')
    <h1 style="text-align: center; color: blue;">{{ $workflowdata['projectname'] }}</h1>
@endsection

@section('content')
    <hr>
    <x-project-menu :workflowid="$app_workflow->workflowid" active="project" />

    <hr>
    <div class="card">
        <div class="card-header">
            <div class="btn-group">
                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-plus"></i> Tambah Dokumen
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('sik.create', $app_workflow->workflowid) }}">
                        <i class="fas fa-certificate text-success"></i> New Certification
                    </a>

                    <a class="dropdown-item" href="#">
                        <i class="fas fa-history text-warning"></i> Extend
                    </a>
                </div>
            </div>
        </div>


        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <table class="table table-bordered table-striped" id="clientTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">No. SIK</th>
                        <th width="20%">Inspector</th>
                        <th width="25%">Jabatan</th>
                        <th width="15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->workflowdata['no_sik'] }}</td>
                            <td>{{ $row->inspector_fullname }}</td>
                            <td>
                                {{ $row->workflowdata['pilihan_jabatan_project'] }}

                                @if (in_array($row->workflowdata['pilihan_jabatan_project'], ['Anggota', 'Teknisi']) && !empty($row->leader_no_sik))
                                    <br>
                                    <small class="text-muted">
                                        Leader :
                                        <strong>{{ $row->leader_fullname }}</strong>
                                        ({{ $row->leader_no_sik }})
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">

                                    {{-- PREVIEW --}}
                                    <a href="{{ route('sik.show', $row->workflowid) }}"
                                        class="btn btn-sm btn-outline-info rounded" title="Preview">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('sik.edit', [
                                        'projectId' => $app_workflow->workflowid,
                                        'id' => $row->workflowid,
                                    ]) }}"
                                        class="btn btn-sm btn-outline-warning rounded" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>


                                    {{-- DELETE --}}
                                    <form
                                        action="{{ route('sik.delete', ['projectId' => $app_workflow->workflowid, 'id' => $row->workflowid]) }}"
                                        method="POST" style="display:inline-block;"
                                        onsubmit="return confirm('Yakin ingin menghapus SIK ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(function() {
            $('#clientTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });
    </script>
@endpush
