<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class EstickerReason extends Model
{
    protected $table = 'master_esticker_reason';
    public $timestamps = false;
    protected $fillable = ['name_en','status','name_th'];
    
    public static function getData()
    {
        $lang = getLang();
        $sql = "id,name_$lang as name";
        return self::where('status', 1)->select(DB::raw($sql))->get() ;
    }
}
