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
    

    public static function getTaskCategory($type)
    {
        $lang = getLang();
        $taskCategory = TaskCategory::where('status', 1)
        ->where('type', $type)
        ->select(DB::raw("id,name_$lang as name,color"))
        ->get();
        return $taskCategory;
    }
}
