<?php

namespace App\Models\Channel;

use App\Models\Master\WordBlackList;
use App\Models\Master\WordWhiteList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Channel extends Model
{
    protected $table = 'channels';
    public $timestamps = false;
    protected $fillable = ['domain_id','name', 'created_at','created_by','direct_message','type','icon','description','push_notification'];
    protected $dates = ['created_at'];

    public static function DirectMessage($userId1, $userId2)
    {
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

    public static function DirectMessageByChannelId($channelId)
    {
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


    public static function messageValidate($text)
    {

        $whitelist = WordWhiteList::all()->toArray();
        $blacklist = WordBlackList::all()->toArray();



        $arrayWhitelist = [];

        foreach ($whitelist as $key => $w) {
            $symbol = str_pad("", mb_strlen($w['text']), "*");
            // echo "$w -> $symbol<BR>";
           
            $lastPos = 0;
            $positions = array();
            $lastPos = mb_strpos($text, $w['text'], $lastPos);
            while (($lastPos = mb_strpos($text, $w['text'], $lastPos))!== false) {
                $arrayWhitelist[$w['id']]['id'] = $w['id'];
                $arrayWhitelist[$w['id']]['text'] = $w['text'];
                $arrayWhitelist[$w['id']]['pos'][] = $lastPos ;
                $lastPos = $lastPos + mb_strlen($w['text']);
            }
            $text = preg_replace("/(".$w['text'].")/", $symbol, $text);
        }


        foreach ($blacklist as $key => $b) {
            $symbol = str_pad("", mb_strlen($b['text']), "*");
            // echo "$b -> $symbol<BR>";
            $text = preg_replace("/(".$b['text'].")/", $symbol, $text);
        }
        if (!empty($arrayWhitelist)) {
            foreach ($arrayWhitelist as $key => $aw) {
                $id = $aw['id'] ;
                $whiteText = $aw['text'];
                  var_dump($aw);
                die;
                foreach ($aw['pos'] as $k => $pos) {
                    $text = self::mb_substr_replace(
                        $text,
                        $whiteText,
                        $pos,
                        ($pos+mb_strlen($whiteText))
                    );
                }
            }
        }
        
        return $text ;
    }


    private static function mb_substr_replace($output, $replace, $posOpen, $posClose)
    {
            return mb_substr($output, 0, $posOpen).$replace.mb_substr($output, $posClose);
    }
}
