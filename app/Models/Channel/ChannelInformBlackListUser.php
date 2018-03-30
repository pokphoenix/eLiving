<?php

namespace App\Models\Channel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChannelInformBlackListUser extends Model
{
    protected $table = 'channel_blacklist_inform';
    public $timestamps = false;
    protected $fillable = ['domain_id','user_id','created_at','created_by','text'];
    protected $dates = ['created_at', 'updated_at'];
}
