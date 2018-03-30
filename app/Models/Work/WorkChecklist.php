<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Model;

class WorkChecklist extends Model
{
    protected $table = 'work_checklists';
    public $timestamps = false;
    protected $fillable = ['work_id','domain_id','title','created_at','created_by'];
}
