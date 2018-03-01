<?php

namespace App\Models\Routine;


use App\Facades\Permission;
use App\Models\Room;
use App\Models\Task\TaskCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoutineItem extends Model
{
    protected $table = 'routines';

    protected $fillable = ['routine_id','created_at','created_by'];
    protected $dates = ['created_at'];

}
