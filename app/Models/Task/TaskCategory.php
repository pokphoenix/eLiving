<?php

namespace App\Models\Task;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class TaskCategory extends Model
{
    protected $table = 'master_task_category';
    public $timestamps = false;
    protected $fillable = ['name_en','status', 'color','type','name_th'];
   	

   	public static function getTaskCategory($type){
   		$taskCategory = TaskCategory::where('status',1)->where('type',$type) ;
        if(App::getLocale()=='th'){
            $taskCategory->select(DB::raw('id,name_th as name,color'));
        }else{
            $taskCategory->select(DB::raw('id,name_th as name,color'));
        }

       return $taskCategory->get();
   	}
}
