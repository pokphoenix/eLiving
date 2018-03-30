<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TaskComment extends Model
{
    protected $table = 'task_comments';
    public $timestamps = false;

    protected $fillable = ['task_id','domain_id', 'description','created_at','created_by'];
}
