@extends('adminlte::page')

@section('title', 'Units Detail')

@section('plugins.Datatables', true)

@section('content_header')

    <h3 class="text-center text-primary mb-4">
        {{ $workflowdata['projectname'] ?? '' }}
    </h3>

@endsection


@section('content')

    <x-project-menu :workflowid="$workflowid" />


    {{-- ACTION --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex" style="gap:10px">

            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddUnit">
                <i class="fas fa-plus"></i> Tambah Unit
            </button>

            <button class="btn btn-info">
                <i class="fas fa-upload"></i> Upload Database Report
            </button>

            <a class="btn btn-success">
                <i class="fas fa-database"></i> Database COI
            </a>

        </div>
    </div>



    {{-- EQUIPMENT GRID --}}
    <div class="equipment-grid mb-4">

        @foreach ($scopes as $scope)
            <div class="equipment-card">

                <div class="equipment-title">
                    <i class="fas fa-cogs text-primary mr-2"></i>
                    {{ $scope->tipe_nama }}
                </div>

                <div class="equipment-sub">
                    {{ $scope->kategori_nama }}
                </div>

                <div class="equipment-progress">

                    <div class="progress" style="height:6px">

                        <div class="progress-bar bg-success"
                            style="width: {{ $totalUnits > 0 ? ($done / $totalUnits) * 100 : 0 }}%">
                        </div>

                    </div>

                </div>

                <div class="equipment-info">
                    {{ $done }} / {{ $unitsByType[$scope->tipe] ?? 0 }} Units
                </div>

            </div>
        @endforeach

    </div>



    {{-- TABLE --}}
    <div class="card shadow-sm">

        <div class="card-body p-0">

            <table id="unitTable" class="table table-hover mb-0">

                <thead class="thead-light">

                    <tr>
                        <th width="60">No</th>
                        <th>Date</th>
                        <th>Tag</th>
                        <th>SN</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Inspected by</th>
                        <th width="120">Action</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-database fa-2x mb-2"></i>
                            <br>
                            Tidak ada data unit
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>



    {{-- MODAL ADD UNIT --}}
    <div class="modal fade" id="modalAddUnit">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header bg-primary">

                    <h5 class="modal-title">
                        Tambah Unit
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>



                <div class="modal-body">


                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <button id="btnBack" class="btn btn-light btn-sm" style="display:none">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>

                        <div id="stepTitle" class="font-weight-bold text-center w-100">
                            Choose Equipment
                        </div>

                        <div style="width:60px"></div>

                    </div>



                    {{-- STEP INDICATOR --}}
                    <div class="step-indicator">

                        <div class="step step-equipment active">Equipment</div>
                        <div class="step step-type">Type</div>
                        <div class="step step-category">Category</div>
                        <div class="step step-form">Form</div>

                    </div>



                    {{-- STEP 1 --}}
                    <div id="stepEquipment">

                        <div class="choice-grid">

                            @foreach ($scopes->unique('jenis') as $scope)
                                <div class="choice-card equipment-choice" data-id="{{ $scope->jenis }}">

                                    <i class="fas fa-cogs"></i>

                                    <div class="choice-name">
                                        {{ $scope->jenis_nama }}
                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </div>



                    {{-- STEP 2 --}}
                    <div id="stepType" style="display:none"></div>


                    {{-- STEP 3 --}}
                    <div id="stepCategory" style="display:none"></div>


                    {{-- STEP 4 --}}
                    <div id="stepForm" style="display:none"></div>


                </div>

            </div>

        </div>

    </div>


@endsection



@section('js')

    <script>
        $(document).ready(function() {

            $('#unitTable').DataTable({

                responsive: true,
                autoWidth: false,
                pageLength: 10,

                language: {
                    search: "Pencarian:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data"
                }

            });

        });

        let currentStep = 1;
        let selectedType = null;

        function setStep(step) {

            currentStep = step;

            $('.step').removeClass('active');

            if (step === 1) {
                $('.step-equipment').addClass('active');
                $('#stepTitle').text('Choose Equipment');
                $('#btnBack').hide();
            }

            if (step === 2) {
                $('.step-type').addClass('active');
                $('#stepTitle').text('Choose Type');
                $('#btnBack').show();
            }

            if (step === 3) {
                $('.step-category').addClass('active');
                $('#stepTitle').text('Choose Category');
                $('#btnBack').show();
            }

            if (step === 4) {
                $('.step-form').addClass('active');
                $('#stepTitle').text('Unit Form');
                $('#btnBack').show();
            }

        }



        $(document).on('click', '.equipment-choice', function() {

            let id = $(this).data('id');

            setStep(2);

            $('#stepEquipment').hide();

            $.get('/units/get-types/' + id, function(html) {

                $('#stepType').html(html).fadeIn();

            });

        });



        $(document).on('click', '.type-choice', function() {

            selectedType = $(this).data('id');

            setStep(3);

            $('#stepType').hide();

            $.get('/units/get-categories/' + selectedType, function(html) {

                $('#stepCategory').html(html).fadeIn();

            });

        });

        $(document).on('click', '.category-choice', function() {

            let categoryId = $(this).data('id');

            let projectId = "{{ $workflowid }}";
            let unitId = "{{ $unit->workflowid }}";

            window.location.href =
                "/units/form/" + projectId + "/" + unitId + "/" + selectedType + "/" + categoryId;

        });

        $('#btnBack').click(function() {

            if (currentStep === 2) {

                setStep(1);

                $('#stepType').hide();
                $('#stepEquipment').show();

            } else if (currentStep === 3) {

                setStep(2);

                $('#stepCategory').hide();
                $('#stepType').show();

            } else if (currentStep === 4) {

                setStep(3);

                $('#stepForm').hide();
                $('#stepCategory').show();

            }

        });



        $('#modalAddUnit').on('shown.bs.modal', function() {

            setStep(1);

            $('#stepEquipment').show();
            $('#stepType').hide();
            $('#stepCategory').hide();
            $('#stepForm').hide();

        });
    </script>

@endsection



@push('css')
    <style>
        .equipment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 15px;
        }

        .equipment-card {
            background: #fff;
            border-radius: 10px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .equipment-title {
            font-weight: 600;
        }

        .equipment-sub {
            font-size: 12px;
            color: #777;
            margin-bottom: 8px;
        }



        .choice-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
        }

        .choice-card {
            background: #fff;
            border-radius: 10px;
            padding: 18px;
            text-align: center;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            transition: 0.25s;
        }

        .choice-card i {
            font-size: 22px;
            color: #007bff;
            margin-bottom: 6px;
        }

        .choice-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            border-color: #007bff;
        }



        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .step {
            flex: 1;
            text-align: center;
            padding: 6px;
            font-size: 12px;
            border-bottom: 2px solid #e5e7eb;
        }

        .step.active {
            border-color: #007bff;
            color: #007bff;
            font-weight: 600;
        }
    </style>
@endpush
