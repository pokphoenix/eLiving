<?php

namespace App\Models\Task;


use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    protected $table = 'task_historys';
    public $timestamps = false;
    protected $fillable = ['task_id', 'domain_id','status','created_at','created_by','pin','assign_to_user_id','move_to_pioritized','task_comment_id','task_attach_id','duedate_to','task_category_id','checklist_id'];
}
