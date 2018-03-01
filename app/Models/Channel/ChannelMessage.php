<?php

namespace App\Models\Channel;


use Illuminate\Database\Eloquent\Model;

class ChannelMessage extends Model
{
    protected $table = 'channel_messages';
    protected $fillable = ['channel_id','domain_id','text', 'created_at','type','created_by','pin'];
    public $timestamps = false;
    protected $dates = ['created_at', 'updated_at'];

}
