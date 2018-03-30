<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Model;

class PostHistory extends Model
{
    protected $table = 'post_historys';
    public $timestamps = false;
    protected $fillable = ['post_id', 'domain_id','status','created_by','pin','assign_to_user_id','move_to_pioritized','post_comment_id','post_attach_id'];
}
