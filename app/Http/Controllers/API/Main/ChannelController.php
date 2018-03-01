<?php

namespace App\Http\Controllers\API\Main;

use App\Http\Controllers\ApiController;
use App\Models\Channel\Channel;
use App\Models\Channel\ChannelAttachment;
use App\Models\Channel\ChannelMessage;
use App\Models\Channel\ChannelSeen;
use App\Models\Channel\ChannelUser;
use App\Models\Images;
use App\Models\Notification;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChannelController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($domainId){
        $data['can_create'] = (Auth()->user()->hasRole('admin.chat'))? true : false;
        // $data['channel_list'] = Auth()->user()->getChannel();

        $sql = "SELECT c.*
                FROM channels c
                WHERE c.type!=3 
                AND c.domain_id= ".Auth()->user()->recent_domain."
                AND c.direct_message=0 
                AND c.id not in (
                    SELECT channel_id FROM channel_users 
                    WHERE user_id = ".Auth()->user()->id." 
                    AND domain_id= ".Auth()->user()->recent_domain."
                    AND accept=1
                    AND show_list=1
                )
                ORDER BY id ASC;"
                ;
        $data['channel_list'] = DB::select(DB::raw($sql)) ; 
        return $this->respondWithItem($data);
    }

    public function getChannelJoin(){
        $data['channel_list'] = Auth()->user()->getChannelJoin() ; 
        return $this->respondWithItem($data);
    }

    public function getContact(){
        $data['contact_list'] = Auth()->user()->getContact() ; 
        return $this->respondWithItem($data);
    }


    public function contact($domainId){
        $sql = "SELECT u.*, CONCAT(u.first_name,' ',u.last_name) as name
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                FROM users u
                INNER JOIN user_domains ud 
                ON ud.id_card = u.id_card
                WHERE ud.domain_id=".Auth()->user()->recent_domain."
                AND ud.approve=1
                AND u.id !=".Auth()->user()->id."
                AND u.id not in (
                    select user_id 
                    from channel_users 
                    where channel_id in (
                        SELECT c.id FROM channels c
                                        JOIN channel_users cu 
                                        ON c.id = cu.channel_id
                                        WHERE cu.user_id = ".Auth()->user()->id."
                                        AND c.direct_message=1
                                        AND c.domain_id= ".Auth()->user()->recent_domain."
                    )
                    AND user_id != ".Auth()->user()->id."
                    AND show_list =1
                )"
                ;
        $query = DB::select(DB::raw($sql)) ; 
        foreach ($query as $key => $q) {
           $query[$key]->img = getBase64Img($q->img);
        }
        $data['contact_list'] = $query ;
        return $this->respondWithItem($data);
    } 
    public function contactDestroy($domainId,$channelId){
        ChannelUser::where('channel_id',$channelId)->where('user_id','<>',Auth()->user()->id)
        ->update(['show_list'=>0]);
        return $this->respondWithItem(['text'=>'success']);
    }

    public function validateName(Request $request,$domainId){
        $data = $request->input('name');
        $query = Channel::where('domain_id',$domainId)->where('name',$data)->first();
        echo (empty($query)) ? "true" : "false" ;
    }


    public function store(Request $request,$domainId){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        
        $repeatChannel = Channel::where('name',$post['name'])->where('domain_id',$domainId)->first();
        if(!empty($repeatChannel)){
            return $this->respondWithError('ชื่อห้องซ้ำ');
        }

        $channel = new  Channel();
        $channel->domain_id = $domainId ;
        $channel->created_at = Carbon::now() ;
        $channel->created_by = Auth()->user()->id ;
        $channel->name = $post['name'] ;
        $channel->type = $post['type'] ;
        $channel->icon = $post['icon'] ;
        $channel->description = $post['description'] ;
        $channel->save();

        //--- เพิ่มตัวผู้สร้างเข้าห้อง
        $cu = new ChannelUser();
        $cu->domain_id = $domainId ;
        $cu->channel_id = $channel->id ;
        $cu->user_id = Auth()->user()->id ;
        $cu->accept = 1 ;
        $cu->owner = 1 ;
        $cu->created_at = Carbon::now() ;
        $cu->save();

        $data['channel_id'] = $channel->id ;
        return $this->respondWithItem($data);
    }

    public function show($domainId,$channelId){




        $userId = Auth()->user()->id;
        $cu = ChannelUser::where('channel_id',$channelId)->where('user_id',$userId)->where('accept',1)->first();
            if (empty($cu)){
                return $this->respondWithError('คุณไม่มีสิทธิ์ในห้องนี้ค่ะ');
            }


        ChannelSeen::SetSeen($domainId,$channelId,$userId);
        

        $data['status']['is_owner'] =  $cu->owner ;
        $data['status']['push_notification'] =  $cu->push_notification ;


        $c = channel::find($channelId) ;
        if(!empty($c)){
            if($c->direct_message){
                ChannelUser::where('channel_id',$channelId)->update(['show_list'=>1]);
            }
           
        }
       



         




        $channel = Channel::DirectMessageByChannelId($channelId);
        if($channel->type!=1){
            //-- ถ้าไม่ใช่ห้อง public ต้องมีการเช็คว่าเข้าร่วมแล้วหรือยัง
           
        }
       
        $data['channels'] = $channel ;

        $seens = ChannelSeen::where('channel_id',$channelId)->where('seen_by','<>',$userId)->orderBy('id','asc')->get();



        $channelMessages = ChannelMessage::from('channel_messages as cm')
        ->join('users as u', 'u.id', '=', 'cm.created_by')
         ->leftjoin('channel_attachments as ca', 'ca.channel_message_id', '=', 'cm.id')
        ->select(DB::raw( "cm.*,u.first_name,u.last_name,UNIX_TIMESTAMP(cm.updated_at) as  updated_ts
            ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
            ,CASE WHEN u.onlined_at is not null AND (u.onlined_at+ INTERVAL ".CHECK_ONLINE_MINUTE." MINUTE) >= now()  THEN 1 ELSE 0 END  as is_online
             ,CONCAT( '".url('/public/upload/')."/', ca.path,'/',ca.filename) as attachment_path
            ,ca.file_displayname as attachment_name , ca.file_extension as attachment_extension" ))
        ->where('cm.channel_id',$channelId)
        
        ->orderBy('created_at','asc')
        ->get()->toArray();


        if(!empty($channelMessages)){
             foreach ($channelMessages as $key => $cm) {
                $channelMessages[$key]['img'] = getBase64Img($cm['img']);
                $channelMessages[$key]['has_seen'] = 0 ;
                $channelMessages[$key]['has_seen_date'] = null ;
                $seenCount = 0 ;

                $channelMessages[$key]['has_seen_count'] = $seenCount ;
                if($userId==$cm['created_by']){
                    if(!empty($seens)){
                        foreach ($seens as $jj => $seen) {
                            // echo  $channelMessages[$key]['id']." : ".$cm['id']." : ".$seen->channel_message_id."<BR>" ;
                            if( $cm['id'] <= $seen->channel_message_id  ){
                                $channelMessages[$key]['has_seen'] = 1 ;
                                $channelMessages[$key]['has_seen_date'] = date('Y-m-d H:i:s',strtotime($seen->seen_at)) ;

                               
                                // echo $cm->id." : ".$seen->channel_message_id."<BR>" ;
                                $seenCount++ ;
                                // echo " count : ".$seenCount."<BR>";

                               // break;
                            }
                        }
                        $channelMessages[$key]['has_seen_count'] = $seenCount ;   
                        
                    }
                }
                // 
                
            } 
        }
      
       
        $data['messages'] = $channelMessages;
        $data['member_channel'] = ChannelUser::getMember($domainId,$channelId);
        $data['member_request_channel'] = ChannelUser::getMember($domainId,$channelId,0);
       
       
        return $this->respondWithItem($data);

    }

    public function edit($domainId,$id){
        $userId = Auth()->user()->id;
        $cu = ChannelUser::where('channel_id',$id)->where('user_id',$userId)->where('accept',1)->first();
        if (empty($cu)){
            return $this->respondWithError('คุณไม่มีสิทธิ์ในห้องนี้ค่ะ');
        }

        $channel = Channel::find($id);
       
       
        $data['channel'] = $channel ;
       
  
        return $this->respondWithItem($data);

    }

     public function update(Request $request,$domainId,$id){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        
        $repeatChannel = Channel::where('id','<>',$id)->where('name',$post['name'])->where('domain_id',$domainId)->first();
        if(!empty($repeatChannel)){
            return $this->respondWithError('ชื่อห้องซ้ำ');
        }

        $channel =  Channel::find($id) ;
        $channel->domain_id = $domainId ;
        $channel->name = $post['name'] ;
        $channel->type = $post['type'] ;
        $channel->icon = $post['icon'] ;
        $channel->description = $post['description'] ;
        $channel->save();

        $data['channel_id'] = $id ;
        return $this->respondWithItem($data);
    }

    public function invite(Request $request,$domainId,$channelId){
        $post = $request->all();
        $validator = $this->validatorInvite($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        
        $channel = Channel::find($channelId) ;
        if(empty($channel)){
            return $this->respondWithError("not found this channel");
        }

        $user = [];
        $userIdList = "";
        try{
            if(isset($post['member_select'])&&(count($post['member_select'])>0)){
                foreach ($post['member_select'] as $key => $m) {
                    $user[$key]['domain_id'] = $domainId ;
                    $user[$key]['channel_id'] = $channelId ; 
                    $user[$key]['user_id'] = $m ;
                    $user[$key]['created_at'] = Carbon::now() ;
                    $user[$key]['accept'] = 1 ;
                    $userIdList .= ",$m" ;
                }

                ChannelUser::insert($user);

                if($userIdList!=""){
                    $userIdList = substr($userIdList, 1);
                    $sql = "select u.id_card,ud.noti_player_id,ud.noti_player_id_mobile 
                    from users as u 
                    inner join user_domains as ud 
                    on ud.id_card = u.id_card 
                    and ud.approve = 1
                    where ud.domain_id = $domainId and u.id in ($userIdList)";
                    $query = DB::select(DB::raw($sql));
                    if(!empty($query)){
                        Notification::addNotificationMulti($query,$domainId,'You invited to chat room '.$channel->name ,3,3,$channelId);
                    }
                }
                
            }
           

        }catch (\Exception $e) {
            $errors = $this->catchError($e);
            return $this->respondWithError($errors);
        }
        $data['text'] = 'success' ;
        return $this->respondWithItem($data);
    }

    public function member(Request $request,$domainId,$channelId){
        $data['channel'] = Channel::where('id',$channelId)->first();
        $sql = "SELECT u.id as user_id,u.first_name,u.last_name
                , UNIX_TIMESTAMP(cu.created_at) as created_at
                ,cu.owner
                ,cu.accept
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                FROM channel_users cu 
                LEFT JOIN users u
                ON cu.user_id = u.id
                WHERE cu.channel_id = $channelId
                AND cu.domain_id = $domainId
                ORDER BY cu.owner DESC ,cu.accept DESC
                " ;
        $query = DB::select(DB::raw($sql));
        $owners = [];
        $members = [];
        $memberRequests = [] ;
        foreach ($query as $q){
            // if($q->owner==1){
            //     $owners[] = $q ;
            // }elseif($q->accept==1){
            $q->img = getBase64Img($q->img);
            if($q->accept==1){
                $members[] = $q ;
            }else{
                $memberRequests[] = $q ;
            }
        } 

        $data['channel_members'] = $members;
        // $data['channel_owners'] = $owners;
        $data['channel_member_requests'] = $memberRequests;

        $statusJoin = ChannelUser::where('channel_id',$channelId)
        ->where('domain_id',$domainId)
        ->where('user_id',Auth()->user()->id)->first();

        $status['is_join'] = (isset($statusJoin) && $statusJoin->accept==1) ? true : false ;
        $status['is_can_request'] = (!empty($statusJoin)) ? true : false ;
        $status['is_can_invite'] = ( isset($statusJoin) && $statusJoin->accept==1) ? true : false ;
        $status['is_can_accept'] = ( isset($statusJoin) && $statusJoin->accept==1) ? true : false ;
        $status['is_can_kick'] = ( isset($statusJoin) && $statusJoin->owner==1) ? true : false ;
        $status['is_can_owner'] = ( isset($statusJoin) && $statusJoin->owner==1) ? true : false ;

        $data['action_status'] = $status ;


       

        return $this->respondWithItem($data);
    }

    public function join(Request $request,$domainId,$channelId){
        
        $ch = Channel::where('id',$channelId)->where('domain_id',$domainId)->first();
        if(empty($ch)){
            return $this->respondWithError("not found this channel");
        }
        $accept = 0 ;
        if($ch->type==1){
            $accept = 1 ;
        }

        //--- เพิ่มตัวผู้สร้างเข้าห้อง
        $cu = new ChannelUser();
        $cu->domain_id = $domainId ;
        $cu->channel_id = $channelId ;
        $cu->user_id = Auth()->user()->id ;
        $cu->accept = $accept ;
        $cu->created_at = Carbon::now() ;
        $cu->save();

        $userList = ChannelUser::from('channel_users as cu')
        ->join('users as u','u.id','=','cu.user_id')
        ->join('user_domains as ud', function ($join) {
            $join->on('ud.id_card', '=', 'u.id_card')
            ->on('ud.domain_id', '=', 'cu.domain_id' );
        })
        ->where('cu.channel_id',$channelId)
        ->where('cu.domain_id',$domainId)
        ->where('cu.owner',1)
        ->where('ud.approve',1)
        ->select(DB::raw("ud.noti_player_id,ud.noti_player_id_mobile"))
        ->get();
        if(!empty($userList)){
            foreach ($userList as $key => $u) {
                if(isset($u->noti_player_id)){
                    $parsedBody['user_id_list'][] = $u->noti_player_id;
                }
                 if(isset($u->noti_player_id_mobile)){
                    $parsedBody['user_id_list'][] = $u->noti_player_id_mobile;
                }
            }
        }

        if(!empty($parsedBody['user_id_list'])){
            $parsedBody['direct'] = true;
            $parsedBody['message'] = 'Request to join on room '.$ch->name ;
            $sendNoti = Notification::sendNoti($parsedBody);
        }
        

        $data['channel_id'] = $channelId ;
        $data['accept'] = $accept ;
        $data['member_channel'] = ChannelUser::getMember($domainId,$channelId);
        $data['member_request_channel'] = ChannelUser::getMember($domainId,$channelId,0);
        return $this->respondWithItem($data);
    }

    public function accept(Request $request,$domainId,$channelId){
        $requesterId = $request->input('user_id');  
        $update['accept'] = 1 ; 

        $channel = Channel::find($channelId) ;
        if(empty($channel)){
            return $this->respondWithError("not found this channel");
        }

        $cu = ChannelUser::where('user_id',$requesterId)
        ->where('channel_id',$channelId)
        ->where('domain_id',$domainId)
        ->update($update);

       
        $user = User::where('id',$requesterId)->first();
        $notiMsg = "You can chat in room ".$channel->name;
        $notiStatus = 4;
        $notiType = 3;
        if(!empty($user)){
            Notification::addNotificationDirect($user->id_card,$domainId,$notiMsg,$notiStatus,$notiType,$channelId);
        }

        $data['channel_id'] = $channelId ;
        $data['member_channel'] = ChannelUser::getMember($domainId,$channelId);
        $data['member_request_channel'] = ChannelUser::getMember($domainId,$channelId,0);
        return $this->respondWithItem($data);
    }

    public function owner(Request $request,$domainId,$channelId){
        $requesterId = $request->input('user_id');  
        $update['owner'] = 1 ; 
        $cu = ChannelUser::where('user_id',$requesterId)
        ->where('channel_id',$channelId)
        ->where('domain_id',$domainId)
        ->update($update);
        $data['channel_id'] = $channelId ;
        return $this->respondWithItem($data);
    }

    public function kick(Request $request,$domainId,$channelId){
        $requesterId = $request->input('user_id');  
        $cu = ChannelUser::where('user_id',$requesterId)
        ->where('channel_id',$channelId)
        ->where('domain_id',$domainId)
        ->delete();
        $data['channel_id'] = $channelId ;
        $data['member_channel'] = ChannelUser::getMember($domainId,$channelId);
        $data['member_request_channel'] = ChannelUser::getMember($domainId,$channelId,0);
        return $this->respondWithItem($data);
    }

    public function leave(Request $request,$domainId,$channelId){
        $userId = auth()->user()->id;

        //--- นับจำนวน Owner 
        $sql = "SELECT count(owner) as cnt
                FROM channel_users
                WHERE channel_id = $channelId 
                AND domain_id = $domainId
                AND owner=1 " ;
        $owner = DB::select(DB::raw($sql));
        $cu = ChannelUser::where('user_id',$userId)
        ->where('channel_id',$channelId)
        ->where('domain_id',$domainId)
        ->first();
        //-- ถ้าเป็น owner คนสุดท้าย ห้ามลบ
        if($cu->owner==1&&$owner[0]->cnt==1){
             return $this->respondWithError("ไม่สามารถออกจากห้องนี้ได้เนื่องจากคุณเป็น เจ้าของห้องเพียงคนเดียว");
        }   
        ChannelUser::where('user_id',$userId)
        ->where('channel_id',$channelId)
        ->where('domain_id',$domainId)
        ->delete();

        $data['channel_id'] = $channelId ;
        return $this->respondWithItem($data);
    }




    public function chat(Request $request,$domainId,$channelId){

        $channel = Channel::find($channelId) ;
        if(empty($channel)){
            return $this->respondWithError( $this->langMessage("ไม่พบห้องแชทนี้", "not found this channel") );
        }

        $post = $request->all();
        //--- เพิ่มตัวผู้สร้างเข้าห้อง
        $cm = new ChannelMessage();
        $cm->domain_id = $domainId ;
        $cm->channel_id = $channelId ;
        $cm->created_by = Auth()->user()->id ;
        $cm->created_at = Carbon::now();
        $cm->updated_at = Carbon::now();
        $cm->text = $post['text'] ;
        $cm->type = $post['type'] ;
        $cm->save();
        
        $data['channel'] =  Channel::DirectMessageByChannelId($channelId);
        $data['chat'] = ChannelMessage::from('channel_messages as cm')
        ->join('users as u', 'u.id', '=', 'cm.created_by')
        ->select(DB::raw( "cm.*,u.first_name,u.last_name,UNIX_TIMESTAMP(cm.updated_at) as  updated_ts 
            ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
            ,CASE WHEN u.onlined_at is not null AND (u.onlined_at+ INTERVAL ".CHECK_ONLINE_MINUTE." MINUTE) >= now()  THEN 1 ELSE 0 END  as is_online" ))
        ->where('cm.id',$cm->id)
        ->first();


        
        
        $sql = "SELECT ud.noti_player_id,ud.noti_player_id_mobile FROM channel_users as cu 
                INNER JOIN users as u ON u.id = cu.user_id 
                INNER JOIN user_domains as ud  ON ud.id_card = u.id_card AND ud.domain_id=cu.domain_id
                WHERE cu.channel_id=$channelId 
                AND  cu.domain_id=$domainId
                AND cu.accept=1
                AND cu.push_notification=1 
                AND ud.approve=1 
                AND (cu.push_off_at is null 
                    OR (push_off_at+INTERVAL 1 MINUTE) <= now() 
                )
                " ;
                
        $userList = DB::select(DB::raw($sql));

      

        if(!empty($userList)){
            foreach ($userList as $key => $u) {
               
                if(isset($u->noti_player_id)){
                    $parsedBody['user_id_list'][] = $u->noti_player_id;
                }
                 if(isset($u->noti_player_id_mobile)){
                    $parsedBody['user_id_list'][] = $u->noti_player_id_mobile;
                }
            }
        }
        
        if(!empty($parsedBody['user_id_list'])){
            $channelName = $channel->name ;
            if($channel->direct_message==1){
                $user = User::find($channel->name) ;
                if(!empty($user))
                $channelName = (App::isLocale('en')? '' : 'คุณ ' ).$user->first_name ;
            }


            $parsedBody['direct'] = true;

            if(App::isLocale('en')){
                $parsedBody['message'] = "message \"".cutStrlen($post['text'],SUB_STR_MESSAGE)."\" from ".$channelName ;
            }else{
                $parsedBody['message'] = "ข้อความ \"".cutStrlen($post['text'],SUB_STR_MESSAGE)."\" จาก".$channelName ;
            }

          
            $sendNoti = Notification::sendNoti($parsedBody);
        }

        return $this->respondWithItem($data);
    }

    public function chatAttachment(Request $request,$domainId,$channelId){

        $channel = Channel::find($channelId) ;
        if(empty($channel)){
            return $this->respondWithError( $this->langMessage("ไม่พบห้องแชทนี้", "not found this channel") );
        }

        $post = $request->all();
       
        $img = Images::upload($post['attachment']);
        if(!$img['result']){
            return $this->respondWithError($img['error']);
        }
        if(isset($img)&&isset($img['file'])){
            $cm = new ChannelMessage();
            $cm->domain_id = $domainId ;
            $cm->channel_id = $channelId ;
            $cm->created_by = Auth()->user()->id ;
            $cm->created_at = Carbon::now();
            $cm->updated_at = Carbon::now();
            $cm->text = '' ;
            $cm->type = $post['type'] ;
            $cm->save();
            $filesData = [];
            if(is_array($img['file'])){
                foreach ($img['file'] as $key => $v) {
                   
                    $filesData[$key]['channel_id'] = $channelId ;
                    $filesData[$key]['channel_message_id'] =  $cm->id ;
                    $filesData[$key]['domain_id'] = $domainId ;
                    $filesData[$key]['path'] = $v['filePath'];
                    $filesData[$key]['filename'] =  $v['fileName'] ;
                    $filesData[$key]['file_displayname'] =  $v['fileDisplayName'] ;
                    $filesData[$key]['file_extension'] =  $v['fileExtension'] ;
                }

                $attach = ChannelAttachment::insert($filesData);
            }
        }

         //--- เพิ่มตัวผู้สร้างเข้าห้อง
           

       
       
        
        $data['channel'] =  Channel::DirectMessageByChannelId($channelId);
        $data['chat'] = ChannelMessage::from('channel_messages as cm')
        ->join('users as u', 'u.id', '=', 'cm.created_by')
        ->leftjoin('channel_attachments as ca', 'ca.channel_message_id', '=', 'cm.id')
        ->select(DB::raw( "cm.*,u.first_name,u.last_name,UNIX_TIMESTAMP(cm.updated_at) as  updated_ts 
            ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
            ,CASE WHEN u.onlined_at is not null AND (u.onlined_at+ INTERVAL ".CHECK_ONLINE_MINUTE." MINUTE) >= now()  THEN 1 ELSE 0 END  as is_online
            ,CONCAT( '".url('/public/upload/')."/', ca.path,'/',ca.filename) as attachment_path
            ,ca.file_displayname as attachment_name , ca.file_extension as attachment_extension" ))
        ->where('cm.id',$cm->id)
        ->first();


        return $this->respondWithItem($data);
    }

    public function push(Request $request,$domainId,$channelId){

        $channel = Channel::find($channelId) ;
        if(empty($channel)){
            return $this->respondWithError("not found this channel");
        }


        $userId = Auth()->user()->id ;

        $channelUser = ChannelUser::where('channel_id',$channelId)
        ->where('domain_id',$domainId)
        ->where('user_id',$userId)
        ->first();
        if(empty($channelUser)){
            return $this->respondWithError("not found this channel");
        }


        $noti = 0 ; $notiTxt = "Turn off" ;
        if($channelUser->push_notification==0){
            $noti = 1 ; 
            $notiTxt = "Turn on" ;
        }

        ChannelUser::where('channel_id',$channelId)
        ->where('domain_id',$domainId)
        ->where('user_id',$userId)
        ->update(['push_notification'=>$noti]);
        

        $data['push_notification_status'] = $noti ;
        $data['push_notification_text'] =  $notiTxt." push notification success" ;

        return $this->respondWithItem($data);
    }

    public function pushoff(Request $request,$domainId,$channelId){
         ChannelUser::where('channel_id',$channelId)
        ->where('domain_id',$domainId)
        ->where('user_id',Auth()->user()->id)
        ->update(['push_off_at'=>Carbon::now()]);
        return $this->respondWithItem(['push_off_at'=>Carbon::now()]);
    }


    public function directChat(Request $request,$domainId){
        $post = $request->all();

        $validator = $this->validatorDirectChannel($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        
        $userId = Auth()->user()->id ;
        $insert = true;
        
        $query = Channel::DirectMessage($post['uid'],$userId);
        if(!empty($query)){
            $insert = false;
        }


        if($insert){
            $channel = new  Channel();
            $channel->domain_id = $domainId ;
            $channel->created_at = Carbon::now() ;
            $channel->created_by = Auth()->user()->id ;
            $channel->name = $post['uid'] ;
            $channel->type = 0 ;
            $channel->direct_message = 1 ;
            $channel->save();

             //--- เพิ่มตัวผู้สร้างเข้าห้อง
            $cu = new ChannelUser();
            $cu->domain_id = $domainId ;
            $cu->channel_id = $channel->id ;
            $cu->user_id = $post['uid'] ;
            $cu->accept = 1 ;
            $cu->owner = 1 ;
            $cu->created_at = Carbon::now() ;
            $cu->save();

            $cu2 = new ChannelUser();
            $cu2->domain_id = $domainId ;
            $cu2->channel_id = $channel->id ;
            $cu2->user_id = Auth()->user()->id ;
            $cu2->accept = 1 ;
            $cu2->owner = 1 ;
            $cu2->created_at = Carbon::now() ;
            $cu2->save();
            $query = Channel::DirectMessage($post['uid'],$userId);
        }else{
            $cu3 = Channel::where('created_by',Auth()->user()->id)->where('name',$post['uid'])->first();
            if(empty($cu3)){
                $cu3 = Channel::where('created_by',$post['uid'])->where('name',Auth()->user()->id)->first();
            }
            if(!empty($cu3)){
                ChannelUser::where('channel_id',$cu3->id)->update(['show_list'=>1]);
            }
            // 
        }
        $data['channel'] =  $query;
        $chats = ChannelMessage::from('channel_messages as cm')
        ->join('users as u', 'u.id', '=', 'cm.created_by')
        ->leftJoin('channels as c', 'c.id', '=', 'cm.channel_id')
        ->select(DB::raw('cm.*,u.first_name,u.last_name,UNIX_TIMESTAMP(cm.updated_at) as  updated_ts'))
        ->where('cm.channel_id',$query->id)
        ->orderBy('cm.created_at','desc')
        ->limit(5)
        ->get()->toArray();
       
        if(!empty($chats)){
            $sortArray = array(); 
            foreach($chats as $chat){ 
                foreach($chat as $key=>$v){ 
                    if(!isset($sortArray[$key])){ 
                        $sortArray[$key] = array(); 
                    } 
                    $sortArray[$key][] = $v; 
                } 
            } 

            $orderby = "created_at"; //change this to whatever key you want from the array 

            array_multisort($sortArray[$orderby],SORT_ASC,$chats); 
        }
        

        $data['chat'] = $chats ;
       
        return $this->respondWithItem($data);
    }

    public function destroy($domainId,$id){
        ChannelAttachment::where('channel_id',$id)->delete();
        ChannelMessage::where('channel_id',$id)->delete();
        ChannelUser::where('channel_id',$id)->delete();
        Channel::find($id)->delete();
        return $this->respondWithItem(['text'=>'Delete success']);
    } 

    public function destroyMessage($domainId,$id){
        $channel = ChannelMessage::where('id',$id)->first();
        if(empty($channel)){
            return $this->respondWithError($this->langMessage('Not found this message','ไม่พบข้อความนี้'));
        }
        $channelId = $channel->channel_id ;
        $channel->delete();
        return $this->respondWithItem(['text'=>$this->langMessage('Delete Success','ลบข้อความสำเร็จ')]);
    } 
    public function pinMessage($domainId,$id){
        $channel = ChannelMessage::where('id',$id)->first();
        if(empty($channel)){
            return $this->respondWithError($this->langMessage('Not found this message','ไม่พบข้อความนี้'));
        }
        $channelId = $channel->channel_id ;
        
        // $cm = ChannelMessage::where('channel_id',$channelId)
        // ->where('id','<>',$id)
        // ->update(['pin'=>0]);

       

        $channel->update(['pin'=>1]);

        return $this->respondWithItem(['text'=>$this->langMessage('Pin Message Success','ปักหมุดข้อความสำเร็จ')]);
    } 
    public function unpinMessage($domainId,$id){
        $channel = ChannelMessage::where('id',$id)->update(['pin'=>0]);
        return $this->respondWithItem(['text'=>$this->langMessage('Unpin Message Success','ปลดปักหมุดข้อความสำเร็จ')]);
    }

    private function validator($data)
    {
        return Validator::make($data, [
            'name' => 'required|string|min:2|max:255',
            'type' => 'required|numeric',
        ]);
    }
    private function validatorInvite($data)
    {
        return Validator::make($data, [
            'member_select' => 'required',
        ]);
    }

    private function validatorDirectChannel($data)
    {
        return Validator::make($data, [
            'type' => 'required|numeric',
            'uid' => 'required|numeric',
            'direct_message' => 'required|numeric',
        ]);
    }

 
}
