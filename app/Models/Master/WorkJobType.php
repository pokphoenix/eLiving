<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use App;
use DB;

class WorkJobType extends Model
{
    protected $table = 'master_work_job_type';
    public $timestamps = false;
    protected $fillable = ['name_en','status','name_th'];
    
    public static function getData()
    {
        $lang = getLang();
        return  WorkJobType::where('status', 1)
        ->select(DB::raw("id,name_$lang as name"))->get();
    }
}
