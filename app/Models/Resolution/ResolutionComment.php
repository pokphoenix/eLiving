<?php

namespace App\Models\Resolution;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ResolutionComment extends Model
{
    protected $table = 'resolution_comments';
    public $timestamps = false;

    protected $fillable = ['resolution_id','domain_id', 'description','created_at','created_by'];
}
