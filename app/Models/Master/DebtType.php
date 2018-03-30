<?php

namespace App\Models\Master;

use App\User;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class DebtType extends Model
{
    protected $table = 'master_debt_type';
    public $timestamps = false;
    
    protected $fillable = ['name_th','name_en','status'];
    // protected $dates = ['created_at', 'updated_at'];

    public static function getData()
    {
        $lang = App::getLocale();
        return  self::where('status', 1)
        ->select(DB::raw("id,name_$lang as name"))->get();
    }
}
