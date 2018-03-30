<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use App;
use DB;

class WorkAreaType extends Model
{
    protected $table = 'master_work_area_type';
    public $timestamps = false;
    protected $fillable = ['name_en','status','name_th'];
    
    public static function getData()
    {
        $lang = getLang();
        return  self::where('status', 1)
        ->select(DB::raw("id,name_$lang as name"))->get();
    }
}
