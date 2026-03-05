<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'workflowid',
        'parent_id',
        'name',
        'type',
        'file_path',
        'mime_type',
        'uploaded_by'
    ];
}
