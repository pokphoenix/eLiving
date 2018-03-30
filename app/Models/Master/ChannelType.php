<?php

namespace App\Models\Master;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ChannelType extends Model
{
    protected $table = 'master_channel_type';
    public $timestamps = false;
    
    protected $fillable = ['name','status'];
    // protected $dates = ['created_at', 'updated_at'];
}
