<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class Project extends Model
{
    protected $table = 'app_workflow';
    protected $primaryKey = 'workflowid';
    public $timestamps = false;

    public function getProjectNumberAttribute()
    {
        $data = json_decode($this->workflowdata, true);
        return $data['project_number'] ?? null;
    }

    public function getContractNumberAttribute()
    {
        $data = json_decode($this->workflowdata, true);
        return $data['no_kontrak'] ?? null;
    }

    public function clientRel()
    {
        return $this->belongsTo(
            Client::class,
            'client',      // FK di app_workflow
            'pemohonid'    // PK di pemohon
        );
    }

    public function getClientNameAttribute()
    {
        return $this->clientRel->nama_perusahaan ?? '-';
    }
}
