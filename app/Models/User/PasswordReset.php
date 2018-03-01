<?php

namespace App\Models\User;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PasswordReset extends Model
{
    protected $table = 'password_resets';
    public $timestamps = false;
    
    protected $fillable = ['email', 'token','created_at'];
    // protected $dates = ['created_at', 'updated_at'];
}
