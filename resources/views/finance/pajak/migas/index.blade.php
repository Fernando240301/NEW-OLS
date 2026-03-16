@extends('adminlte::page')

@section('title', 'Pajak MIGAS')

@section('content_header')
    <h1>Pajak Project MIGAS</h1>
@stop

@section('content')

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>PIC</th>
                <th>Bulan</th>
                <th>Total Akumulasi</th>
                <th>PPH21</th>
                <th>Detail PPJB</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($pics as $pic)
                <tr>

                    <td>{{ $pic->pic }}</td>

                    <td>
                        {{ date('F', mktime(0, 0, 0, $pic->bulan, 1)) }}
                    </td>

                    <td>
                        {{ number_format($pic->total, 0, ',', '.') }}
                    </td>

                    <td>
                        {{ number_format($pic->pph21, 0, ',', '.') }}
                    </td>

                    <td>

                        <button class="btn btn-info btn-sm btn-detail" data-pic="{{ $pic->pic }}"
                            data-bulan="{{ $pic->bulan }}" data-toggle="modal" data-target="#modalDetail">
                            Detail
                        </button>

                    </td>

                    <td>

                        <form method="POST" action="{{ route('pajak.migas.process.pic') }}">
                            @csrf

                            <input type="hidden" name="pic" value="{{ $pic->pic }}">
                            <input type="hidden" name="bulan" value="{{ $pic->bulan }}">

                            <button class="btn btn-success btn-sm">
                                Proses Pajak
                            </button>

                        </form>

                    </td>

                </tr>
            @endforeach

        </tbody>
    </table>

    <div class="modal fade" id="modalDetail">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Detail PPJB</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <h5>Daftar PPJB</h5>

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>No PPJB</th>
                                <th>Total</th>
                                <th>Preview PPJB</th>
                            </tr>
                        </thead>

                        <tbody id="detailBody"></tbody>

                    </table>

                    <hr>

                    <h5>Simulasi Perhitungan Pajak</h5>

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No PPJB</th>
                                <th>Fee</th>
                                <th>Akumulasi</th>
                                <th>Tarif</th>
                                <th>Pajak</th>
                            </tr>
                        </thead>

                        <tbody id="simulasiBody">

                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPreview">

        <div class="modal-dialog modal-xl">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Preview PPJB</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body" style="height:80vh">

                    <iframe id="pdfFrame" style="width:100%;height:100%;border:none;">
                    </iframe>

                </div>

            </div>

        </div>

    </div>

@stop


@section('js')

    <script>
        $(document).ready(function() {

            /*
            =====================================
            AUTO HITUNG PPH23 (2%)
            =====================================
            */

            $('.pph23').on('focus', function() {

                let row = $(this).closest('tr');

                let total = parseFloat(row.find('.total').data('total'));

                let pph23 = total * 0.02;

                $(this).val(Math.round(pph23));

            });

        });
    </script>

    <script>
        $('.btn-detail').click(function() {

            let pic = $(this).data('pic');
            let bulan = $(this).data('bulan');

            $.get('/pajak-migas/detail/' + pic, {
                bulan: bulan
            }, function(data) {

                let html = '';
                let htmlSimulasi = '';

                // LIST PPJB
                data.ppjbs.forEach(function(row) {

                    html += `
        <tr>
            <td>${row.no_ppjb}</td>
            <td>${parseInt(row.total).toLocaleString()}</td>

            <td>
                <button 
                class="btn btn-primary btn-sm btn-preview"
                data-url="${row.pdf_url}">
                Preview
                </button>
            </td>

        </tr>
        `;
                });

                $('#detailBody').html(html);


                // SIMULASI PAJAK
                data.simulasi.forEach(function(row, index) {

                    htmlSimulasi += `
        <tr>

            <td>${index+1}</td>

            <td>${row.no_ppjb}</td>

            <td>${parseInt(row.fee).toLocaleString()}</td>

            <td>${parseInt(row.akumulasi).toLocaleString()}</td>

            <td>${row.tarif}</td>

            <td>${parseInt(row.pajak).toLocaleString()}</td>

        </tr>
        `;
                });

                $('#simulasiBody').html(htmlSimulasi);

            });

        });

        $(document).on('click', '.btn-preview', function() {

            let url = $(this).data('url');

            $('#pdfFrame').attr('src', url);

            $('#modalPreview').modal('show');

        });
    </script>
@stop
