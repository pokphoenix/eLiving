<?php

namespace App\Models\Log;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;

class LogError extends Model
{
    protected $table = 'log_error';

    
    protected $fillable = ['id','url', 'json_data','created_at','status'];
    public $timestamps = false;
    protected $dates = ['created_at'];

    public static function strMax($string){
    
        $char = strlen($string) ;
        if ($char > 1000){
            $string = substr($string, 0,1000) ;
        }
        return $string ;
    }

    public static function SetLogError($activity,$status){
        $log = new LogError;
        $log->url = \Request::fullUrl();
        $log->json_data_out = self::strMax($activity);
        $log->json_data_in = self::strMax(json_encode(\Request::all())) ;
        $log->created_at = Carbon::now();
        $log->status = $status;
        $log->save();
    }

}
