@extends('adminlte::page')

@section('title', 'Verifikasi Work Assignment')

@section('plugins.Datatables', true)

@section('content_header')
    <h1 class="text-center">VERIFIKASI WORK ASSIGNMENT</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-striped" id="verifTable">
                <thead>
                    <tr>
                        <th width="12%">Aksi</th>
                        <th>Project Number</th>
                        <th>Client</th>
                        <th>Project Name</th>
                        <th>Created By</th>
                        <th>Created Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td class="text-center">
                                {{-- PREVIEW PDF --}}
                                <a href="{{ route('work_assignment.pdf', $row->workflowid) }}"
                                    class="btn btn-danger btn-sm mb-1" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>

                                {{-- APPROVE --}}
                                <form action="{{ route('verifikasi.mm.approve', $row->workflowid) }}" method="POST"
                                    style="display:inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1"
                                        onclick="return confirm('Approve Work Assignment ini?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </td>

                            <td>{{ $row->noreg }}</td>
                            <td>{{ $row->nama_perusahaan }}</td>
                            <td>{{ $row->projectname }}</td>
                            <td>{{ $row->createuser }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->createtime)->format('d-m-Y H:i') }}</td>
                            <td>
                                <span class="badge badge-warning">
                                    Menunggu Verifikasi MM
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Tidak ada data untuk diverifikasi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            $('#verifTable').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [5, 'asc']
                ],
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
