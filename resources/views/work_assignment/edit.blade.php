@extends('adminlte::page')

@section('title', 'Edit Client')

@section('content_header')
    <h1>Edit Project</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('work_assignment.update', $app_workflow->workflowid) }}" method="POST"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Project Type</label>
                    <select name="project_type" id="project_type" class="form-control">
                        <option value="">-- Pilih --</option>

                        <option value="PR"
                            {{ old('project_type', $workflowdata['project_type'] ?? '') == 'PR' ? 'selected' : '' }}>
                            PR
                        </option>

                        <option value="FR"
                            {{ old('project_type', $workflowdata['project_type'] ?? '') == 'FR' ? 'selected' : '' }}>
                            FR
                        </option>

                        <option value="NP"
                            {{ old('project_type', $workflowdata['project_type'] ?? '') == 'NP' ? 'selected' : '' }}>
                            NP
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Project Name</label>
                    <input type="text" name="projectname" class="form-control"
                        value="{{ old('projectname', $app_workflow->projectname) }}" required>
                </div>

                <div class="form-group">
                    <label>Client Name</label>
                    <select name="client" class="form-control select2bs4" required>
                        <option value="">-- Pilih Client --</option>
                        @foreach ($namaclient as $k)
                            <option value="{{ $k->pemohonid }}"
                                {{ $app_workflow->client == $k->pemohonid ? 'selected' : '' }}>
                                {{ $k->nama_perusahaan }}
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Contract Number</label>
                    <input type="text" name="no_kontrak" class="form-control"
                        value="{{ old('no_kontrak', $workflowdata['no_kontrak']) }}" required>
                </div>

                <div class="form-group">
                    <label>Issued Contract</label>
                    <input type="date" name="tanggal_kontrak" class="form-control"
                        value="{{ old('tanggal_kontrak', $workflowdata['tanggal_kontrak']) }}" required>
                </div>

                <div class="form-group">
                    <label>Expired Contract</label>
                    <input type="date" name="tanggal_akhir" class="form-control"
                        value="{{ old('tanggal_akhir', $workflowdata['tanggal_akhir']) }}" required>
                </div>

                <div class="form-group">
                    <label>Contract Price</label>
                    <input type="text" name="harga_kontrak" class="form-control"
                        value="{{ old('harga_kontrak', $workflowdata['harga_kontrak']) }}" required>
                </div>

                <div class="form-group">
                    <label for="lokasi_kantor">Office Address</label>
                    <textarea name="lokasi_kantor" id="lokasi_kantor" class="form-control" rows="3"
                        placeholder="Silahkan masukan Alamat Kantor">{{ old('lokasi_kantor', $workflowdata['lokasi_kantor'] ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="alamat_perusahaan">Site Address</label>
                    <textarea name="lokasi_lapangan" id="lokasi_lapangan" class="form-control" rows="3"
                        placeholder="Silahkan masukan Alamat Site">{{ old('lokasi_lapangan', $workflowdata['lokasi_lapangan'] ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Contact Person Client</label>
                    <input type="text" name="contact_person" class="form-control"
                        value="{{ old('contact_person', $workflowdata['contact_person']) }}" required>
                </div>

                <div class="form-group">
                    <label>Number Phone Client</label>
                    <input type="text" name="no_hp" class="form-control"
                        value="{{ old('no_hp', $workflowdata['no_hp']) }}" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="text" name="email" class="form-control"
                        value="{{ old('email', $workflowdata['email']) }}" required>
                </div>

                <div class="form-group">
                    <label>Contact Person SA/SE/SR</label>
                    <input type="text" name="contact_person1" class="form-control"
                        value="{{ old('contact_person1', $workflowdata['contact_person1']) }}" required>
                </div>

                <div class="form-group">
                    <label>Number Phone SA/SE/SR</label>
                    <input type="text" name="no_hp1" class="form-control"
                        value="{{ old('no_hp1', $workflowdata['no_hp1']) }}" required>
                </div>

                <div class="form-group">
                    <label>Lokasi Pengujian PSV</label>
                    <input type="text" name="lokasiujipsv" class="form-control"
                        value="{{ old('lokasiujipsv', $workflowdata['lokasiujipsv'] ?? '') }}">

                </div>

                <div class="form-group">
                    <label for="pidp">Termin Pembayaran</label>
                    <textarea name="pidp" id="pidp" class="form-control" rows="3"
                        placeholder="Silahkan masukan Termin Pembayaran">{{ old('pidp', $workflowdata['pidp'] ?? '') }}</textarea>
                </div>

                <hr>
                <b>CONDITION</b>

                <div class="form-group">
                    <label>Mob Demob</label>
                    <select name="mobdemob" id="mobdemob" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="MIT"
                            {{ old('mobdemob', $workflowdata['mobdemob'] ?? '') == 'MIT' ? 'selected' : '' }}>
                            MIT
                        </option>
                        <option value="Client"
                            {{ old('mobdemob', $workflowdata['mobdemob'] ?? '') == 'Client' ? 'selected' : '' }}>
                            Client
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Akomodasi</label>
                    <select name="akomodasi" id="akomodasi" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="MIT"
                            {{ old('akomodasi', $workflowdata['akomodasi'] ?? '') == 'MIT' ? 'selected' : '' }}>
                            MIT
                        </option>
                        <option value="Client"
                            {{ old('akomodasi', $workflowdata['akomodasi'] ?? '') == 'Client' ? 'selected' : '' }}>
                            Client
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lokal Transport</label>
                    <select name="lokaltransport" id="lokaltransport" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="MIT"
                            {{ old('lokaltransport', $workflowdata['lokaltransport'] ?? '') == 'MIT' ? 'selected' : '' }}>
                            MIT
                        </option>
                        <option value="Client"
                            {{ old('lokaltransport', $workflowdata['lokaltransport'] ?? '') == 'Client' ? 'selected' : '' }}>
                            Client
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Meals</label>
                    <select name="meals" id="meals" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="MIT"
                            {{ old('meals', $workflowdata['meals'] ?? '') == 'MIT' ? 'selected' : '' }}>
                            MIT
                        </option>
                        <option value="Client"
                            {{ old('meals', $workflowdata['meals'] ?? '') == 'Client' ? 'selected' : '' }}>
                            Client
                        </option>
                    </select>
                </div>

                <hr>
                <b>LAMPIRAN PENDAMPING INVOICE</b>

                <div class="form-group">
                    <label>Invoice Asli</label>
                    <select name="invoiceasli" id="invoiceasli" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak"
                            {{ old('invoiceasli', $workflowdata['invoiceasli'] ?? '') == 'Tidak' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="Iya"
                            {{ old('invoiceasli', $workflowdata['invoiceasli'] ?? '') == 'Iya' ? 'selected' : '' }}>
                            Iya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>BAST/BASTP</label>
                    <select name="bastp" id="bastp" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak"
                            {{ old('bastp', $workflowdata['bastp'] ?? '') == 'Tidak' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="Iya"
                            {{ old('bastp', $workflowdata['bastp'] ?? '') == 'Iya' ? 'selected' : '' }}>
                            Iya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Payment Approval</label>
                    <select name="paymentapproval" id="paymentapproval" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak"
                            {{ old('paymentapproval', $workflowdata['paymentapproval'] ?? '') == 'Tidak' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="Iya"
                            {{ old('paymentapproval', $workflowdata['paymentapproval'] ?? '') == 'Iya' ? 'selected' : '' }}>
                            Iya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Copy Lampiran C</label>
                    <select name="copylampiranc" id="copylampiranc" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak"
                            {{ old('copylampiranc', $workflowdata['copylampiranc'] ?? '') == 'Tidak' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="Iya"
                            {{ old('copylampiranc', $workflowdata['copylampiranc'] ?? '') == 'Iya' ? 'selected' : '' }}>
                            Iya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Copy Lampiran D</label>
                    <select name="copylampirand" id="copylampirand" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak"
                            {{ old('copylampirand', $workflowdata['copylampirand'] ?? '') == 'Tidak' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="Iya"
                            {{ old('copylampirand', $workflowdata['copylampirand'] ?? '') == 'Iya' ? 'selected' : '' }}>
                            Iya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>E-Faktur</label>
                    <select name="efaktur" id="efaktur" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak"
                            {{ old('efaktur', $workflowdata['efaktur'] ?? '') == 'Tidak' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="Iya"
                            {{ old('efaktur', $workflowdata['efaktur'] ?? '') == 'Iya' ? 'selected' : '' }}>
                            Iya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>E-Nova</label>
                    <select name="enova" id="enova" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak"
                            {{ old('enova', $workflowdata['enova'] ?? '') == 'Tidak' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="Iya"
                            {{ old('enova', $workflowdata['enova'] ?? '') == 'Iya' ? 'selected' : '' }}>
                            Iya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Perfomance Bond</label>
                    <select name="performancebond" id="performancebond" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak"
                            {{ old('performancebond', $workflowdata['performancebond'] ?? '') == 'Tidak' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="Iya"
                            {{ old('performancebond', $workflowdata['performancebond'] ?? '') == 'Iya' ? 'selected' : '' }}>
                            Iya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lampiran HSE</label>
                    <select name="lampiranhse" id="lampiranhse" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak"
                            {{ old('lampiranhse', $workflowdata['lampiranhse'] ?? '') == 'Tidak' ? 'selected' : '' }}>
                            Tidak
                        </option>
                        <option value="Iya"
                            {{ old('lampiranhse', $workflowdata['lampiranhse'] ?? '') == 'Iya' ? 'selected' : '' }}>
                            Iya
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Document Contract (Tambah Baru)</label>
                    <input type="file" name="files[]" class="form-control" multiple>
                </div>
                @if (!empty($workflowdata['lampiran_kontrak']))
                    <div class="form-group">
                        <label>Dokumen Tersimpan</label>
                        <ul class="list-group">
                            @foreach ($workflowdata['lampiran_kontrak'] as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ asset('storage/kontrak/' . $file) }}" target="_blank">
                                        {{ $file }}
                                    </a>

                                    {{-- optional: checkbox hapus --}}
                                    <label class="mb-0">
                                        <input type="checkbox" name="delete_files[]" value="{{ $file }}">
                                        Hapus
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('work_assignment.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
@endsection
