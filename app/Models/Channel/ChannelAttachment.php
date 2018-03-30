<?php

namespace App\Models\Channel;

use Illuminate\Database\Eloquent\Model;

class ChannelAttachment extends Model
{
    protected $table = 'channel_attachments';
    public $timestamps = false;
    protected $fillable = ['channel_id','domain_id', 'channel_message_id','path','filename','file_displayname','file_extension'];
    protected $dates = ['created_at', 'updated_at'];
}
