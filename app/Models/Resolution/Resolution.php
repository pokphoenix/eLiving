<?php

namespace App\Models\Resolution;

use Illuminate\Database\Eloquent\Model;

class Resolution extends Model
{
    protected $table = 'resolutions';

    protected $fillable = ['title','description', 'status','domain_id','vote_winner','doned_at','voting_at'];
    protected $dates = ['created_at', 'updated_at'];
}
