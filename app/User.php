<?php

namespace App;


use App\Models\Address;
use App\Models\Domain;
use App\Models\Notification ;
use App\Models\Room;
use App\Models\RoomUser;
use Carbon\Carbon;
use Doctrine\Instantiator\Exception\UnexpectedValueException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password','first_name','last_name','api_token','tel','job_title','id_card','recent_domain','facebook_id','profile_url','displayname','remark','is_review','profile_url','avartar_id','alert_text','onlined_at'
        ,'nick_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected function setPrimaryKey($key)
    {
      $this->primaryKey = $key;
    }

    public function domains(){
        $sql = "SELECT *
                FROM  user_domains 
                WHERE id_card = ".$this->id_card ;
        $query = DB::select(DB::raw($sql));

        return $query ;
    } 

    public function getProfile(){
        $user = Auth()->user() ;
        $user->profile_img = url('/public/img/profile/0.png')  ;
        if(isset($user->profile_url) && $user->avartar_id==0 ){
            $user->profile_img = $user->profile_url ; 
        }elseif($user->avartar_id!=0){
            $user->profile_img = url("/public/img/profile/".$user->avartar_id.".png") ;
        }

        $user->profile_img = getBase64Img($user->profile_img);
        return   $user ;
    } 


    public function domain(){
        return $this->belongsToMany('App\Models\Domain', 'user_domains', 'id_card', 'domain_id');
    } 
    public function role(){
        return $this->belongsToMany('App\Models\Role', 'role_user', 'id_card', 'role_id');
    }
    public function room(){
        return $this->belongsToMany('App\Models\Room', 'user_rooms', 'id_card', 'room_id');
    }
    
    public function syncData(){
        $sql = "SELECT *
                FROM  user_domains 
                WHERE id_card = ".$this->id_card ;
        $query = DB::select(DB::raw($sql));
        if(count($query)==1){
            $this->recent_domain = $query[0]->domain_id;
            $this->save();
        }
        if(empty($query)){
            foreach ($query as $key => $q) {
                $user->makeUserRole('user',$q->domain_id);
            }
           
        }
    }

    public function joinDomain($domainId,$approve){
        $approvedAt = null ;
        $createdAt = Carbon::now();
        if($approve){
            $approvedAt = $createdAt ;
        }

      
        $sql = "SELECT *
                FROM  user_domains 
                WHERE id_card = ".$this->id_card." AND domain_id=".$domainId ;
        $query = DB::select(DB::raw($sql));
        if(empty($query)){
            // $sql = "INSERT INTO user_domains (id_card,domain_id,approve,approved_at,created_at)
            //     VALUES ('".$this->id_card."',".$domainId.",".$approve.",".$approvedAt.",".$createdAt.")";
            // $query = DB::select(DB::raw($sql));

            DB::insert('INSERT INTO user_domains (id_card,domain_id,approve,approved_at,created_at)
                VALUES  (?,?,?,?,?)', [$this->id_card, $domainId,$approve,$approvedAt,$createdAt]);
        }else{
            DB::update("UPDATE user_domains SET approve=?,approved_at=? WHERE  id_card=? AND domain_id = ?", [$approve,$approvedAt,$this->id_card,$domainId]);
        }       

        // if (! ($this->domain->contains($domainId))) {
        //     $this->domain()->attach([$domainId =>['created_at'=>Carbon::now(),'approve'=>$approve ]]);
        // }
    }
    public function joinRoom($roomId){
        $sql = "SELECT *
                FROM  user_rooms 
                WHERE id_card = ".$this->id_card." AND room_id=".$roomId ;
        $query = DB::select(DB::raw($sql));
        if(empty($query)){
            $sql = "INSERT INTO user_rooms (id_card,room_id)
                VALUES ('".$this->id_card."',".$roomId.")";
            $query = DB::select(DB::raw($sql));
        }        
        // if (! ($this->room->contains($roomId))) {
        //     $this->room()->attach($roomId);
        // }
    } 
    public function insertAddress($domainId,$post){
        $addressData['id_card'] =  $this->id_card ;
        $addressData['domain_id'] =  $domainId ;
        $addressData['address'] =  isset($post['address']) ? $post['address'] : null ;
        $addressData['district_id'] = isset($post['district_id']) ? $post['district_id'] : null;
        $addressData['amphur_id'] = isset($post['amphur_id']) ? $post['amphur_id'] : null ;
        $addressData['province_id'] = isset($post['province_id']) ? $post['province_id'] : null ;
        $addressData['zip_code'] = isset($post['zip_code']) ? $post['zip_code'] : null ;
        
        if(isset($post['address_id'])){
             // $address = Address::where('id_card', $this->id_card)->where('domain_id',$domainId)->orderBy('active','desc')->first();
            $address = Address::find($post['address_id']);
        }
        if(empty($address)){
            $address = new Address();
        }
        $address->fill($addressData)->save(); 
    }

    public function makeUserRole($title,$domainId,$update = false){
        $roles = \App\Models\Role::all()->toArray();

        $roles = array_column($roles,'name','id');
        $roleId = $this->getIdInArray($roles, $title);
        // if($roleId&&! ($this->role->contains($roleId))){
        //     $this->role()->attach([$roleId =>['created_at'=>Carbon::now(),'domain_id'=>$domainId ]]);
        // }

        if($update){
            $sql = "UPDATE role_user SET  role_id = ".$roleId." WHERE id_card = '".$this->id_card."' AND domain_id=".$domainId ;
            $query = DB::update(DB::raw($sql));
            return ;
        }

        $sql = "SELECT *
                FROM  role_user 
                WHERE id_card = ".$this->id_card." AND role_id=".$roleId." AND domain_id=".$domainId ;
        $query = DB::select(DB::raw($sql));
        if(empty($query)){
            $sql = "INSERT INTO role_user (id_card,role_id,domain_id)
                VALUES ('".$this->id_card."',".$roleId.",".$domainId.")";
            $query = DB::select(DB::raw($sql));
        }    
    }

    public function checkRoom($roomData,$domainId,$idcard){
        $sql = "SELECT r.id,ur.id_card
                FROM  rooms r
                INNER JOIN user_rooms ur ON ur.room_id = r.id 
                WHERE r.name = '".$roomData["name"]."' AND r.domain_id=".$domainId ;
        $query = collect(DB::select(DB::raw($sql)))->first(); 
        if(!empty($query)){
            if($query->id_card != $idcard)
                return ['result'=>false,'error'=>'หมายเลขห้องที่ระบุมีเจ้าของเป็นคนอื่นค่ะ'];
        }else{
            $room = new Room();
            $room->fill($roomData)->save(); 
            $this->joinRoom($room->id);
        }
        return ['result'=>true];
    }

    public static function getEditData($domainId,$idcard){
        $sql = "SELECT u.id_card,u.first_name,u.last_name,u.email,u.tel,r.name as room_name,u.remark,u.is_review,u.username,u.alert_text,CASE WHEN ud.approve=4 THEN 1 ELSE 0 END as is_ban
                ,u.nick_name
                FROM  users u 
                LEFT JOIN user_domains ud
                ON ud.id_card = u.id_card
                AND ud.domain_id = ".$domainId."
                LEFT JOIN user_rooms ur 
                ON ur.id_card = u.id_card
                LEFT JOIN rooms r 
                ON r.id = ur.room_id
                AND r.domain_id = ud.domain_id
                WHERE u.id_card = '".$idcard."'" ;
        $user = collect(DB::select(DB::raw($sql)))->first(); 
        $user = (array)$user;
        if(!empty($user)){
             $user['role'] = [];
            $sql2 = "SELECT rol.name as role_name
                FROM  role_user ru 
                LEFT JOIN roles rol 
                ON rol.id = ru.role_id
                WHERE ru.id_card = '".$idcard."' AND ru.domain_id=$domainId" ;
            $query2 = DB::select(DB::raw($sql2)); 
            if(!empty($query2)){
                foreach ($query2 as $key => $q) {
                     $user['role'][] = $q->role_name ;
                }
               
            } 
        }


        return $user; 
    }

    public static function getImage($domainId,$idcard){
        $sql = "SELECT * ,CONCAT('".url('public/upload/')."/',path,'/',image) as img
                FROM  user_images
                WHERE id_card = '".$idcard."' AND domain_id = ".$domainId ."
                ORDER BY id ASC 
                ";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }

        return $query ; 
    }



    public static function getAddress($domainId,$idcard){
        return  Address::from('user_address as ud')
        ->leftJoin('districts as d', 'ud.district_id', '=', 'd.DISTRICT_ID')
        ->leftJoin('amphures as a', 'ud.amphur_id', '=', 'a.AMPHUR_ID')
        ->leftJoin('provinces as p', 'ud.province_id', '=', 'p.PROVINCE_ID')
        ->select(DB::raw( "ud.address,ud.district_id,d.DISTRICT_NAME as district_name,ud.amphur_id,a.AMPHUR_NAME as amphur_name,ud.province_id,p.PROVINCE_NAME as province_name,ud.zip_code,ud.address_name,ud.id"))
        ->where('ud.id_card',$idcard)->where('ud.domain_id',$domainId)->orderBy('ud.active','DESC')->first();
    }

    public static function getAddressList($domainId,$idcard){
        return  Address::from('user_address as ud')
        ->leftJoin('districts as d', 'ud.district_id', '=', 'd.DISTRICT_ID')
        ->leftJoin('amphures as a', 'ud.amphur_id', '=', 'a.AMPHUR_ID')
        ->leftJoin('provinces as p', 'ud.province_id', '=', 'p.PROVINCE_ID')
        ->select(DB::raw( "ud.id,ud.address,ud.district_id,d.DISTRICT_NAME as district_name,ud.amphur_id,a.AMPHUR_NAME as amphur_name,ud.province_id,p.PROVINCE_NAME as province_name,ud.zip_code,ud.address_name,ud.active"))
        ->where('ud.id_card',$idcard)->where('ud.domain_id',$domainId)
        ->orderBy('ud.active','desc')
        ->get();
    }

    private function getIdInArray($array, $term)
     {
        foreach ($array as $key => $value) {
            if ($value == $term) {
                 return $key;
            }
        }
        return 0 ;
    }

    /**
    * @param string|array $roles
    */
    public function authorizeRoles($roles)
    {
      if (is_array($roles)) {
          return $this->hasAnyRole($roles) || 
                 abort(401, 'This action is unauthorized.');
      }
      return $this->hasRole($roles) || 
             abort(401, 'This action is unauthorized.');
    }
    /**
    * Check multiple roles
    * @param array $roles
    */
    public function hasAnyRole($roles)
    {
      return null !== $this->role()->whereIn(‘name’, $roles)->first();
    }
    /**
    * Check one role
    * @param string $role
    */
    public function hasRole($role)
    {

         $sql = "SELECT role_id 
                FROM role_user ru
                LEFT JOIN roles r 
                ON r.id = ru.role_id 
                WHERE ru.id_card = '".Auth()->user()->id_card."' AND r.name='".$role.
                "' AND ru.domain_id=".Auth()->user()->recent_domain ;
        return !empty(collect(DB::select(DB::raw($sql)))->first()) ? true : false ; 
    }
    public function getDomainName()
    {
        $query = Domain::find(Auth()->user()->recent_domain);
        return  (!empty($query)) ? $query->url_name : null ;
    }

    public function checkApprove($domainId=null){
        if(is_null($domainId)) 
            $domainId = Auth()->user()->recent_domain;

        $sql = "SELECT approve FROM user_domains 
                WHERE id_card =".Auth()->user()->id_card."
                AND domain_id =$domainId" ;
        $query = collect(DB::select(DB::raw($sql)))->first();
        if(empty($query)) {
            return false;
        } 
        return  ($query->approve==1) ? true : false ;
    }

    public function checkStatusApprove(){
        $sql = "SELECT approve FROM user_domains 
                WHERE id_card =".Auth()->user()->id_card."
                AND domain_id =".Auth()->user()->recent_domain ;
        $query = collect(DB::select(DB::raw($sql)))->first();
        return  $query->approve ;
    }

    public function getChannelJoin(){
        $userId = Auth()->user()->id ;
        $domainId = Auth()->user()->recent_domain ;

         $sql = "SELECT c.*,cu.accept,0 as unseen_count
                FROM channels c
                JOIN channel_users cu 
                ON c.id = cu.channel_id
                WHERE cu.user_id = $userId
                AND cu.accept = 1
                AND c.direct_message=0
                AND c.domain_id= $domainId" ;
        $query =  DB::select(DB::raw($sql)) ; 
        if(!empty($query)){
            foreach ($query as $key => $q) {
                $sql2 = "SELECT  cm.channel_id,count(cm.id) as unseen_count
                        FROM channel_messages cm
                        WHERE cm.id > IFNULL((SELECT max(channel_message_id) as channel_message_id  FROM channel_seen WHERE channel_id = ".$q->id." AND seen_by=$userId GROUP BY channel_id),0)
                        AND cm.channel_id = ".$q->id."
                        GROUP BY cm.channel_id" ;
                        // var_dump($sql2);die;
                $unseen = collect(DB::select(DB::raw($sql2)))->first() ;
                if(isset($unseen)){
                    $query[$key]->unseen_count = $unseen->unseen_count;
                }
            }
        }

        return $query ;
       
    }
    public function getContact($all=null){
        $userId = Auth()->user()->id ;
        $domainId = Auth()->user()->recent_domain ;
        $sql = "SELECT cu.*, CONCAT(u.first_name,' ',u.last_name) as name
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,0 as unseen_count
                FROM channel_users cu
                INNER JOIN 
                (
                    SELECT c.id FROM channels c
                    JOIN channel_users cu 
                    ON c.id = cu.channel_id
                    WHERE cu.user_id = $userId
                    AND c.direct_message=1
                    AND c.domain_id=  $domainId 
                ) cc
                ON cc.id = cu.channel_id
                INNER JOIN users u ON u.id=cu.user_id
               
                WHERE cu.user_id !=  $userId 
                AND cu.domain_id=  $domainId";
               
        if(!isset($all)){
            $sql .=  " AND cu.show_list = 1";
        }
        $query =  DB::select(DB::raw($sql)) ; 
        if(!empty($query)){
            foreach ($query as $key => $q) {
            
                $sql2 = "SELECT  cm.channel_id,count(cm.id) as unseen_count
                        FROM channel_messages cm
                        WHERE cm.id > IFNULL((SELECT max(channel_message_id) as channel_message_id  FROM channel_seen WHERE channel_id = ".$q->channel_id." AND seen_by=$userId GROUP BY channel_id),0)
                        AND cm.channel_id = ".$q->channel_id."
                        GROUP BY cm.channel_id" ;
                $unseen = collect(DB::select(DB::raw($sql2)))->first() ;
                if(isset($unseen)){
                    $query[$key]->unseen_count = $unseen->unseen_count;
                }
            }
        }
        return $query;
    }
    public function getChannel(){
        $sql = "SELECT c.*
                    ,IFNULL(cu.accept, 0) accept  
                FROM channels c
                LEFT JOIN 
                   ( SELECT * FROM  channel_users 
                     WHERE user_id = ".Auth()->user()->id." 
                     AND domain_id= ".Auth()->user()->recent_domain." ) cu
                ON c.id = cu.channel_id
                WHERE c.type!=3 
                AND c.domain_id= ".Auth()->user()->recent_domain."
                AND c.direct_message=0
                UNION 
                SELECT c.*,cu.accept FROM channels c
                JOIN channel_users cu 
                ON c.id = cu.channel_id
                WHERE cu.user_id = ".Auth()->user()->id."
                 AND cu.accept = 1
                 AND c.type = 3
                 AND c.direct_message=0
                 AND c.domain_id= ".Auth()->user()->recent_domain ;
        return DB::select(DB::raw($sql)) ; 
    }

    public function getRoom(){
      
        $sql = "  SELECT r.* FROM rooms r
                    INNER JOIN user_rooms ur
                    ON r.id = ur.room_id
                    WHERE ur.id_card = '".Auth()->user()->id_card."'
                    AND r.domain_id=".Auth()->user()->recent_domain."
                    AND ur.approve=1
                    ";
        return DB::select(DB::raw($sql)) ; 

    }

    public function getProfileImg(){
        $user = Auth()->user() ;
        $fbImg = $user->profile_url  ;
        $avartarId = $user->avartar_id  ;
        if(isset($fbImg)&&$avartarId==0) {
            $img = $fbImg ;
        }else{
            $img = url('')."/public/img/profile/". $avartarId.".png" ;
        }
        return $img ;
    }

    public function homeUrl(){
        return url(Auth()->user()->recent_domain.'/dashboard');
    }

    public function approveSendNoti($idCard){
        $sql = "select u.id_card,ud.noti_player_id 
                from users as u 
                on tv.user_id = u.id 
                inner join user_domains as ud 
                on ud.id_card = u.id_card 
                and ud.domain_id = tv.domain_id 
                and ud.approve = 1
                where u.id_card = $idCard";
        $query = DB::select(DB::raw($sql));
        if(!empty($query)){

            if(isset($query->noti_player_id)){
                $parsedBody['user_id_list'][] = $query->noti_player_id ;
            }
            if(isset($query->noti_player_id_mobile)){
                $parsedBody['user_id_list'][] = $query->noti_player_id_mobile ;
            }
            if(isset($parsedBody['user_id_list'])){
                $parsedBody['direct'] = true;
                $parsedBody['message'] = (App::isLocale('en')) ? 'Admin activated your account' : 'แอดมินยืนยันรหัสผู้ใช้ของคุณแล้ว สามารถใช้งานระบบได้แล้วค่ะ' ;
                $sendNoti = Notification::sendNoti($parsedBody);
            }
        }
    }

    public static function userAddRoom($post,$idCard){
        $room = (gettype($post['user-room'])=="string") ?  (array)json_decode($post['user-room']) : $post['user-room'] ;
        RoomUser::where('id_card',$idCard)->delete();
        $userData = [] ;

        foreach ($room as $key => $u) {
            if(is_array($u)){
                $roomId = $u['room_id'];
                $idCard = $u['id_card'];
                $roomApprove = isset($u['room_approve']) ? $u['room_approve'] : 0 ;
            }else{
                $roomId = $u->room_id ;
                $idCard = $u->id_card ;
                $roomApprove = isset($u->room_approve) ? $u->room_approve : 0 ;
            }

            $userRoom['room_id'] = $roomId ;
            $userRoom['id_card'] = $idCard ; 
            $userRoom['approve'] = $roomApprove ;
            $userRoom['approved_at'] = null ;
            $userRoom['approved_by'] = null ;
            if($roomApprove==1&&Auth()->user()->hasRole('admin') ){
                $userRoom['approved_at'] = Carbon::now();
                $userRoom['approved_by'] = Auth()->user()->id ;
            }
          
            $userData[] = $userRoom ;
        }
        RoomUser::insert($userData);
    }

    public function getNotification($idCard=null){
        if(is_null($idCard)){
            $idCard = auth()->user()->id_card ;
        }

        $sql = "SELECT id,message,type,status,ref_id,created_at FROM 
                notifications 
                WHERE domain_id = ".auth()->user()->recent_domain." 
                AND id_card = '".$idCard."' " ;
        $mainQuery = " AND type >1 ";
        $hasAdmin = Auth()->user()->hasRole('admin');
        $hasOfficer = Auth()->user()->hasRole('officer');
        $hasHeaduser = Auth()->user()->hasRole('head.user');
        if($hasAdmin||$hasOfficer||$hasHeaduser){
            $mainQuery = " AND type != 0 ";
        }

        $sql .= $mainQuery ;


        if($hasAdmin){
            $sql .= " UNION ALL
                SELECT id,message,type,status,ref_id,created_at FROM 
                notifications 
                WHERE domain_id = ".auth()->user()->recent_domain." 
                AND type = 0 
                AND role_id =1";
        }
        $sql .= " ORDER BY id DESC LIMIT 10" ;
        $notification   =  DB::select(DB::raw($sql));


        // $notification = Notification::where('id_card',$idCard)
        // ->where('domain_id',auth()->user()->recent_domain)
        // ->select(DB::raw('message,type,status'))
        // ->orderBy('id','desc')
        // ->limit(10)
        // ->get();
        $sqlCount = "";
        if($hasAdmin){ 
            $sqlCount .= "SELECT SUM(cnt) as cnt FROM ( " ;
        }
        $sqlCount .= "SELECT count(*) as cnt FROM 
                notifications 
                WHERE domain_id = ".auth()->user()->recent_domain." 
                AND id_card = '".$idCard."'
                AND seen = 0 
                AND type != 0 " ;
        if($hasAdmin){
            $sqlCount .= " UNION
                SELECT count(*) as cnt FROM 
                notifications 
                WHERE domain_id = ".auth()->user()->recent_domain." 
                AND type = 0  AND seen = 0  )  x";
        }
       
        $queryCount   =  DB::select(DB::raw($sqlCount));
        $count = (!empty($queryCount)) ? $queryCount[0]->cnt : 0 ; 

        // $count = Notification::where('id_card',$idCard)
        // ->where('domain_id',auth()->user()->recent_domain)
        // ->where('seen',0)
        // ->count(); 

        $data =  new \stdClass();
        $data->notification = $notification ;
        $data->total = $count ;
        return $data;
    }

    public static function active(){

        $user =  Auth()->user();
        if(isset($user)){
            User::find($user->id)->update(['onlined_at'=>Carbon::now()]) ;
        }
       
    }


}
