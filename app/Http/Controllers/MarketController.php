<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\RefJenisPeralatan;
use App\Models\RefTipePeralatan;
use App\Models\KategoriPeralatan;
use App\Models\ScopeofWork;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class MarketController extends Controller
{
    public function index()
    {
        $data = Project::all();

        $jenisPeralatan = RefJenisPeralatan::orderBy('id')->get();
        $tipePeralatan  = RefTipePeralatan::orderBy('id')->get();
        $kategoriPeralatan = KategoriPeralatan::orderBy('id')->get();

        return view('work_assignment.index', compact(
            'data',
            'jenisPeralatan',
            'tipePeralatan',
            'kategoriPeralatan'
        ));
    }

    //Add Project
    public function create()
    {
        $namaclient = DB::table('pemohon')
            ->orderBy('pemohonid')
            ->get();

        return view('work_assignment.create', compact('namaclient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_type' => 'required',
            'projectname'  => 'required|string',
            'client'       => 'required',
            'files'        => 'nullable|array',
            'files.*'      => 'file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        DB::beginTransaction();

        try {
            /* =========================
         * 1. UPLOAD MULTI FILE
         * ========================= */
            $uploadedFiles = [];

            $files = $request->file('files', []);

            if (!empty($files)) {
                foreach ($files as $file) {
                    $original = str_replace(' ', '_', $file->getClientOriginalName());
                    $filename = now()->format('Ymd_His') . '_' . $original;

                    $file->storeAs('public/kontrak', $filename);

                    $uploadedFiles[] = $filename;
                }
            }

            /* =========================
         * 2. SIAPKAN WORKFLOW DATA (MIRIP CI3)
         * ========================= */
            $prefix = $request->project_type; // PR / FR / NP

            $lastProject = DB::table('app_workflow')
                ->where('workflowdata', 'like', '%"project_number":"' . $prefix . '-%')
                ->orderBy('workflowid', 'desc')
                ->lockForUpdate()
                ->first();

            $nextNumber = 1;

            if ($lastProject) {
                $data = json_decode($lastProject->workflowdata, true);

                if (!empty($data['project_number'])) {
                    $lastNumber = intval(substr($data['project_number'], -4));
                    $nextNumber = $lastNumber + 1;
                }
            }


            // Format 4 digit
            $projectNumber = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);


            $workflow = [
                'project_number'     => $projectNumber,
                'project_type'       => $request->project_type,
                'projectname'        => $request->projectname,
                'client'             => $request->client,
                'no_kontrak'         => $request->no_kontrak,
                'tanggal_kontrak'    => $request->tanggal_kontrak,
                'tanggal_akhir'      => $request->tanggal_akhir_kerja,
                'lokasi_kantor'      => $request->lokasi_kantor,
                'lokasi_lapangan'    => $request->lokasi_lapangan,
                'harga_kontrak'      => $request->harga_kontrak,
                'contact_person'     => $request->contact_person,
                'bastp'              => $request->bastp,
                'paymentapproval'    => $request->paymentapproval,
                'copylampiranc'      => $request->copylampiranc,
                'copylampirand'      => $request->copylampirand,
                'no_hp'              => $request->no_hp,
                'contact_person1'    => $request->contact_person1,
                'no_hp1'             => $request->no_hp1,
                'email'              => $request->email,
                'lokasiujipsv'       => $request->lokasiujipsv,
                'pidp'               => $request->pidp,
                'mobdemob'           => $request->mobdemob,
                'akomodasi'          => $request->akomodasi,
                'lokaltransport'     => $request->lokaltransport,
                'meals'              => $request->meals,
                'invoiceasli'        => $request->invoiceasli,
                'efaktur'            => $request->efaktur,
                'enova'              => $request->enova,
                'performancebond'    => $request->performancebond,
                'lampiranhse'        => $request->lampiranhse,

                // ⬇️ INI PENGGANTI file & file1 CI3
                'lampiran_kontrak'   => $uploadedFiles,
            ];

            /* =========================
         * 3. INSERT KE TABLE (app_workflow / app_workflow)
         * ========================= */
            DB::table('app_workflow')->insert([
                'resi'            => now()->format('YmdHis'),
                'codeid'          => rand(1000, 9999),
                'client'          => $request->client,
                'noreg'           => $projectNumber,
                'projectname'     => $request->projectname,
                'tipe'            => 'pr',
                'jns_ijin'        => '0',
                'jns_layanan'     => '0',
                'nib'             => '',
                'processname'     => 'verifikasi_permohonan',
                'processcategory' => 'marketing',
                'createuser'      => Auth::user()->username ?? 'system',
                'createtime'      => now(),
                'next_taskname'   => 'verifikasi_permohonan',
                'next_stepname'   => 'step0',
                'next_rolename'   => 'manager_marketing',
                'next_status'     => 'proses',
                'last_update'     => now(),
                'last_status'     => 'proses',
                'workflowdata'    => json_encode($workflow),
            ]);

            DB::commit();

            return redirect()
                ->route('work_assignment.index')
                ->with('success', 'Project berhasil disimpan');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    //Edit Project
    public function edit($id)
    {
        $app_workflow = DB::table('app_workflow')
            ->where('workflowid', $id)
            ->first();

        if (!$app_workflow) {
            abort(404);
        }

        // 🔑 decode workflowdata
        $workflowdata = json_decode($app_workflow->workflowdata, true);

        //Nama Client
        $namaclient = DB::table('pemohon')
            ->orderBy('pemohonid')
            ->get();

        return view('work_assignment.edit', compact('app_workflow', 'workflowdata', 'namaclient'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'project_type' => 'required',
            'projectname'  => 'required|string',
            'client'       => 'required',
            'files'        => 'nullable|array',
            'files.*'      => 'file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        DB::beginTransaction();

        try {
            // ambil data lama
            $app = DB::table('app_workflow')
                ->where('workflowid', $id)
                ->lockForUpdate()
                ->first();

            if (!$app) {
                abort(404);
            }

            $workflowdata = json_decode($app->workflowdata, true) ?? [];

            /* =========================
         * 1. HANDLE FILE LAMA
         * ========================= */
            $existingFiles = $workflowdata['lampiran_kontrak'] ?? [];

            // hapus file yang dicentang
            if ($request->filled('delete_files')) {
                foreach ($request->delete_files as $file) {
                    Storage::delete('public/kontrak/' . $file);
                    $existingFiles = array_values(
                        array_diff($existingFiles, [$file])
                    );
                }
            }

            /* =========================
         * 2. UPLOAD FILE BARU
         * ========================= */
            $newFiles = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $original = str_replace(' ', '_', $file->getClientOriginalName());
                    $filename = now()->format('Ymd_His') . '_' . $original;

                    $file->storeAs('public/kontrak', $filename);
                    $newFiles[] = $filename;
                }
            }

            /* =========================
         * 3. UPDATE WORKFLOWDATA
         * ========================= */
            $workflowdata = array_merge($workflowdata, [
                'project_type'     => $request->project_type,
                'projectname'      => $request->projectname,
                'client'           => $request->client,
                'no_kontrak'       => $request->no_kontrak,
                'tanggal_kontrak'  => $request->tanggal_kontrak,
                'tanggal_akhir'    => $request->tanggal_akhir,
                'lokasi_kantor'    => $request->lokasi_kantor,
                'lokasi_lapangan'  => $request->lokasi_lapangan,
                'harga_kontrak'    => $request->harga_kontrak,
                'contact_person'   => $request->contact_person,
                'no_hp'            => $request->no_hp,
                'contact_person1'  => $request->contact_person1,
                'no_hp1'           => $request->no_hp1,
                'email'            => $request->email,
                'lokasiujipsv'     => $request->lokasiujipsv,
                'pidp'             => $request->pidp,
                'mobdemob'         => $request->mobdemob,
                'akomodasi'        => $request->akomodasi,
                'lokaltransport'   => $request->lokaltransport,
                'meals'            => $request->meals,
                'invoiceasli'      => $request->invoiceasli,
                'bastp'              => $request->bastp,
                'paymentapproval'    => $request->paymentapproval,
                'copylampiranc'      => $request->copylampiranc,
                'copylampirand'      => $request->copylampirand,
                'efaktur'          => $request->efaktur,
                'enova'            => $request->enova,
                'performancebond'  => $request->performancebond,
                'lampiranhse'      => $request->lampiranhse,

                // gabungkan file lama + baru
                'lampiran_kontrak' => array_merge($existingFiles, $newFiles),
            ]);

            /* =========================
         * 4. UPDATE TABLE app_workflow
         * ========================= */
            DB::table('app_workflow')
                ->where('workflowid', $id)
                ->update([
                    'projectname'  => $request->projectname,
                    'client'       => $request->client,
                    'last_update'  => now(),
                    'workflowdata' => json_encode($workflowdata),
                ]);

            DB::commit();

            return redirect()
                ->route('work_assignment.index')
                ->with('success', 'Project berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }


    // Delete Work Assignment
    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $workflow = DB::table('app_workflow')
                ->where('workflowid', $id)
                ->first();

            if (!$workflow) {
                return redirect()
                    ->route('work_assignment.index')
                    ->with('error', 'Data Project tidak ditemukan');
            }

            DB::table('app_workflow_deleted')->insert([
                (array) $workflow + [
                    'deleted_at' => now(),
                    'deleted_by' => Auth::user()->username ?? 'system',
                ]
            ]);

            DB::table('app_workflow')
                ->where('workflowid', $id)
                ->delete();

            DB::commit();

            return redirect()
                ->route('work_assignment.index')
                ->with('success', 'Data Project berhasil dihapus');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeScope(Request $request)
    {
        $request->validate([
            'workflowid' => 'required|exists:app_workflow,workflowid',
            'scope'      => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $keepIds = [];

            foreach ($request->scope as $row) {

                if (empty($row['jenis']) || empty($row['tipe'])) {
                    continue;
                }

                $data = [
                    'workflowid' => $request->workflowid,
                    'lokasi'     => $row['lokasi'] ?? null,
                    'item'       => null,
                    'jenis'      => $row['jenis'],
                    'tipe'       => $row['tipe'],
                    'kategori'   => $row['kategori'] ?? null,
                    'jumlah'     => (int) ($row['jumlah'] ?? 1),
                    'harga'      => str_replace('.', '', $row['harga'] ?? 0),
                ];

                // 🔑 UPDATE jika ada ID
                if (!empty($row['id'])) {
                    ScopeofWork::where('id', $row['id'])->update($data);
                    $keepIds[] = $row['id'];
                }
                // 🆕 INSERT jika tidak ada ID
                else {
                    $new = ScopeofWork::create($data);
                    $keepIds[] = $new->id;
                }
            }

            // 🗑️ DELETE row lama yang tidak dikirim
            ScopeofWork::where('workflowid', $request->workflowid)
                ->whereNotIn('id', $keepIds)
                ->delete();

            DB::commit();
            return back()->with('success', 'Scope of Work berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    public function getScope($workflowid)
    {
        return ScopeofWork::where('workflowid', $workflowid)->get();
    }

    public function pdf($id)
    {
        $project = Project::with('clientRel')->findOrFail($id);

        $workflow = json_decode($project->workflowdata, true) ?? [];

        $scope = ScopeofWork::with([
            'jenisRel',
            'kategoriRel',
            'tipeRel' // optional
        ])
            ->where('workflowid', $id)
            ->get();


        $issuedDate = !empty($workflow['tanggal_kontrak'])
            ? Carbon::parse($workflow['tanggal_kontrak'])
            ->locale('en')
            ->translatedFormat('d F Y')
            : '-';

        $expiredDate = !empty($workflow['tanggal_akhir'])
            ? Carbon::parse($workflow['tanggal_akhir'])
            ->locale('en')
            ->translatedFormat('d F Y')
            : '-';

        $pdf = Pdf::loadView(
            'work_assignment.pdf',
            compact('project', 'workflow', 'scope', 'issuedDate', 'expiredDate')
        )->setPaper('A4', 'portrait');

        return $pdf->stream(
            'Work_Assignment_' . ($workflow['project_number'] ?? $id) . '.pdf'
        );
    }
}
