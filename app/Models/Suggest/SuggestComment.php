<?php

namespace App\Models\Suggest;




use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuggestComment extends Model
{
    protected $table = 'suggest_comments';
    public $timestamps = false;

    protected $fillable = ['suggest_id','domain_id', 'description','created_at','created_by'];
   	
    
}	
