<?php

namespace App\Models\Task;


use Illuminate\Database\Eloquent\Model;

class TaskChecklistItem extends Model
{
    protected $table = 'task_checklist_items';
    public $timestamps = false;
    protected $fillable = ['checklist_id','task_id','domain_id','title','created_at','created_by','status'];
}
