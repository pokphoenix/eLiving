<?php

namespace App\Models\Channel;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class ChannelSeen extends Model
{
    protected $table = 'channel_seen';
    protected $fillable = ['channel_id','channel_message_id','seen_at', 'seen_by','domain_id'];
    public $timestamps = false;
    protected $dates = ['seen_at'];

    public static function SetSeen($domainId, $channelId, $userId)
    {

        $sql ="SELECT 
				(SELECT id FROM channel_messages 
				WHERE channel_id=$channelId 
				ORDER BY created_at DESC
				LIMIT 1 
				) as lastest_message_id
				,(SELECT channel_message_id 
				FROM channel_seen 
				WHERE channel_id=$channelId AND seen_by =$userId
				ORDER BY seen_at DESC LIMIT 1) as lastest_seen_message_id" ;
        $msg = collect(DB::select(DB::raw($sql)))->first();
        if ($msg->lastest_message_id!=$msg->lastest_seen_message_id) {
            $seen = new ChannelSeen();
            $seen->channel_id = $channelId;
            $seen->channel_message_id = $msg->lastest_message_id ;
            $seen->seen_at = Carbon::now();
            $seen->seen_by = Auth()->user()->id;
            $seen->domain_id =$domainId;
            $seen->save();
        }
    }
}
