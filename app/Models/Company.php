<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = ['domain_id','name', 'address','contact_name','contact_tel','contact_email','type','tin','credit','note','is_branch','branch_id','branch_no','status','created_by'];
    protected $dates = ['created_at', 'updated_at'];
}
