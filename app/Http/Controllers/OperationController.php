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
        $data = Project::orderBy('workflowid', 'desc')->get();

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

        // 🔑 decode workflowdata
        $workflowdata = json_decode($app_workflow->workflowdata, true);

        //Nama Client
        $namaclient = DB::table('pemohon')
            ->orderBy('pemohonid')
            ->get();

        return view('sik.sik', compact('app_workflow', 'workflowdata', 'namaclient'));
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
                preg_match('/\/No\/(\d+)\//', $json['no_sik'], $match);
                if (isset($match[1])) {
                    $nextNo = (int)$match[1] + 1;
                }
            }
        }

        $noUrut = str_pad($nextNo, 2, '0', STR_PAD_LEFT);
        $tahun  = date('Y');

        $noSik = "SIK/{$workflowdata['project_number']}/No/{$noUrut}/{$tahun}";

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
}
