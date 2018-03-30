<?php

namespace App\Models\Work;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class WorkAttach extends Model
{
    protected $table = 'work_attachments';
    public $timestamps = false;
    protected $fillable = ['work_id','domain_id','path','filename','created_at','created_by'];
}
