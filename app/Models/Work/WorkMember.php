<?php

namespace App\Models\Work;

use App\Facades\Permission;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class WorkMember extends Model
{
    protected $table = 'work_members';
    public $timestamps = false;

    protected $fillable = ['work_id','domain_id', 'user_id','created_at'];
    protected $dates = ['created_at'];
}
