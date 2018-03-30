<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use App;
use DB;

class TitleName extends Model
{
    protected $table = 'master_title_name';
    public $timestamps = false;
    protected $fillable = ['name_en','status','name_th'];
    
    public static function getData()
    {
        $lang = getLang();
        return  self::where('status', 1)
        ->select(DB::raw("id,name_$lang as name"))->get();
    }
}
