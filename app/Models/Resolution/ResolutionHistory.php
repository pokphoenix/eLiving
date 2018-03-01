<?php

namespace App\Models\Resolution;


use Illuminate\Database\Eloquent\Model;

class ResolutionHistory extends Model
{
    protected $table = 'resolution_historys';
    public $timestamps = false;
    protected $fillable = ['resolution_id', 'domain_id','status','created_by','pin','assign_to_user_id','move_to_pioritized','resolution_comment_id'];
}
