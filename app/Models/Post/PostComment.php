<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PostComment extends Model
{
    protected $table = 'post_comments';
    public $timestamps = false;

    protected $fillable = ['post_id','domain_id', 'description','created_at','created_by'];
}
