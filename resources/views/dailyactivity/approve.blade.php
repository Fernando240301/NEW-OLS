@extends('adminlte::page')

@section('title', 'Verifikasi Aktivitas')

@section('content_header')
    <h1>Verifikasi Aktivitas</h1>
@stop

@section('content')

{{-- FILTER --}}
<div class="card mb-3 p-3" style="background:#dfe8f1;">
    <div class="row">
        <div class="col-md-2">
            <label>Tgl Awal</label>
            <input type="date" id="start_date" class="form-control">
        </div>
        <div class="col-md-2">
            <label>Tgl Akhir</label>
            <input type="date" id="end_date" class="form-control">
        </div>
        <div class="col-md-2">
            <label>Status</label>
            <select id="status" class="form-control">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Search</label>
            <input type="text" id="search" class="form-control">
        </div>
        <div class="col-md-2 mt-4">
            <button id="btnFilter" class="btn btn-primary">Filter</button>
        </div>
    </div>
</div>

{{-- TABLE --}}
<div class="card">
    <div class="card-body table-responsive">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Verifikasi</th>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Divisi</th>
                    <th>Kegiatan</th>
                    <th>Uraian</th>
                    <th>Pr num</th>
                    <th>Status</th>
                    <th>Evidence</th>
                </tr>
            </thead>
            <tbody id="approveTable"></tbody>
        </table>
        <div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Preview File</h5>
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <div class="modal-body text-center" id="previewContent">
                Loading...
            </div>

        </div>
    </div>
</div>

    </div>
</div>
@stop
@section('js')
<script>
$(document).ready(function(){

    loadData();

    $('#btnFilter').click(function(){
        loadData();
    });

    function loadData(){

        $.ajax({
            url: "{{ route('dailyactivity.approve.data') }}",
            type: "GET",
            data: {
                start: $('#start_date').val(),
                end: $('#end_date').val(),
                status: $('#status').val(),
                search: $('#search').val()
            },
            success: function(res){

                let html = '';

                if(!res || res.length === 0){
                    html = `
                        <tr>
                            <td colspan="10" class="text-center">No data found</td>
                        </tr>
                    `;
                    $('#approveTable').html(html);
                    return;
                }

                res.forEach((item, index) => {

    let badge = 'badge-warning';
    if(item.status === 'Approved') badge = 'badge-success';
    if(item.status === 'Rejected') badge = 'badge-danger';

    let actionBtn = '';

    if (item.status === 'Pending') {
        actionBtn = `
            <button class="btn btn-success btn-approve" data-id="${item.id}">
                ✔
            </button>

            <button class="btn btn-danger btn-reject" data-id="${item.id}">
                ✖
            </button>
        `;
    } 
    else if (item.status === 'Approved') {
        actionBtn = `
            <span class="badge badge-success p-2">
                Approved ✓
            </span>
        `;
    } 
    else if (item.status === 'Rejected') {
        actionBtn = `
            <span class="badge badge-danger p-2">
                Rejected ✖
            </span>
        `;
    }

    html += `
        <tr>
            <td>${index + 1}</td>

            <td>${actionBtn}</td>

            <td>${item.activity_date ?? '-'}</td>
            <td>${item.username ?? '-'}</td>
            <td>${item.divisi ?? '-'}</td>
            <td>${item.jenis_kegiatan ?? '-'}</td>
            <td>${item.uraian ?? '-'}</td>
            <td>
            ${item.project_number ? 'PR-' + item.project_number : '-'} 
            ${item.project_name ? ' | ' + item.project_name : ''}
            </td>

            <td>
                <span class="badge ${badge}">
                    ${item.status ?? '-'}
                </span>
            </td>

            <td>
                ${renderEvidence(item)}
            </td>
        </tr>
    `;
});

                $('#approveTable').html(html);
            },
            error: function(xhr){
            console.log("ERROR:", xhr.responseText);
            alert("Gagal load data approval");
        }
        });
    }
    $(document).on('click', '.btn-preview', function(){

    let file = $(this).data('file');
    let ext = file.split('.').pop().toLowerCase();

    let content = '';

    // PDF
    if(ext === 'pdf'){
        content = `<iframe src="${file}" width="100%" height="600px"></iframe>`;
    }

    // IMAGE
    else if(['jpg','jpeg','png','gif','webp'].includes(ext)){
        content = `<img src="${file}" class="img-fluid"/>`;
    }

    // VIDEO (optional)
    else if(['mp4','webm'].includes(ext)){
        content = `
            <video controls width="100%">
                <source src="${file}" type="video/mp4">
            </video>
        `;
    }

    // DEFAULT DOWNLOAD
    else{
        content = `
            <a href="${file}" target="_blank" class="btn btn-primary">
                Download File
            </a>
        `;
    }

    $('#previewContent').html(content);
    $('#previewModal').modal('show');
});

    // =========================
    // APPROVE
    // =========================
    $(document).on('click', '.btn-approve', function(){

        let id = $(this).data('id');

        if(!confirm('Approve data ini?')) return;

        $.ajax({
            url: `/dailyactivity/${id}/approve`,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res){
                loadData();
            },
            error: function(xhr){
                console.log(xhr.responseText);
                alert("Approve gagal");
            }
        });

    });

    // =========================
    // REJECT
    // =========================
    $(document).on('click', '.btn-reject', function(){

        let id = $(this).data('id');

        let reason = prompt("Masukkan alasan reject:");
        if(reason === null || reason.trim() === '') return;

        $.ajax({
            url: `/dailyactivity/${id}/reject`,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                reason: reason
            },
            success: function(res){
                loadData();
            },
            error: function(xhr){
                console.log(xhr.responseText);
                alert("Reject gagal");
            }
        });

    });

    // =========================
    // EVIDENCE RENDER SAFE
    // =========================
    function renderEvidence(item){

    let html = '';

    // FILE
    if(item.evidence){

        try {
            let files = JSON.parse(item.evidence);

            files.forEach(file => {
                html += `
                    <a href="/storage/${file}" target="_blank"
                       class="btn btn-sm btn-primary mr-1">
                       View File
                    </a>
                `;
            });

        } catch(e){
            console.log(e);
        }
    }

    // LINK
    if(item.link){
        html += `
            <a href="${item.link}" target="_blank"
               class="btn btn-sm btn-info">
               Link
            </a>
        `;
    }

    return html || '-';
}

});
</script>
@stop