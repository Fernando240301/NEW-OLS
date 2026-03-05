@extends('adminlte::page')

@section('title', 'Daily Activity')

@section('css')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<style>
    #calendar {
        max-width: 100%;
        margin: 0 auto;
    }
    .fc-day-selected {
    background-color: #0d6efd !important;
    color: white !important;
    border-radius: 6px;
    }
    .has-activity {
    position: relative;
}

.has-activity::after {
    content: '';
    width: 6px;
    height: 6px;
    background: red;
    border-radius: 50%;
    position: absolute;
    bottom: 4px;
    left: 50%;
    transform: translateX(-50%);
}
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- PROFILE INFO --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">NIP</th>
                    <td>{{ auth()->user()->userid }}</td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td>{{ auth()->user()->name }}</td>
                </tr>
                <tr>
                    <th>Jenis Kelamin</th>
                    <td>-</td>
                </tr>
                <tr>
                    <th>No.Telp</th>
                    <td>-</td>
                </tr>
            </table>
        </div>

        <div class="col-md-6">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">NPWP</th>
                    <td>-</td>
                </tr>
                <tr>
                    <th>Akademik</th>
                    <td>-</td>
                </tr>
                <tr>
                    <th>Jurusan</th>
                    <td>-</td>
                </tr>
                <tr>
                    <th>Universitas</th>
                    <td>-</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- BUTTON --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-light border w-100">
                Kalender Aktivitas
            </button>
        </div>

        <div class="col-md-3 text-center">
    @if($bolehInput)
        <a href="#"
           class="btn btn-primary"
           data-toggle="modal"
           data-target="#modalTambahAktivitas">
            <i class="fas fa-plus"></i> Tambah Aktivitas
        </a>
    @endif
</div>
    <br><br>
        <div class="col-md-3">
            <input type="date"
       name="tanggal"
       class="form-control"
       value="{{ $tanggal }}"
       readonly>
        </div>

    {{-- CONTENT --}}
    <div class="row">

        {{-- KALENDER --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-body table-responsive">

                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Kegiatan</th>
                                <th>Uraian</th>
                                <th>Evidence</th>
                                <th>Status</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->tanggal }}</td>
                                <td>{{ $item->jenis_kegiatan }}</td>
                                <td>{{ $item->uraian }}</td>
                                <td>{{ $item->evidences->count() }} file</td>
                                <td>
    @if($item->status == 'pending')
        <span class="badge bg-warning">Pending</span>
    @elseif($item->status == 'approved')
        <span class="badge bg-success">Approved</span>
    @else
        <span class="badge bg-danger">Rejected</span>
    @endif
</td>
     <td>

    <a href="#" class="btn btn-sm btn-info">
        Detail
    </a>

    @if(auth()->user()->role == 'manager' && $item->status == 'pending')
        <form action="{{ route('activity.approve', $item->id) }}" 
              method="POST" 
              style="display:inline;">
            @csrf
            <button class="btn btn-sm btn-success">
                Approve
            </button>
        </form>

        <form action="{{ route('activity.reject', $item->id) }}" 
              method="POST" 
              style="display:inline;">
            @csrf
            <button class="btn btn-sm btn-danger">
                Reject
            </button>
        </form>
    @endif

</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    Tidak ada data..
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $data->links() }}

                </div>
            </div>
        </div>

    </div>

</div>
<div class="modal fade" id="modalTambahAktivitas">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Tambah Aktivitas</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('activity.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="row">

                        <!-- LEFT SIDE -->
                        <div class="col-md-6">
                            <h5><b>Data Aktivitas :</b></h5>
                            <hr>

                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" 
                                       name="tanggal"
                                       class="form-control"
                                       value="{{ $tanggal }}"
                                       readonly>
                            </div>

                            <div class="form-group">
                                <label>Jenis Kegiatan</label>
                                <select name="jenis_kegiatan" class="form-control">
                                    <option value="">-Pilih-</option>
                                    <option value="Meeting">Meeting</option>
                                    <option value="Development">Development</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Project Number</label>
                                <select name="project_number" class="form-control select2">
                                    <option value="">-Pilih Project Number-</option>
                                    <option value="test1">test</option>
                                    <option value="test2">test2</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Uraian</label>
                                <textarea name="uraian" class="form-control" rows="5"></textarea>
                            </div>
                        </div>

                        <!-- RIGHT SIDE -->
                        <div class="col-md-6">
                            <h5><b>Evidence :</b></h5>
                            <hr>

                            <div class="form-group">
                                <label>Log Activity</label>
                                <select name="log_activity" class="form-control">
                                    <option value="">-tidak ada log-</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Link</label>
                                <input type="text" name="link" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>File Upload</label>
                                <input type="file" name="file_upload[]" class="form-control mb-2">
                                <input type="file" name="file_upload[]" class="form-control mb-2">
                                <input type="file" name="file_upload[]" class="form-control mb-2">
                                <input type="file" name="file_upload[]" class="form-control">
                            </div>

                            <div class="alert alert-warning mt-3">
                                <small>
                                    <b>* Evidence Wajib di isi</b><br>
                                    dapat dipilih salah satu log/link/upload<br><br>

                                    <b>** Log</b> terhubung dengan aktivitas akun pada hari yang dipilih<br>
                                    <b>** Link</b> dapat dicopy dari url aktivitas yang sedang dikerjakan<br>
                                    <b>** Upload</b> dapat berupa screenshot / dokumen bukti aktivitas
                                </small>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection

@section('plugins.Select2',true)
@section('js')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var selectedDate = "{{ $tanggal }}";

    var calendar = new FullCalendar.Calendar(
        document.getElementById('calendar'),
        {
            initialView: 'dayGridMonth',
            initialDate: selectedDate,
            height: 'auto',

            events: "{{ route('activity.events') }}",

            dateClick: function(info) {
                window.location.href =
                    "{{ route('activity.index') }}?tanggal=" + info.dateStr;
            }
        }
    );

    calendar.render();
});
</script>
@endsection