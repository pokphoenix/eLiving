<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LogActivity extends Model
{
    protected $table = 'log_activity';

    
    protected $fillable = ['id','user_id', 'activity','domain_id'];
    public $timestamps = false;
    protected $dates = ['created_at'];


    public static function SetLogActivity($activity){
        $log = new LogActivity;
        $log->user_id = Auth()->user()->id;
        $log->activity = $activity;
        $log->created_at = Carbon::now();
        $log->domain_id = Auth()->user()->recent_domain;
        $log->save();
    }

}
