<?php

namespace App\Models\Post;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PostAttach extends Model
{
    protected $table = 'post_attachments';
    public $timestamps = false;
    protected $fillable = ['post_id','domain_id','path','filename','created_at','created_by'];	
}
