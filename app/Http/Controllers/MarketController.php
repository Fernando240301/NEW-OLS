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

    //Add Client
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
                'no_hp'              => $request->no_hp,
                'contact_person1'    => $request->contact_person1,
                'no_hp1'             => $request->no_hp1,
                'email'              => $request->email,
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

    //Edit Client
    public function edit($id)
    {
        $client = DB::table('pemohon')
            ->where('pemohonid', $id)
            ->first();

        if (!$client) {
            abort(404);
        }

        $klasifikasi = DB::table('ref_klasifikasi_client')
            ->orderBy('nama')
            ->get();

        return view('client.edit', compact('client', 'klasifikasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'klasifikasi'     => 'required',
            'email_pemohon'   => 'required|email',
        ]);

        DB::table('pemohon')
            ->where('pemohonid', $id)
            ->update([
                'nama_perusahaan'     => $request->nama_perusahaan,
                'klasifikasi'         => $request->klasifikasi,
                'email_pemohon'       => $request->email_pemohon,
                'alamat_perusahaan'   => $request->alamat_perusahaan,
                'kota_perusahaan'     => $request->kota_perusahaan,
                'provinsi_perusahaan' => $request->provinsi_perusahaan,
                'negara'              => $request->negara,
                'kode_pos'            => $request->kode_pos,
                'telp_perusahaan'     => $request->telp_perusahaan,
                'contact1'            => $request->contact1,
                'contact_celluler1'   => $request->contact_celluler1,
                'contact2'            => $request->contact2,
                'contact_celluler2'   => $request->contact_celluler2,
                'contact3'            => $request->contact3,
                'contact_celluler3'   => $request->contact_celluler3,
            ]);

        return redirect()
            ->route('client.index')
            ->with('success', 'Data client berhasil diperbarui');
    }

    // Delete Client
    public function delete($id)
    {
        $deleted = DB::table('pemohon')
            ->where('pemohonid', $id)
            ->delete();

        if (!$deleted) {
            return redirect()
                ->route('client.index')
                ->with('error', 'Data client tidak ditemukan');
        }

        return redirect()
            ->route('client.index')
            ->with('success', 'Data client berhasil dihapus');
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
}
