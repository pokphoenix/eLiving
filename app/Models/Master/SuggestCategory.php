<?php

namespace App\Models\Master;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class SuggestCategory extends Model
{
    protected $table = 'master_suggest_category';
    public $timestamps = false;
    protected $fillable = ['name_en','status', 'color','name_th'];
   	
    public static function getCategory(){
      $lang = getLang();
      $sql = "id,name_$lang as name,color";
      return SuggestCategory::where('status',1)->select(DB::raw($sql))->get() ;
    }
  
}
