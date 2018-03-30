<?php

namespace App\Models\Work;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class WorkComment extends Model
{
    protected $table = 'work_comments';
    public $timestamps = false;

    protected $fillable = ['work_id','domain_id', 'description','created_at','created_by'];
}
