<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use App;
use DB;

class WorkSystemType extends Model
{
    protected $table = 'master_work_system_type';
    public $timestamps = false;
    protected $fillable = ['name_en','status','name_th','color'];
    
    public static function getData()
    {
        $lang = getLang();
        return  WorkSystemType::where('status', 1)
        ->select(DB::raw("id,name_$lang as name,color"))->get();
    }
}
