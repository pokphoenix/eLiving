<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class StatusHistory extends Model
{
    protected $table = 'master_status_history';
    public $timestamps = false;
    
    protected $fillable = ['name','status'];
    // protected $dates = ['created_at', 'updated_at'];

    public static function getStatus($name){
    	return StatusHistory::where('name',$name)->first()->id ;
    } 
  
}
