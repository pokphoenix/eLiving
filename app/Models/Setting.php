<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = false;
    
    public static function getVal($domainId,$key){
    	$query = Setting::where('domain_id',$domainId)->where('keys',$key)->where('status',1)->first();
    	$return = null ;
      	if(!empty($query)){
      		$return = $query->values;
      	}
        return $return ;
    }


}
