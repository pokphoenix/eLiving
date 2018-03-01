<?php

namespace App\Models\User;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserHistoryEmail extends Model
{
    protected $table = 'user_history_email';
    public $timestamps = false;
    
    protected $fillable = ['email_old', 'email','created_by','created_at'];
    // protected $dates = ['created_at', 'updated_at'];
}
