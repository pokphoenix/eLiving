<?php

namespace App\Models\Suggest;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuggestAttach extends Model
{
    protected $table = 'suggest_attachments';
    public $timestamps = false;
    protected $fillable = ['suggest_id','domain_id','path','filename','created_at','created_by'];
}
