<?php

namespace App\Models\Log;

use App\Tools\Utility;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class LogExceptionError extends Model
{
    protected $table = 'log_exception_error';

    
    protected $fillable = ['id','url', 'body','created_at','message','file','line','code','method','platform'];
    public $timestamps = false;
    protected $dates = ['created_at'];

   

    public static function SetData($message, $file, $line, $code, $method, $platform)
    {
        $log = new LogExceptionError;
        $log->url = \Request::fullUrl();
        $log->body = Utility::strMax(json_encode(\Request::all())) ;
        $log->created_at = Carbon::now();
        $log->message = $message;
        $log->file = $file;
        $log->line = $line;
        $log->code = $code;
        $log->method = $method;
        $log->platform = $platform;
        $log->save();
        return $log->id ;
    }
}
