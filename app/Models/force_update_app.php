<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class force_update_app extends Model
{
    protected $table = 'force_update_app';

    protected $fillable = ['id','platform','version','is_force'];
}
