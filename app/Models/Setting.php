<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use DB;

class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = false;
    
    public static function getVal($domainId, $key)
    {
        $query = Setting::where('domain_id', $domainId)->where('keys', $key)->where('status', 1)->first();
        $return = null ;
        if (!empty($query)) {
            $return = $query->values;
        }
        return $return ;
    }


    public static function getServerTime()
    {
        $sql = "SELECT  UNIX_TIMESTAMP(now()) as ts";
        $query = collect(DB::select(DB::raw($sql)))->first();
        return $query->ts ;
    }
}
