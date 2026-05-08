<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppWorkflow extends Model
{
    protected $table = 'app_workflow';
    protected $primaryKey = 'workflowid';
    public $incrementing = false;
    public $timestamps = false;
}
