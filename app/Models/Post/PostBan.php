<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Model;

class PostBan extends Model
{
    protected $table = 'post_ban';
    public $timestamps = false;

    protected $fillable = ['user_id','created_at', 'created_by','domain_id'];
   	protected $dates = ['created_at'];

   	
}	
