@extends('adminlte::page')

@section('plugins.FontAwesome', true)

@section('title', 'RPUM')

@section('content_header')
<h1 class="mb-3">RPUM (Realisasi Pembayaran)</h1>
@stop

@section('content')

@section('css')
<style>
    .table td {
        vertical-align: middle;
    }

    .card {
        border-radius: 12px;
    }

    .btn-success {
        border-radius: 8px;
        font-weight: 500;
    }

    input.form-control-sm {
        border-radius: 8px;
    }

    table.dataTable tbody tr.shown {
        background: #f1f7ff;
    }

    .modal-content {
        border-radius: 12px;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }

    iframe {
        background: #f8f9fa;
    }

    #buktiContainer img {
        max-height: 500px;
    }

    .btn-group .btn {
        border-radius: 8px !important;
    }

    .btn-light {
        background: #f8f9fa;
    }

    .btn-light:hover {
        background: #e9ecef;
    }

    .badge {
        font-size: 12px;
        border-radius: 8px;
    }

    td {
        max-width: 200px;
    }
</style>
@stop

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="mb-3 text-end">
            <a href="{{ route('rpum.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>

        <table class="table table-hover align-middle" id="rpumTable">
            <thead class="table-light">
                <tr>
                    <th>No PPJB</th>
                    <th>Project No</th> <!-- 🔥 TAMBAH -->
                    <th>Description</th>
                    <th>PIC</th>
                    <th>Total</th>
                    <th>Input RPUM</th>
                </tr>
            </thead>
        </table>

    </div>
</div>


<div class="modal fade" id="rpumModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="rpumForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Input RPUM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" id="ppjb_id">

                    <div class="mb-2">
                        <label>Tanggal Transfer</label>
                        <input type="date" name="tanggal_transfer" id="tanggal_transfer"
                            class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah"
                            class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label d-block">Jenis Pembayaran</label>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_pembayaran" id="ca" value="CA" required>
                            <label class="form-check-label" for="ca">CA</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_pembayaran" id="biaya" value="Biaya">
                            <label class="form-check-label" for="biaya">Biaya</label>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label>Bukti Transfer</label>
                        <input type="file" name="bukti_transfer"
                            class="form-control" accept="application/pdf,image/*" required>
                    </div>

                    <div id="previewArea" class="mt-3 text-center"></div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success w-100">
                        <i class="fas fa-check"></i> Simpan RPUM
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- MODAL PREVIEW PDF -->
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf"></i> Preview PPJB
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <iframe id="pdfFrame"
                    src=""
                    width="100%"
                    height="600px"
                    style="border: none;">
                </iframe>
            </div>

            <div class="modal-footer">
                <a href="#" id="downloadPdf" target="_blank" class="btn btn-success">
                    <i class="fas fa-download"></i> Download
                </a>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalBukti" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-paperclip"></i> Bukti Transfer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center" id="buktiContainer">
                <!-- isi dynamic -->
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalHistory">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="fas fa-list"></i> History Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Bukti</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody"></tbody>
                </table>

            </div>

        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(function () {

    let table = $('#rpumTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('rpum.datatables') }}",
        columns: [
            { data: 'no_ppjb' },
            { data: 'project_no' }, // 🔥 TAMBAH INI
            { data: 'description' },
            { data: 'pic' },
            { data: 'total_format', orderable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    // ============================
    // OPEN PREVIEW MODAL
    // ============================
    $('#rpumTable').on('click', '.btn-preview', function () {

        let url = $(this).data('url');

        // set iframe
        $('#pdfFrame').attr('src', url);

        // set download link
        $('#downloadPdf').attr('href', url);

        // show modal
        $('#modalPreview').modal('show');
    });

    // ============================
    // CLEAR IFRAME SAAT CLOSE
    // ============================
    $('#modalPreview').on('hidden.bs.modal', function () {
        $('#pdfFrame').attr('src', '');
    });

    // =========================
    // OPEN MODAL
    // =========================
    $(document).on('click', '.btn-rpum', function () {

        let id = $(this).data('id');
        let total = $(this).data('total');

        $('#ppjb_id').val(id);
        $('#jumlah').val('');

        // default pilih CA
        $('input[name="jenis_pembayaran"][value="CA"]').prop('checked', true);

        // default today
        let today = new Date().toISOString().split('T')[0];
        $('#tanggal_transfer').val(today);

        $('#rpumForm').attr('action', '/rpum/store/' + id);

        $('#previewArea').html('');

        $('#rpumModal').modal('show');
    });

    // =========================
    // FILE PREVIEW
    // =========================
    $('input[name="bukti_transfer"]').on('change', function () {

        let file = this.files[0];
        if (!file) return;

        let url = URL.createObjectURL(file);

        if (file.type === 'application/pdf') {
            $('#previewArea').html(`
                <iframe src="${url}" width="100%" height="300px"></iframe>
            `);
        } else {
            $('#previewArea').html(`
                <img src="${url}" class="img-fluid rounded">
            `);
        }
    });

    // ============================
    // PREVIEW BUKTI TRANSFER
    // ============================
    $('#rpumTable').on('click', '.btn-preview-bukti', function () {

        let url = $(this).data('url');

        let ext = url.split('.').pop().toLowerCase();

        let content = '';

        if (ext === 'pdf') {
            content = `<iframe src="${url}" width="100%" height="500px"></iframe>`;
        } else {
            content = `<img src="${url}" class="img-fluid rounded shadow">`;
        }

        $('#buktiContainer').html(content);

        $('#modalBukti').modal('show');
    });

    // =======================
    // OPEN HISTORY MODAL
    // =======================
    $(document).on('click', '.btn-history', function () {

        let id = $(this).data('id');

        $('#historyBody').html('<tr><td colspan="3">Loading...</td></tr>');

        $.get('/rpum/history/' + id, function (res) {

            let html = '';

            if (res.length === 0) {
                html = '<tr><td colspan="3" class="text-center">Belum ada pembayaran</td></tr>';
            }

            res.forEach(r => {

                let url = '/storage/' + r.bukti_transfer;

                html += `
                    <tr>
                        <td>${r.tanggal_transfer}</td>
                        <td>Rp ${parseInt(r.jumlah).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-sm btn-info btn-preview-bukti"
                                data-url="${url}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            $('#historyBody').html(html);
        });

        $('#modalHistory').modal('show');
    });

    $(document).on('click', '.btn-preview-bukti', function () {

        let url = $(this).data('url');

        $('#pdfFrame').attr('src', url);
        $('#downloadPdf').attr('href', url);

        $('#modalPreview').modal('show');
    });

});

$('#rpumForm').on('submit', function (e) {

    let file = $('input[name="bukti_transfer"]').val();

    if (!file) {
        e.preventDefault();
        alert('Bukti transfer wajib diupload!');
        return false;
    }
});
</script>
@stop