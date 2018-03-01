<?php

namespace App\Models\Channel;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Channel extends Model
{
    protected $table = 'channels';
    public $timestamps = false;
    protected $fillable = ['domain_id','name', 'created_at','created_by','direct_message','type','icon','description','push_notification'];
    protected $dates = ['created_at'];

    public static function DirectMessage($userId1,$userId2){
    	$sql = "SELECT c.*
    			,u1.id as u1_user_id
                ,u1.first_name as u1_first_name
                ,u1.last_name as u1_last_name
                ,u2.id as u2_user_id
                ,u2.first_name as u2_first_name
                ,u2.last_name as u2_last_name
                FROM channels c
                LEFT JOIN users u1  
                ON c.name = u1.id 
                LEFT JOIN users u2  
                ON c.created_by = u2.id
                WHERE (name = $userId1 AND created_by = $userId2) 
                or  (created_by = $userId1 AND name = $userId2)";
        return collect(DB::select(DB::raw($sql)))->first();
    }

    public static function DirectMessageByChannelId($channelId){
        $sql = "SELECT c.*
                ,u1.id as u1_user_id
                ,u1.first_name as u1_first_name
                ,u1.last_name as u1_last_name
                ,u2.id as u2_user_id
                ,u2.first_name as u2_first_name
                ,u2.last_name as u2_last_name
                FROM channels c
                LEFT JOIN users u1  
                ON c.name = u1.id 
                LEFT JOIN users u2  
                ON c.created_by = u2.id
                WHERE c.id=$channelId";
        return collect(DB::select(DB::raw($sql)))->first();
    }
}
