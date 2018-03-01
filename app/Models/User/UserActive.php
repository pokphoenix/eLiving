<?php

namespace App\Models\User;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserActive extends Model
{
    protected $table = 'user_auto_actives';
    public $timestamps = false;
    
    protected $fillable = ['token', 'email','id_card','created_at','active'];
    protected $dates = ['created_at'];
}
