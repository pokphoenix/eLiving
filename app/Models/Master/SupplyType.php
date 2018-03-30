<?php

namespace App\Models\Master;

use App\User;
use Illuminate\Database\Eloquent\Model;
use DB;

class SupplyType extends Model
{
    protected $table = 'master_supplies_type';
    public $timestamps = false;
    
    protected $fillable = ['name_th','name_en','status'];
    // protected $dates = ['created_at', 'updated_at'];

    public static function getData()
    {
        $lang = getLang();
        return  self::where('status', 1)
        ->select(DB::raw("id,name_$lang as name"))->get();
    }
}
