<?php

namespace App\Models\Master;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Prioritize extends Model
{
    protected $table = 'master_prioritize';
    public $timestamps = false;
    
    protected $fillable = ['name_th','name_en','status'];
    // protected $dates = ['created_at', 'updated_at'];

    

}
