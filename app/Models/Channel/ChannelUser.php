<?php

namespace App\Models\Channel;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChannelUser extends Model
{
    protected $table = 'channel_users';
    public $timestamps = false;
    protected $fillable = ['channel_id','domain_id', 'user_id','accept','owner','created_at','push_notification','show_list','push_off_at'];
    protected $dates = ['created_at', 'updated_at'];

    public static function getMember($domainId,$channelId,$accept=1){
    	$sql = "SELECT u.id as user_id,u.first_name,u.last_name
                , UNIX_TIMESTAMP(cu.created_at) as created_at
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,CASE WHEN onlined_at is not null AND (onlined_at+ INTERVAL ".CHECK_ONLINE_MINUTE." MINUTE) >= now()  THEN 1
                ELSE 0 END  as is_online
                ,cu.owner
                ,cu.accept
                FROM channel_users cu 
                INNER JOIN users u
                ON cu.user_id = u.id
                WHERE cu.channel_id = $channelId
                AND cu.domain_id = $domainId
                AND cu.accept =$accept
                ORDER BY cu.owner DESC ,cu.accept DESC
                " ;
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return $query;
    }

}



