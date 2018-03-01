<?php

namespace App\Models\Admin;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PreWelcome extends Model
{
    protected $table = 'pre_welcome';
    public $timestamps = false;

    protected $fillable = ['text','domain_id'];
    

}
