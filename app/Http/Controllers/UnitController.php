<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnitController extends Controller
{

    public function index($workflowid)
    {

        $app_workflow = DB::table('app_workflow')
            ->where('workflowid', $workflowid)
            ->first();

        if (!$app_workflow) {
            abort(404);
        }

        $workflowdata = json_decode($app_workflow->workflowdata, true);


        $units = DB::table('app_workflow')
            ->where('nworkflowid', $workflowid)
            ->where('processname', 'surat_instruksi_kerja_01')
            ->where('processcategory', 'New Certification')
            ->get();


        $leaders = [];
        $members = [];

        foreach ($units as $u) {

            $json = json_decode($u->workflowdata, true);

            if (!$json) continue;

            if (($json['pilihan_jabatan_project'] ?? '') === 'Leader') {

                $leaders[$u->workflowid] = [
                    'workflowid' => $u->workflowid,
                    'userid' => $json['user_inspector'],
                    'no_sik' => $json['no_sik'],
                    'location_job' => $json['location_job'] ?? '-'
                ];
            }

            if (($json['pilihan_jabatan_project'] ?? '') === 'Anggota') {

                $members[] = [
                    'leader_workflowid' => $json['leadnya_pilihan_jabatan_project'],
                    'userid' => $json['user_inspector'],
                    'no_sik' => $json['no_sik'] ?? '-'
                ];
            }
        }


        $data = [];

        foreach ($leaders as $leaderWorkflowId => $leader) {

            $leaderName = DB::table('sys_users')
                ->where('userid', $leader['userid'])
                ->value('fullname');

            $anggotaNames = [];

            foreach ($members as $m) {

                if ($m['leader_workflowid'] == $leaderWorkflowId) {

                    $nama = DB::table('sys_users')
                        ->where('userid', $m['userid'])
                        ->value('fullname');

                    if ($nama) {
                        $anggotaNames[] = $nama . ' (' . $m['no_sik'] . ')';
                    }
                }
            }

            $data[] = [
                'id' => $leader['workflowid'],
                'leader' => $leaderName . ' (' . $leader['no_sik'] . ')',
                'anggota' => $anggotaNames,
                'lokasi' => $leader['location_job']
            ];
        }


        return view('units.index', compact(
            'data',
            'workflowid',
            'workflowdata'
        ));
    }



    public function detail($projectId, $unitId)
    {

        $project = DB::table('app_workflow')
            ->where('workflowid', $projectId)
            ->first();

        if (!$project) abort(404);

        $workflowdata = json_decode($project->workflowdata, true);


        $unit = DB::table('app_workflow')
            ->where('workflowid', $unitId)
            ->first();

        if (!$unit) abort(404);

        $unitdata = json_decode($unit->workflowdata, true);


        /*
        =========================
        TOTAL UNIT
        =========================
        */

        $totalUnits = 0;

        if (!empty($unitdata['peralatan'])) {

            foreach ($unitdata['peralatan'] as $alat) {

                $totalUnits += (int)($alat['jumlah'] ?? 0);
            }
        }


        $done = 0;
        $remaining = $totalUnits - $done;


        /*
        =========================
        EQUIPMENT SCOPE
        =========================
        */

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


        /*
        =========================
        UNIT PER TYPE
        =========================
        */

        $unitsByType = [];

        if (!empty($unitdata['peralatan'])) {

            foreach ($unitdata['peralatan'] as $alat) {

                $type = $alat['type_peralatan'];
                $jumlah = (int)($alat['jumlah'] ?? 0);

                if (!isset($unitsByType[$type])) {

                    $unitsByType[$type] = 0;
                }

                $unitsByType[$type] += $jumlah;
            }
        }


        $workflowid = $projectId;


        return view('units.detail', compact(
            'workflowid',
            'workflowdata',
            'unit',
            'unitdata',
            'scopes',
            'totalUnits',
            'done',
            'remaining',
            'unitsByType'
        ));
    }



    /*
    =========================
    STEP 2
    =========================
    */

    public function getTypes($jenisId)
    {

        $types = DB::table('ref_tipe_peralatan')
            ->where('jenis', $jenisId)
            ->get();

        return view('units.partials.types', compact('types'));
    }



    /*
    =========================
    STEP 3
    =========================
    */

    public function getCategories($typeId)
    {

        $categories = DB::table('ref_kategori_peralatan')->get();

        return view('units.partials.categories', compact('categories'));
    }



    /*
    =========================
    STEP 4
    =========================
    */

    public function getForm($categoryId, $typeId)
    {

        $kategori = DB::table('ref_kategori_peralatan')
            ->where('id', $categoryId)
            ->first();

        $tipe = DB::table('ref_tipe_peralatan')
            ->where('id', $typeId)
            ->first();

        if (!$kategori || !$tipe) abort(404);


        $typeFolder = Str::slug($tipe->nama, '_');
        $categoryAlias = Str::slug($kategori->alias, '_');


        return view("units.forms.$typeFolder.$categoryAlias");
    }

    public function form($projectId, $unitId, $typeId, $categoryId)
    {

        $project = DB::table('app_workflow')
            ->where('workflowid', $projectId)
            ->first();

        $unit = DB::table('app_workflow')
            ->where('workflowid', $unitId)
            ->first();

        $type = DB::table('ref_tipe_peralatan')
            ->where('id', $typeId)
            ->first();

        $category = DB::table('ref_kategori_peralatan')
            ->where('id', $categoryId)
            ->first();

        return view('units.form', [
            'project' => $project,
            'unit' => $unit,
            'type' => $type,
            'category' => $category
        ]);
    }
}
