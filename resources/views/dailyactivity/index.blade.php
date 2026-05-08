@extends('adminlte::page')

@section('title', 'Daily Activity')

@section('content_header')
    <h1>Daily Activity</h1>
@stop

{{-- CSS --}}
@section('css')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<style>


/* 🔥 Kalau dropdown di dalam modal */

/* MODAL */
#modalActivity .modal-content{
    border-radius: 14px;
    overflow: hidden;
}

#modalActivity .card{
    border-radius: 12px;
}

#modalActivity .card-header{
    border-bottom: 1px solid #eee;
    padding: 14px 20px;
}

#modalActivity .card-body{
    padding: 20px;
}

#modalActivity .form-control{
    border-radius: 8px;
    height: 42px;
}

#modalActivity textarea.form-control{
    height: auto;
}

#modalActivity label{
    font-size: 14px;
    color: #444;
}

#modalActivity .btn{
    border-radius: 8px;
    min-width: 110px;
}

#modalActivity .alert{
    border-radius: 10px;
}

#modalActivity .custom-file-label{
    border-radius: 8px;
}

#modalActivity .modal-header{
    padding: 16px 24px;
}

#modalActivity .modal-footer{
    padding: 16px 24px;
}

.selected-date {
    background-color: #28a745 !important;
    color: #fff !important;
    border-radius: 8px;
}
.dot-marker {
    width: 8px;
    height: 8px;
    background-color: green;
    border-radius: 50%;
    position: absolute;
    top: 5px;
    right: 5px;
}
.fc-daygrid-day {
    position: relative;
}
.fc-daygrid-day.activity-dot::after {
    content: '';
    width: 8px;
    height: 8px;
    background-color: green;
    border-radius: 50%;
    position: absolute;
    top: 5px;
    right: 5px;
}

.selected-date .fc-daygrid-day-number {
    font-weight: bold;
}
.fc-daygrid-day:hover {
    cursor: pointer;
    background-color: #f1f1f1;
}

.modal.show {
    pointer-events: auto;
}
.fc .selected-date {
    background-color: #03681b !important;
    color: #d71b1b !important;
}

.fc .selected-date .fc-daygrid-day-number {
    color: #ca3131 !important;
    font-weight: bold;
}
#selected_date {
    background-color: #e9f7ef;
    font-weight: bold;
}

/* 🔥 Optional: biar lebih enak dilihat */
select.form-control option {
    padding: 5px;
}
#calendar {
    position: relative;
    z-index: 1;
}
#selected_date {
    position: relative;
}
</style>
@stop
@section('content')

<div class="mb-3">
    <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

{{-- DATA USER --}}
<div class="row mb-3">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr><th>NIP</th><td>{{ $user->detail->nip ?? '-' }}</td></tr>
            <tr><th>Nama</th><td>{{ $user->name }}</td></tr>
            <tr><th>Jenis Kelamin</th><td>{{ $user->detail->jenis_kelamin ?? '-' }}</td></tr>
            <tr><th>No.Telp</th><td>{{ $user->detail->no_telp ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="col-md-6">
        <table class="table table-bordered">
            <tr><th>NPWP</th><td>{{ $user->detail->npwp ?? '-' }}</td></tr>
            <tr><th>Akademik</th><td>{{ $user->detail->academic ?? '-' }}</td></tr>
            <tr><th>Jurusan</th><td>{{ $user->detail->jurusan ?? '-' }}</td></tr>
            <tr><th>Universitas</th><td>{{ $user->detail->university ?? '-' }}</td></tr>
        </table>
    </div>
</div>

{{-- BUTTON --}}
<div class="d-flex justify-content-between align-items-start mb-3">

    <div class="col-md-6 p-0 mt-2">
        <div class="card text-center">
            <div class="card-body">
                Kalender Aktivitas
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center">

    <!-- BUTTON -->
    <button id="btnTambah" class="btn btn-primary mr-4">
        <i class="fas fa-plus"></i> Tambah Aktivitas
    </button>

    <!-- TANGGAL -->
    <div class="text-center">

        <input type="text"
               id="selected_date"
               class="form-control text-center"
               readonly
               style="width:220px;">
    </div>

</div>
</div>

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

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Kegiatan</th>
                            <th>Uraian</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="activityTable">
                    @forelse($activities as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->activity_date }}</td>
                            <td>{{ $item->jenis_kegiatan }}</td>
                            <td>{{ $item->uraian }}</td>
                            <td>
                                <span class="badge 
                                    {{ $item->status == 'Approved' ? 'badge-success' : 
                                    ($item->status == 'Pending' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $item->status ?? 'Pending' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('dailyactivity.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                @if(auth()->user()->role == 'manager' && $item->status != 'Approved')
                                    <button class="btn btn-sm btn-success btn-approve" data-id="{{ $item->id }}">
                                        Approve
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data..</td>
                        </tr>
                    @endforelse
                </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@stop

{{-- JS --}}
@section('js')


<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Select2 JS (INI YANG KURANG) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    let userRole = "{{ auth()->user()->role }}";
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    let selectedDate = new Date().toISOString().split('T')[0];
$('#selected_date').val(selectedDate);

// =========================
// VALIDASI TANGGAL
// =========================
function checkDateLimit(dateStr){

    let today = new Date();

    // batas minimal = kemarin
    let limitDate = new Date();
    limitDate.setDate(today.getDate() - 1);

    // reset jam
    today.setHours(0,0,0,0);
    limitDate.setHours(0,0,0,0);

    let selected = new Date(dateStr);
    selected.setHours(0,0,0,0);

    // disable jika lebih lama dari H-1
    if(selected < limitDate){
        $('#btnTambah').prop('disabled', true);

        $('#btnTambah')
            .removeClass('btn-primary')
            .addClass('btn-secondary');

    } else {

        $('#btnTambah').prop('disabled', false);

        $('#btnTambah')
            .removeClass('btn-secondary')
            .addClass('btn-primary');
    }
}

    // cek pertama kali
    checkDateLimit(selectedDate);


    let activityDates = [];

    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',
        height: 600,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        // =========================
        // AMBIL DATA DARI SERVER
        // =========================
        events: function (fetchInfo, successCallback, failureCallback) {

            $.ajax({
                url: '/activity-events', // ⚠️ PASTIKAN INI BENAR
                type: 'GET',
                success: function (res) {

                    console.log('EVENTS:', res);

                    // simpan tanggal activity
                    activityDates = res.map(e => e.start);

                    successCallback(res);
                },
                error: function (err) {
                    console.log('ERROR EVENTS:', err);
                    failureCallback(err);
                }
            });
        },

        // =========================
        // CLICK TANGGAL
        // =========================
        dateClick: function (info) {

            selectedDate = info.dateStr;
            $('#selected_date').val(selectedDate);

            // cek validasi tanggal
            checkDateLimit(selectedDate);

            loadTable(selectedDate);
        },

        // =========================
        // HIGHLIGHT TANGGAL DIPILIH
        // =========================
        dayCellClassNames: function (arg) {
            if (arg.dateStr === selectedDate) {
                return ['selected-date'];
            }
            return [];
        },

        // =========================
        // TAMBAH DOT DI CELL
        // =========================
        dayCellDidMount: function (arg) {

            let dateStr = arg.date.toISOString().split('T')[0];

            if (activityDates.includes(dateStr)) {

                let dot = document.createElement('div');
                dot.style.width = '8px';
                dot.style.height = '8px';
                dot.style.backgroundColor = 'green';
                dot.style.borderRadius = '50%';
                dot.style.position = 'absolute';
                dot.style.bottom = '6px';
                dot.style.left = '50%';
                dot.style.transform = 'translateX(-50%)';

                arg.el.style.position = 'relative';
                arg.el.appendChild(dot);
            }
        }
    });

    calendar.render();
    loadTable(selectedDate);

    // =========================
    // BUTTON MODAL
    // =========================
    $('#btnTambah').on('click', function () {

        let selectedDate = $('#selected_date').val();

        if (!selectedDate) {
            alert('Pilih tanggal dulu!');
            return;
        }

        $('#activity_date').val(selectedDate);
        $('#modalActivity').modal('show');
    });
    function loadTable(date) {

    $.ajax({
        url: "{{ route('dailyactivity.filter') }}",
        type: "GET",
        data: { date: date },

        success: function (res) {

            let html = '';

            if (res.length === 0) {
                html = `
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data</td>
                    </tr>
                `;
            } else {

                res.forEach((item, index) => {

                    // 🔥 BADGE STATUS
                    let badge = 'badge-success';
                    if(item.status === 'Pending') badge = 'badge-warning';
                    if(item.status === 'Rejected') badge = 'badge-danger';

                    // 🔥 BUTTON APPROVE (KHUSUS MANAGER)
                    let approveBtn = '';
                    if(userRole === 'manager' && item.status !== 'Approved'){
                        approveBtn = `
                            <button class="btn btn-sm btn-success btn-approve" data-id="${item.id}">
                                Approve
                            </button>
                        `;
                    }

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.activity_date}</td>
                            <td>${item.jenis_kegiatan}</td>
                            <td>${item.uraian ?? '-'}</td>
                            <td><span class="badge ${badge}">${item.status ?? 'Pending'}</span></td>
                            <td>
                                <a href="/dailyactivity/${item.id}/edit" class="btn btn-sm btn-warning">Edit</a>
                                ${approveBtn}
                            </td>
                        </tr>
                    `;
                });
            }

            $('#activityTable').html(html);
        }
    });
}
$(document).on('click', '.btn-approve', function(){

    let id = $(this).data('id');

    if(confirm('Approve activity ini?')){
        $.ajax({
            url: `/dailyactivity/${id}/approve`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res){
                alert('Berhasil di approve');
                loadTable($('#selected_date').val());
            },
            error: function(err){
                console.log(err);
                alert('Gagal approve');
            }
        });
    }
});

});
</script>

<!-- Modal Tambah Activity -->
<div class="modal fade" id="modalActivity" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Aktivitas
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="formActivity"
                  action="{{ route('dailyactivity.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="modal-body p-4">

                    <div class="row">

                        <!-- ========================= -->
                        <!-- LEFT -->
                        <!-- ========================= -->
                        <div class="col-md-6">

                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 font-weight-bold text-primary">
                                        Data Aktivitas
                                    </h6>
                                </div>

                                <div class="card-body">

                                    <!-- TANGGAL -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            Tanggal
                                        </label>

                                        <input type="date"
                                               name="activity_date"
                                               id="activity_date"
                                               class="form-control"
                                               readonly>
                                    </div>

                                    <!-- JENIS -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            Jenis Kegiatan
                                        </label>

                                        <select name="jenis_kegiatan"
                                                class="form-control"
                                                required>

                                            <option value="">- Pilih Kegiatan -</option>

                                            @foreach($kegiatan as $kode => $nama)
                                                <option value="{{ $kode }}">
                                                    {{ $kode }} - {{ $nama }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <!-- PROJECT -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            Project Number
                                        </label>

                                        <select name="project_number"
                                                id="project_number"
                                                class="form-control select2">

                                            <option value="">
                                                - Pilih Project -
                                            </option>

                                            @foreach($data as $project)
                                                <option value="{{ $project->project_number }}">
                                                    {{ $project->project_number }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <!-- URAIAN -->
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">
                                            Uraian Aktivitas
                                        </label>

                                        <textarea name="uraian"
                                                  class="form-control"
                                                  rows="6"
                                                  placeholder="Masukkan detail aktivitas..."></textarea>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- ========================= -->
                        <!-- RIGHT -->
                        <!-- ========================= -->
                        <div class="col-md-6">

                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 font-weight-bold text-success">
                                        Evidence Aktivitas
                                    </h6>
                                </div>

                                <div class="card-body">

                                    <!-- LINK -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            Link Evidence
                                        </label>

                                        <input type="text"
                                               name="link"
                                               class="form-control"
                                               placeholder="https://...">
                                    </div>

                                    <!-- FILE -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            Upload File
                                        </label>

                                        <div class="custom-file mb-2">
                                            <input type="file"
                                                   name="file_upload[]"
                                                   class="custom-file-input">

                                            <label class="custom-file-label">
                                                Pilih file...
                                            </label>
                                        </div>

                                        <div class="custom-file mb-2">
                                            <input type="file"
                                                   name="file_upload[]"
                                                   class="custom-file-input">

                                            <label class="custom-file-label">
                                                Pilih file...
                                            </label>
                                        </div>

                                        <div class="custom-file mb-2">
                                            <input type="file"
                                                   name="file_upload[]"
                                                   class="custom-file-input">

                                            <label class="custom-file-label">
                                                Pilih file...
                                            </label>
                                        </div>

                                        <div class="custom-file">
                                            <input type="file"
                                                   name="file_upload[]"
                                                   class="custom-file-input">

                                            <label class="custom-file-label">
                                                Pilih file...
                                            </label>
                                        </div>
                                    </div>

                                    <!-- ALERT -->
                                    <div class="alert alert-warning mt-4 mb-0">

                                        <h6 class="font-weight-bold">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Informasi Evidence
                                        </h6>

                                        <small>
                                            • Evidence wajib diisi salah satu<br>
                                            • Link dapat berupa URL pekerjaan<br>
                                            • Upload dapat berupa screenshot / dokumen<br>
                                            • File dapat lebih dari satu
                                        </small>

                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">

                    <button type="submit"
                            class="btn btn-success px-4">

                        <i class="fas fa-save mr-1"></i>
                        Simpan
                    </button>

                    <button type="button"
                            class="btn btn-secondary px-4"
                            data-dismiss="modal">

                        Batal
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
<script>
   $('#formActivity').on('submit', function(e){
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(res){
            alert('Data berhasil disimpan');
            $('#modalActivity').modal('hide');
            location.reload();
        },
        error: function(err){
            console.log(err.responseText);
            alert('Gagal simpan data');
        }
    });
});
$(document).on('shown.bs.modal', '#modalActivity', function () {

    // destroy dulu kalau sudah pernah init (biar gak double)
    if ($.fn.select2 && $('#project_number').hasClass("select2-hidden-accessible")) {
        $('#project_number').select2('destroy');
    }

    $('#project_number').select2({
        placeholder: 'Cari PR Number...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalActivity')
    });

});
</script>

@stop