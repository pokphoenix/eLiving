<?php

namespace App\Models\Work;

use Illuminate\Database\Eloquent\Model;

class WorkHistory extends Model
{
    protected $table = 'work_historys';
    public $timestamps = false;
    protected $fillable = ['work_id', 'domain_id','status','created_at','created_by','pin','assign_to_user_id','move_to_pioritized','task_comment_id','task_attach_id','duedate_to','task_category_id','checklist_id'];
}
