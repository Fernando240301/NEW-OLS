<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use Illuminate\Support\Facades\Response;
use App\Models\RefJenisPeralatan;
use App\Models\RefTipePeralatan;
use App\Models\KategoriPeralatan;
use App\Models\ScopeofWork;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Tcpdf\Fpdi as TcpdfFpdi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\WorkAssignmentApprovalMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OperationController extends Controller
{
    public function index()
    {
        $data = Project::where('tipe', 'pr')
            ->orderBy('workflowid', 'desc')
            ->get();


        return view('project_list.index', compact('data'));
    }

    //Detail Project
    public function detail($id)
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

        $siteAddresses = DB::table('lov_jenis_peralatan')
            ->where('workflowid', $id)
            ->whereNotNull('lokasi')
            ->distinct()
            ->pluck('lokasi')
            ->toArray();

        if (count($siteAddresses) === 0) {
            $siteAddressText = '-';
        } elseif (count($siteAddresses) === 1) {
            $siteAddressText = $siteAddresses[0];
        } elseif (count($siteAddresses) === 2) {
            $siteAddressText = $siteAddresses[0] . ' dan ' . $siteAddresses[1];
        } else {
            $last = array_pop($siteAddresses);
            $siteAddressText = implode(', ', $siteAddresses) . ' dan ' . $last;
        }


        return view('project_list.detail', compact('app_workflow', 'workflowdata', 'namaclient', 'siteAddressText'));
    }

    public function getScope(Request $request)
    {
        $workflowid = $request->workflowid;

        $scopes = DB::table('lov_jenis_peralatan as s')
            ->leftJoin('ref_jenis_peralatan as j', 'j.id', '=', 's.jenis')
            ->leftJoin('ref_tipe_peralatan as t', 't.id', '=', 's.tipe')
            ->leftJoin('ref_kategori_peralatan as k', 'k.id', '=', 's.kategori')
            ->select(
                's.*',
                'j.nama as jenis_nama',
                't.nama as tipe_nama',
                'k.nama as kategori_nama'
            )
            ->where('s.workflowid', $workflowid)
            ->get();

        return view('project_list.scope_rows', compact('scopes'));
    }

    public function viewKontrak($filename)
    {
        $disk = Storage::disk('kontrak');

        // handle kasus .pdf.pdf
        if (!$disk->exists($filename)) {
            if ($disk->exists($filename . '.pdf')) {
                $filename = $filename . '.pdf';
            } else {
                abort(404, 'File kontrak tidak ditemukan');
            }
        }

        $path = $disk->path($filename);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function uploadMarketingFile(Request $request)
    {
        $request->validate([
            'workflowid' => 'required|integer',
            'type'       => 'required|string', // contoh: dokumen_hse
            'files'      => 'required',
            'files.*'    => 'file|mimes:pdf,doc,docx,xls,xlsx',
        ]);

        // ambil workflow
        $workflow = DB::table('app_workflow')
            ->where('workflowid', $request->workflowid)
            ->first();

        if (!$workflow) {
            return back()->withErrors('Workflow tidak ditemukan');
        }

        $workflowdata = json_decode($workflow->workflowdata, true);

        // pastikan key array
        if (!isset($workflowdata[$request->type]) || !is_array($workflowdata[$request->type])) {
            $workflowdata[$request->type] = [];
        }

        // loop file
        foreach ($request->file('files') as $file) {

            $filename = now()->format('Ymd_His') . '_' .
                Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) .
                '.' . $file->getClientOriginalExtension();

            // simpan ke disk kontrak
            $file->storeAs('', $filename, 'kontrak');

            // simpan ke workflowdata (seperti lampiran_kontrak)
            $workflowdata[$request->type][] = $filename;
        }

        // update DB
        DB::table('app_workflow')
            ->where('workflowid', $request->workflowid)
            ->update([
                'workflowdata' => json_encode($workflowdata),
            ]);

        return back()->with('success', 'File berhasil diupload');
    }

    //SIK
    public function sik($id)
    {
        $app_workflow = DB::table('app_workflow')
            ->where('workflowid', $id)
            ->first();

        if (!$app_workflow) {
            abort(404);
        }

        // Ambil semua SIK anak
        $data = DB::table('app_workflow as w')
            ->leftJoin(
                'sys_users as u',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(w.workflowdata, '$.user_inspector'))"),
                '=',
                'u.userid'
            )
            ->where('w.nworkflowid', $id)
            ->where('w.processname', 'surat_instruksi_kerja_01')
            ->orderBy('w.workflowid', 'asc')
            ->select('w.*', 'u.fullname as inspector_fullname')
            ->get();

        // ✅ DECODE DULU SEMUA
        $data->each(function ($item) {
            if (is_string($item->workflowdata)) {
                $item->workflowdata = json_decode($item->workflowdata, true);
            }
        });

        // ✅ PROSES LEADER REFERENCE + AMBIL NAMA LEADER
        $data->each(function ($item) use ($data) {

            $json = $item->workflowdata;

            if (in_array($json['pilihan_jabatan_project'] ?? '', ['Anggota', 'Teknisi'])) {

                $leaderId = $json['leadnya_pilihan_jabatan_project'] ?? null;

                if ($leaderId) {

                    $leader = $data->first(function ($row) use ($leaderId) {
                        return ($row->workflowdata['user_inspector'] ?? null) == $leaderId
                            && ($row->workflowdata['pilihan_jabatan_project'] ?? null) == 'Leader';
                    });

                    if ($leader) {
                        $item->leader_no_sik     = $leader->workflowdata['no_sik'] ?? null;
                        $item->leader_fullname  = $leader->inspector_fullname ?? null;
                    }
                }
            }
        });

        $workflowdata = json_decode($app_workflow->workflowdata, true);

        $namaclient = DB::table('pemohon')
            ->orderBy('pemohonid')
            ->get();

        return view('sik.sik', compact(
            'app_workflow',
            'workflowdata',
            'namaclient',
            'data'
        ));
    }

    public function createsik($id)
    {
        $workflow = DB::table('app_workflow')
            ->where('workflowid', $id)
            ->first();

        $workflowdata = json_decode($workflow->workflowdata, true);

        // ambil nomor SIK terakhir
        $lastSik = DB::table('app_workflow')
            ->where('nworkflowid', $id)
            ->where('processname', 'surat_instruksi_kerja_01')
            ->whereNotNull('workflowdata')
            ->orderBy('createtime', 'desc')
            ->value('workflowdata');

        $nextNo = 1;

        if ($lastSik) {
            $json = json_decode($lastSik, true);

            if (isset($json['no_sik'])) {
                // ambil angka urutan (006)
                preg_match('/\/(\d+)\/\d{4}$/', $json['no_sik'], $match);
                if (isset($match[1])) {
                    $nextNo = (int)$match[1] + 1;
                }
            }
        }

        $noUrut = str_pad($nextNo, 2, '0', STR_PAD_LEFT);
        $tahun  = date('Y');

        $noSik = "SIK/{$workflowdata['project_number']}/{$noUrut}/{$tahun}";

        $namaInspector = DB::table('sys_users')
            ->whereIn('rolesid', [20, 18])
            ->orderBy('userid')
            ->get();

        $leaders = DB::table('app_workflow')
            ->where('nworkflowid', $workflow->workflowid)
            ->where('processname', 'surat_instruksi_kerja_01')
            ->whereNotNull('workflowdata')
            ->get()
            ->map(function ($row) {
                $json = json_decode($row->workflowdata, true);

                if (($json['pilihan_jabatan_project'] ?? null) === 'Leader') {
                    return $json['user_inspector'] ?? null;
                }
                return null;
            })
            ->filter()
            ->unique()
            ->values();

        /*
        $leaders = collection of userid leader
        */

        $leaderUsers = DB::table('sys_users')
            ->whereIn('userid', $leaders)
            ->orderBy('fullname')
            ->get();

        $scopes = DB::table('lov_jenis_peralatan as s')
            ->leftJoin('ref_jenis_peralatan as j', 'j.id', '=', 's.jenis')
            ->leftJoin('ref_tipe_peralatan as t', 't.id', '=', 's.tipe')
            ->leftJoin('ref_kategori_peralatan as k', 'k.id', '=', 's.kategori')
            ->select(
                's.*',
                'j.nama as jenis_nama',
                't.nama as tipe_nama',
                'k.nama as kategori_nama'
            )
            ->where('s.workflowid', $id)
            ->get();


        return view('sik.create', compact('workflowdata', 'noSik', 'workflow', 'namaInspector', 'leaderUsers', 'scopes'));
    }

    public function storesik(Request $request)
    {
        $request->validate([
            'no_sik'           => 'required',
            'tanggal_sik'      => 'nullable|date',
            'user_inspector'   => 'required',
        ]);

        DB::beginTransaction();

        try {

            /* =========================
         * 1. UPLOAD MULTI FILE
         * ========================= */
            $uploadedFiles = [];

            if ($request->hasFile('surat_tugas')) {
                foreach ($request->file('surat_tugas') as $file) {

                    $original = str_replace(' ', '_', $file->getClientOriginalName());
                    $filename = now()->format('Ymd_His') . '_' . uniqid() . '_' . $original;

                    $path = $file->storeAs(
                        'surat_tugas',
                        $filename,
                        'public'
                    );

                    $uploadedFiles[] = $path;
                }
            }

            $parentWorkflow = DB::table('app_workflow')
                ->where('workflowid', $request->nworkflowid)
                ->first();

            if (!$parentWorkflow) {
                throw new \Exception('Workflow induk tidak ditemukan');
            }

            /* =========================
         * 2. SIAPKAN WORKFLOWDATA
         * ========================= */
            $workflowdata = [

                // DATA SIK
                'no_sik'          => $request->no_sik,
                'tanggal_sik'     => $request->tanggal_sik,
                'contact_person'  => $request->contact_person,

                // INSPECTOR
                'user_inspector'                    => $request->user_inspector,
                'pilihan_jabatan_project'           => $request->pilihan_jabatan_project,
                'leadnya_pilihan_jabatan_project'   => $request->leadnya_pilihan_jabatan_project,

                // PERALATAN (ARRAY)
                'peralatan'       => $request->peralatan,

                // LOKASI & TANGGAL
                'location_job'    => $request->location_job,
                'area_sik'        => $request->area_sik,
                'date_start'      => $request->date_start,
                'date_end'        => $request->date_end,
                'durasi'          => $request->durasi,

                // PERSIAPAN
                'persiapan' => [
                    'peri1' => $request->peri1,
                    'peri2' => $request->peri2,
                    'peri3' => $request->peri3,
                    'peri4' => $request->peri4,
                ],

                // PEMERIKSAAN LAPANGAN
                'lapangan' => [
                    'pl1' => $request->pl1,
                    'pl2' => $request->pl2,
                    'pl3' => $request->pl3,
                    'pl4' => $request->pl4,
                    'pl5' => $request->pl5,
                    'pl6' => $request->pl6,
                    'pl7' => $request->pl7,
                    'pl8' => $request->pl8,
                ],

                // PELAPORAN
                'pelaporan' => [
                    'si1' => $request->si1,
                    'si2' => $request->si2,
                    'si3' => $request->si3,
                    'si4' => $request->si4,
                    'si5' => $request->si5,
                ],

                // MIGAS
                'migas' => [
                    'pm1' => $request->pm1,
                    'pm2' => $request->pm2,
                    'pm3' => $request->pm3,
                ],

                'catatan_sik' => $request->catatan_sik,

                // FILE
                'surat_tugas' => $uploadedFiles,
            ];

            /* =========================
         * 3. INSERT KE app_workflow
         * ========================= */
            DB::table('app_workflow')->insert([

                'codeid'          => rand(1000, 9999),
                'projectname'     => $parentWorkflow->projectname,
                'tipe'            => 'doc',
                'resi'            => now()->format('YmdHis'),
                'client'          => $request->client,
                'processname'     => 'surat_instruksi_kerja_01',
                'processcategory' => 'New Certification',
                'createuser'      => Auth::user()->username ?? 'system',
                'createtime'      => now(),
                'workflowdata'    => json_encode($workflowdata),
                'next_status'     => 'proses',
                'nworkflowid'     => $request->nworkflowid,
                'last_update'     => now(),
                'last_status'     => 'proses',
                'next_taskname'   => 'permohonan',  // 🔥 TAMBAHKAN INI
                'next_stepname'   => 'step0',  // 🔥 TAMBAHKAN INI
                'next_rolename'   => 'pemohon',  // 🔥 TAMBAHKAN INI
                'next_status'     => 'proses',  // 🔥 TAMBAHKAN INI
                'jns_ijin'        => 006,  // 🔥 TAMBAHKAN INI
                'jns_layanan'     => 01,  // 🔥 biasanya juga wajib
                'noreg'           => '',  // 🔥 biasanya juga wajib
                'nib'             => '',  // 🔥 biasanya juga wajib

            ]);

            DB::commit();

            return redirect()
                ->route('project_list.sik', $request->nworkflowid)
                ->with('success', 'SIK berhasil disimpan');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    public function getLeaderData($workflowid, $userid)
    {
        $leaderWorkflow = DB::table('app_workflow')
            ->where('nworkflowid', $workflowid)
            ->where('processname', 'surat_instruksi_kerja_01')
            ->whereNotNull('workflowdata')
            ->get()
            ->first(function ($row) use ($userid) {
                $json = json_decode($row->workflowdata, true);
                return ($json['user_inspector'] ?? null) == $userid
                    && ($json['pilihan_jabatan_project'] ?? null) == 'Leader';
            });

        if (!$leaderWorkflow) {
            return response()->json([]);
        }

        return response()->json(json_decode($leaderWorkflow->workflowdata, true));
    }

    public function editsik($projectId, $id)
    {
        // 🔎 Ambil project induk
        $parentWorkflow = DB::table('app_workflow')
            ->where('workflowid', $projectId)
            ->first();

        if (!$parentWorkflow) {
            abort(404);
        }

        $workflowdata1 = json_decode($parentWorkflow->workflowdata, true);

        // 🔎 Ambil data SIK yang akan diedit
        $sik = DB::table('app_workflow')
            ->where('workflowid', $id)
            ->where('nworkflowid', $projectId) // 🔥 penting supaya tidak salah project
            ->where('processname', 'surat_instruksi_kerja_01')
            ->first();

        if (!$sik) {
            abort(404);
        }

        $workflowdata = json_decode($sik->workflowdata, true);

        $namaInspector = DB::table('sys_users')
            ->whereIn('rolesid', [20, 18])
            ->orderBy('userid')
            ->get();

        // Ambil leader dari project ini saja
        $leaderUsers = DB::table('app_workflow as w')
            ->leftJoin(
                'sys_users as u',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(w.workflowdata, '$.user_inspector'))"),
                '=',
                'u.userid'
            )
            ->where('w.nworkflowid', $projectId)
            ->where('w.processname', 'surat_instruksi_kerja_01')
            ->get()
            ->filter(function ($row) {
                $json = json_decode($row->workflowdata, true);
                return ($json['pilihan_jabatan_project'] ?? null) === 'Leader';
            })
            ->map(function ($row) {
                return (object)[
                    'userid' => json_decode($row->workflowdata, true)['user_inspector'],
                    'fullname' => $row->fullname
                ];
            });

        $scopes = DB::table('lov_jenis_peralatan as s')
            ->leftJoin('ref_jenis_peralatan as j', 'j.id', '=', 's.jenis')
            ->leftJoin('ref_tipe_peralatan as t', 't.id', '=', 's.tipe')
            ->leftJoin('ref_kategori_peralatan as k', 'k.id', '=', 's.kategori')
            ->select(
                's.*',
                'j.nama as jenis_nama',
                't.nama as tipe_nama',
                'k.nama as kategori_nama'
            )
            ->where('s.workflowid', $projectId)
            ->get();

        return view('sik.edit', compact(
            'sik',
            'workflowdata',
            'workflowdata1',
            'namaInspector',
            'leaderUsers',
            'scopes',
            'parentWorkflow'
        ));
    }

    public function updatesik(Request $request, $projectId, $id)
    {
        // 🔎 Pastikan SIK milik project tersebut
        $sik = DB::table('app_workflow')
            ->where('workflowid', $id)
            ->where('nworkflowid', $projectId)
            ->where('processname', 'surat_instruksi_kerja_01')
            ->first();

        if (!$sik) {
            abort(404);
        }

        $request->validate([
            'no_sik' => 'required',
            'user_inspector' => 'required',
        ]);

        $workflowdata = json_decode($sik->workflowdata, true);

        /* =========================
       UPDATE DATA
    ========================= */

        $workflowdata['tanggal_sik'] = $request->tanggal_sik;
        $workflowdata['contact_person'] = $request->contact_person;

        $workflowdata['user_inspector'] = $request->user_inspector;
        $workflowdata['pilihan_jabatan_project'] = $request->pilihan_jabatan_project;
        $workflowdata['leadnya_pilihan_jabatan_project'] = $request->leadnya_pilihan_jabatan_project;

        $workflowdata['peralatan'] = $request->peralatan;

        $workflowdata['location_job'] = $request->location_job;
        $workflowdata['area_sik'] = $request->area_sik;
        $workflowdata['date_start'] = $request->date_start;
        $workflowdata['date_end'] = $request->date_end;
        $workflowdata['durasi'] = $request->durasi;

        $workflowdata['persiapan'] = [
            'peri1' => $request->peri1,
            'peri2' => $request->peri2,
            'peri3' => $request->peri3,
            'peri4' => $request->peri4,
        ];

        $workflowdata['lapangan'] = [
            'pl1' => $request->pl1,
            'pl2' => $request->pl2,
            'pl3' => $request->pl3,
            'pl4' => $request->pl4,
            'pl5' => $request->pl5,
            'pl6' => $request->pl6,
            'pl7' => $request->pl7,
            'pl8' => $request->pl8,
        ];

        $workflowdata['pelaporan'] = [
            'si1' => $request->si1,
            'si2' => $request->si2,
            'si3' => $request->si3,
            'si4' => $request->si4,
            'si5' => $request->si5,
        ];

        $workflowdata['migas'] = [
            'pm1' => $request->pm1,
            'pm2' => $request->pm2,
            'pm3' => $request->pm3,
        ];

        $workflowdata['catatan_sik'] = $request->catatan_sik;

        DB::table('app_workflow')
            ->where('workflowid', $id)
            ->update([
                'workflowdata' => json_encode($workflowdata),
                'last_update'  => now(),
            ]);

        return redirect()
            ->route('project_list.sik', $projectId)
            ->with('success', 'SIK berhasil diperbarui');
    }

    public function deletesik($projectId, $id)
    {
        DB::beginTransaction();

        try {

            // 🔎 Ambil SIK berdasarkan project
            $workflow = DB::table('app_workflow')
                ->where('workflowid', $id)
                ->where('nworkflowid', $projectId)
                ->where('processname', 'surat_instruksi_kerja_01')
                ->first();

            if (!$workflow) {
                return redirect()
                    ->route('project_list.sik', $projectId)
                    ->with('error', 'Data SIK tidak ditemukan');
            }

            // =========================
            // PINDAHKAN KE TABLE DELETED
            // =========================
            DB::table('app_workflow_deleted')->insert(
                (array) $workflow + [
                    'deleted_at' => now(),
                    'deleted_by' => Auth::user()->username ?? 'system',
                ]
            );

            // =========================
            // HAPUS DARI TABLE UTAMA
            // =========================
            DB::table('app_workflow')
                ->where('workflowid', $id)
                ->delete();

            DB::commit();

            return redirect()
                ->route('project_list.sik', $projectId)
                ->with('success', 'SIK berhasil dihapus');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
