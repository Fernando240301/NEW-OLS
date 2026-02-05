<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function approveMO(string $token)
    {
        $wf = DB::table('app_workflow')
            ->where('apv_token', $token)
            ->first();

        if (!$wf) {
            return response('Token tidak valid', 404);
        }

        if ($wf->apv_mo == 1) {
            return view('approval.already', [
                'role' => 'Manager Operasi'
            ]);
        }

        DB::table('app_workflow')
            ->where('workflowid', $wf->workflowid)
            ->update([
                'apv_mo'      => 1,
                'date_mo'     => now(),
                'last_status' => 'approved_mo',
                'last_update' => now(),
            ]);

        return view('approval.success', [
            'role'  => 'Manager Operasi',
            'noreg' => $wf->noreg,
        ]);
    }


    public function approveMF(string $token)
    {
        $wf = DB::table('app_workflow')
            ->where('apv_token', $token)
            ->first();

        if (!$wf) {
            return response('Token tidak valid', 404);
        }

        if ($wf->apv_mf == 1) {
            return view('approval.already', [
                'role' => 'Manager Finance'
            ]);
        }

        DB::table('app_workflow')
            ->where('workflowid', $wf->workflowid)
            ->update([
                'apv_mf'      => 1,
                'date_mf'     => now(),
                'last_status' => 'approved_mf',
                'last_update' => now(),

                // 🔐 matikan token setelah final approval
                'apv_token'   => null,
            ]);

        return view('approval.success', [
            'role'  => 'Manager Finance',
            'noreg' => $wf->noreg,
        ]);
    }
}
