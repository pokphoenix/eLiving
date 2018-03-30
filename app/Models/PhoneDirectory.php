<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PhoneDirectory extends Model
{
    protected $table = 'phone_directory';
    public $timestamps = false;

    protected $fillable = ['text','domain_id'];
}
