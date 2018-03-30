<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
    protected $table = 'notifications';
    public $timestamps = false;
    protected $fillable = ['domain_id','id_card', 'message','type','status','seen','role_id','ref_id','created_at'];

   
    private static $APP_ID = Service::ONESIGNAL_APP_ID ;
    private static $APP_KEY = Service::ONESIGNAL_APP_KEY ;
    
    public static function addNotificationDirect($idCard, $domainId, $notiMsg, $notiStatus, $notiType, $refId = null)
    {
        $notification = new Notification();
        $notificationData['domain_id'] =  $domainId;
        $notificationData['id_card'] = $idCard ;
        $notificationData['message'] =  $notiMsg;
        $notificationData['status'] =  $notiStatus;
        $notificationData['type'] =  $notiType;
        $notificationData['created_at'] =  Carbon::now();
        if (isset($refId)) {
            $notificationData['ref_id'] =  $refId;
        }
        
        $notification->fill($notificationData)->save();

        $sql = "SELECT *
                FROM  user_domains 
                WHERE id_card = '".$idCard."' AND domain_id=".$domainId ;
        $userDomain = collect(DB::select(DB::raw($sql)))->first();

        if (isset($userDomain->noti_player_id)||isset($userDomain->noti_player_id_mobile)) {
            $parsedBody['direct'] = true;
            if (isset($userDomain->noti_player_id)) {
                $parsedBody['user_id_list'][] = $userDomain->noti_player_id ;
            }
            if (isset($userDomain->noti_player_id_mobile)) {
                $parsedBody['user_id_list'][] = $userDomain->noti_player_id_mobile ;
            }
            $parsedBody['message'] = $notiMsg ;
            $sendNoti = Notification::sendNoti($parsedBody);
        }
    }

    public static function addNotificationMulti($lists, $domainId, $notiMsg, $notiStatus, $notiType, $refId = null, $exceptYourself = false)
    {
        $parsedBody = [];
        foreach ($lists as $key => $list) {
            if ($exceptYourself&&($list->id_card==auth()->user()->id_card)) {
            } else {
                $notification[$key]['domain_id'] =  $domainId;
                $notification[$key]['id_card'] = $list->id_card ;
                $notification[$key]['message'] =  $notiMsg;
                $notification[$key]['status'] =  $notiStatus;
                $notification[$key]['type'] =  $notiType;
                $notificationData['created_at'] =  Carbon::now();
                if (isset($refId)) {
                    $notification[$key]['ref_id'] =  $refId;
                }
               
                if (isset($list->noti_player_id)) {
                    $parsedBody['user_id_list'][] = $list->noti_player_id ;
                }
                if (isset($list->noti_player_id_mobile)) {
                    $parsedBody['user_id_list'][] = $list->noti_player_id_mobile ;
                }
            }
        }



        if (isset($notification)) {
            Notification::insert($notification);
        }
        if (isset($parsedBody['user_id_list'])) {
            $parsedBody['direct'] = true;
            $parsedBody['message'] = $notiMsg ;
            $sendNoti = Notification::sendNoti($parsedBody);
        }
    }

    public static function sendNoti($parsedBody)
    {
        // { "direct":true,"user_id_list":[],"contents":"","include_android_reg_ids": }


        $direct = $parsedBody['direct'] ;
        $user_id_list =  isset($parsedBody['user_id_list']) ? $parsedBody['user_id_list'] : "" ;
        $deviceId = isset($parsedBody['include_android_reg_ids']) ? $parsedBody['include_android_reg_ids'] : "" ;
       
        $message =isset($parsedBody['message']) ? $parsedBody['message'] : "" ;
        $fields = array(
            'app_id' => self::$APP_ID ,
            'contents' =>  [ 'en'=>$message ] ,
        );
        if (isset($parsedBody['data'])) {
            $fields['data'] = $parsedBody['data'];
        }
        if ($direct) {
            foreach ($user_id_list as $key => $p) {
                if (!empty($p)) {
                    $fields['include_player_ids'][] = $p ;
                }
            }
            $notiType = "direct" ;
        } else {
            $fields['included_segments'] = ['All'] ;
            $notiType = "subscript" ;
        }

        $fields = json_encode($fields);
        $sql =" INSERT INTO `tests` (`desc`) VALUES ('$fields')" ;
        DB::insert(DB::raw($sql)) ;

        $url = "https://onesignal.com/api/v1/notifications" ;
        return self::curl_send($url, $notiType, $fields) ;
    }

    private static function curl_send($url, $type = null, $fields = null)
    {
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($type=="subscript") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.self::$APP_KEY));
        } elseif ($type=="direct") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.self::$APP_KEY));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if (isset($fields)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $return = json_decode($response, true);
        return $return ;
    }
}
