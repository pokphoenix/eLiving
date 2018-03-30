<?php

namespace App\Models\Channel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChannelHide extends Model
{
    protected $table = 'channel_user_hide_message';
    public $timestamps = false;
    protected $fillable = ['channel_id','domain_id', 'user_id','message_id'];
    protected $dates = ['created_at', 'updated_at'];
}
