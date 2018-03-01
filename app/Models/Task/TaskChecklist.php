<?php

namespace App\Models\Task;


use Illuminate\Database\Eloquent\Model;

class TaskChecklist extends Model
{
    protected $table = 'task_checklists';
    public $timestamps = false;
    protected $fillable = ['task_id','domain_id','title','created_at','created_by'];
}
