<?php

namespace App\Models\Task;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TaskViewer extends Model
{
    protected $table = 'task_viewers';
    public $timestamps = false;
    protected $fillable = ['task_id','domain_id', 'user_id','created_at'];
    protected $dates = ['created_at'];
}
