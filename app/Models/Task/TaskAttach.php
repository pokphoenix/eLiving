<?php

namespace App\Models\Task;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TaskAttach extends Model
{
    protected $table = 'task_attachments';
    public $timestamps = false;
    protected $fillable = ['task_id','domain_id','path','filename','created_at','created_by'];	
}
