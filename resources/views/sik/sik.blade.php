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
                        <th width="25%">Inspector</th>
                        <th width="25%">Jabatan</th>
                        <th width="25%">Action</th>
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

                                <div class="mt-1">
                                    <small class="text-primary d-block">
                                        New Certification
                                    </small>

                                    @if ($row->extends->count())
                                        <small class="text-warning d-block">
                                            ↳ Extend ({{ $row->extends->count() }})
                                        </small>
                                    @endif
                                </div>
                            </td>

                            <td>

                                <div class="d-flex flex-column align-items-start">

                                    {{-- ===================== --}}
                                    {{-- BARIS 1 : PARENT --}}
                                    {{-- ===================== --}}
                                    <div class="d-flex gap-1 mb-1">

                                        <a href="{{ route('sik.show', $row->workflowid) }}"
                                            class="btn btn-sm btn-outline-info" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-copy-link"
                                            data-link="{{ route('sik.show', $row->workflowid) }}" title="Copy Link">
                                            <i class="fas fa-copy"></i>
                                        </button>


                                        <a href="{{ route('sik.edit', [
                                            'projectId' => $app_workflow->workflowid,
                                            'id' => $row->workflowid,
                                        ]) }}"
                                            class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form
                                            action="{{ route('sik.delete', [
                                                'projectId' => $app_workflow->workflowid,
                                                'id' => $row->workflowid,
                                            ]) }}"
                                            method="POST" style="display:inline-block;"
                                            onsubmit="return confirm('Yakin ingin menghapus SIK ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                        <a href="{{ route('sik.extend', [
                                            'projectId' => $app_workflow->workflowid,
                                            'id' => $row->workflowid,
                                        ]) }}"
                                            class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-history"></i>
                                        </a>

                                    </div>

                                    {{-- ===================== --}}
                                    {{-- BARIS 2 : EXTEND --}}
                                    {{-- ===================== --}}
                                    @foreach ($row->extends as $ext)
                                        <div class="d-flex gap-1">

                                            <a href="{{ route('sik.show', $ext->workflowid) }}"
                                                class="btn btn-sm btn-outline-info" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>

                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-copy-link"
                                                data-link="{{ route('sik.show', $ext->workflowid) }}" title="Copy Link">
                                                <i class="fas fa-copy"></i>
                                            </button>


                                            <a href="{{ route('sik.edit', [
                                                'projectId' => $app_workflow->workflowid,
                                                'id' => $ext->workflowid,
                                            ]) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form
                                                action="{{ route('sik.delete', [
                                                    'projectId' => $app_workflow->workflowid,
                                                    'id' => $ext->workflowid,
                                                ]) }}"
                                                method="POST" style="display:inline-block;"
                                                onsubmit="return confirm('Yakin ingin menghapus Extend ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    @endforeach

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

    <script>
        $(document).on('click', '.btn-copy-link', function() {

            let link = $(this).data('link');

            let tempInput = document.createElement("input");
            tempInput.value = link;
            document.body.appendChild(tempInput);

            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // mobile support

            document.execCommand("copy");
            document.body.removeChild(tempInput);

            alert('Link berhasil disalin!');
        });
    </script>
@endpush
