<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Amphur;
use App\Models\District;
use App\Models\Domain;
use App\Models\Images;
use App\Models\Notification;
use App\Models\Province;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\UserTemp;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class CreateUserController extends ApiController
{
    private $view = 'admin.create_user';
    private $title = 'โครงการ';
    private $route = 'create-user';
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

    //use AuthenticatesUsers;

   

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('auth');
    }


    public function index($domainId)
    {

        $isSystemAdmin = Auth()->user()->hasRole('system.admin');
        
        $subQuery = "" ;
        if (!$isSystemAdmin) {
            $subQuery = " AND  u.id_card not in ( SELECT id_card FROM role_user WHERE role_id = 7  )";
        }

        try {
            $sql = "SELECT * FROM (
                        SELECT  u.username,u.id_card,u.first_name,u.last_name,u.created_at,r.display_name as role_name , ud.approve , 1 as registor
                        FROM domains d
                        INNER JOIN user_domains ud ON ud.domain_id = d.id
                        INNER JOIN users u on u.id_card = ud.id_card
                        LEFT JOIN role_user ur ON ur.id_card = u.id_card AND ur.domain_id = d.id 
                        LEFT JOIN roles r ON ur.role_id = r.id 

                        WHERE d.id = $domainId  $subQuery
                        UNION ALL
                        SELECT  '-' as username,u.id_card,u.first_name,u.last_name,u.created_at,r.display_name as role_name , ud.approve , 0 as registor
                         FROM domains d
                        INNER JOIN user_domains ud ON ud.domain_id = d.id
                        INNER JOIN user_temps u on u.id_card = ud.id_card AND u.domain_id = d.id
                        LEFT JOIN role_user ur ON ur.id_card = u.id_card AND ur.domain_id = d.id
                        LEFT JOIN roles r ON ur.role_id = r.id 
                        WHERE d.id = $domainId  AND u.id_card not in ( 
                            SELECT u2.id_card FROM users u2
                            INNER JOIN user_domains ud2  ON u2.id_card = ud2.id_card
                            WHERE ud2.domain_id = $domainId
                        ) $subQuery
                    ) a
                    ORDER BY created_at DESC
                    " ;
            $query = DB::select(DB::raw($sql));
            $user = [];
            if (!empty($query)) {
                foreach ($query as $q) {
                    $user[$q->id_card]['username'] = $q->username;
                    $user[$q->id_card]['id_card'] = $q->id_card;
                    $user[$q->id_card]['first_name'] = $q->first_name;
                    $user[$q->id_card]['last_name'] = $q->last_name;
                    $user[$q->id_card]['created_at'] = $q->created_at;
                    $user[$q->id_card]['role'][] = $q->role_name;
                    $user[$q->id_card]['approve'] = $q->approve;
                    $user[$q->id_card]['registor'] = $q->registor;
                }
            }

          
            $data['user'] = array_values($user);
            $data['totaluser'] = User::join('user_domains as ud', 'ud.id_card', '=', 'users.id_card')->where('ud.domain_id', $domainId)->count() ;
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
       
        return $this->respondWithItem($data);
    }

    public function init($domainId)
    {
        $data['roles'] = Role::where('id', '<>', 7);

        $data['districts'] = District::all();
        $data['amphurs'] = Amphur::all();
        $data['provinces'] = Province::all();

        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');

        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $post['file-type'] = (gettype($post['file-type'])=="string") ?  (array)json_decode($post['file-type']) : $post['file-type'] ;
        $repeat = UserTemp::where('id_card', $post['id_card'])->where('domain_id', $domainId)->first();
        if (!empty($repeat)) {
             return $this->respondWithError("The id card has already been taken.");
        }

        // foreach ($post['role'] as $key => $role) {
        //     if($role=="user"||$role=="head.user"){
        //         $validator =  $this->validatorRoom($post);
        //         if ($validator->fails()) {
        //             return $this->respondWithError($validator->errors());
        //         }
        //     }
        // }

        if (isset($post['role'])) {
            foreach ($post['role'] as $key => $role) {
                if ($role=="user"&&empty($post['user-room'])) {
                    return $this->respondWithError(((getLang()=='th') ? 'กรุณาระบุหมายเลขห้อง' : 'Please insert room'));
                }
            }
        }

        $uploadImg = Images::uploadImage($request, $domainId);
        if (!$uploadImg['result']) {
            return $this->respondWithError($uploadImg['error']);
        }
        if (isset($uploadImg)&&isset($uploadImg['file'])) {
            if (is_array($uploadImg['file'])) {
                foreach ($uploadImg['file'] as $key => $v) {
                    $img['id_card']  =  $idcard  ;
                    $img['domain_id']  =  $domainId ;
                    $img['path'] = $v['filePath'];
                    $img['image'] = $v['fileName'];
                    $img['file_name'] = $v['fileDisplayName'];
                    $img['file_code'] = $v['fileID'];
                    $img['file_extension'] = $v['fileExtension'];
                    $img['type'] = $post['file-type'][$key];
                    $img['created_at'] = Carbon::now();
                    Images::create($img);
                }
            }
        }




        $post['api_token'] =  md5(time().str_random(50));
        $post['password'] = bcrypt(str_random(5));
        $post['username'] = $post["id_card"];

        // $roomData["name"] = $post["room"];
        // $roomData["name"] = 555;
        // $roomData['domain_id'] = $domainId;
        // $roomData['id_card'] = $post["id_card"];

        // $room = Room::where('name',$roomData["name"])->where('domain_id',$domainId)->first();
        // if(!empty($room)){
        //     return $this->respondWithError('ห้องหมายเลขนี้ มีเจ้าของแล้วค่ะ');
        // }
        // $room = new Room();
        // $room->fill($roomData)->save();

        if (isset($post['user-room'])) {
            User::userAddRoom($post, $post["id_card"]);
        }


        $user = new UserTemp();
        $post['domain_id'] = $domainId;

        $user->fill($post)->save();

        $approve = 0 ;
        if ($post['approve']=="true") {
            $approve = 5 ;       //--- wait for active link
        }
        $user->joinDomain($domainId, $approve);
       

        $statusApprove = false;
        foreach ($post['role'] as $key => $role) {
            $user->makeUserRole($role, $domainId);
            if ($role=="admin"||$role=="officer") {
                $statusApprove = true;
            }
        }
        
        if ($statusApprove&&$post['approve']=="true") {
            $user->joinDomain($domainId, 1);
            $user->approveSendNoti($post['id_card'], $domainId);
        }


        // $user->joinRoom($room->id);
        if (isset($post['address'])) {
            $user->insertAddress($domainId, $post);
        }


        
        $data['user'] = $post;
        return $this->respondWithItem($post);
    }

    public function edit($domainId, $idcard)
    {


        $query = User::getEditData($domainId, $idcard);
        // $user = User::where("id_card",$idcard)->first();
        if (empty($query)) {
            $query = UserTemp::getEditData($domainId, $idcard);
        }

        if (empty($query)) {
            return $this->respondWithError('not found this user');
        }

       

        $data['user'] = $query ;

        $sql = "SELECT approve FROM user_domains 
                WHERE id_card ='$idcard'
                AND domain_id =$domainId" ;
        $query = collect(DB::select(DB::raw($sql)))->first();

        $data['user_approve'] = $query->approve ;
        $data['room_user'] = RoomUser::from('user_rooms as ru')
                    ->join('rooms as r', 'r.id', '=', 'ru.room_id')
                    ->where('ru.id_card', $idcard)
                    ->select(DB::raw("ru.*,CONCAT( IFNULL(r.name_prefix,''),IFNULL(r.name,''),IFNULL(r.name_surfix,'') ) as text_name"))
                    ->get();
        $data['address'] = User::getAddress($domainId, $idcard);
        $data['docs'] = User::getImage($domainId, $idcard);
        return $this->respondWithItem($data);
    }

    public function update(Request $request, $domainId, $idcard)
    {
        $post = $request->except('api_token', '_method');

        unset($post['api_token']);

        $post['file-type'] = (gettype($post['file-type'])=="string") ?  (array)json_decode($post['file-type']) : $post['file-type'] ;

        $validator = $this->validator($post, $idcard);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        if (isset($post['role'])) {
            foreach ($post['role'] as $key => $role) {
                if ($role=="user"&&empty($post['user-room'])) {
                    return $this->respondWithError(((getLang()=='th') ? 'กรุณาระบุหมายเลขห้อง' : 'Please insert room'));
                }
            }
        }
      



        // foreach ($post['role'] as $key => $role) {
        //     if($role=="user"||$role=="head.user"){
        //         $validator =  $this->validatorRoom($post);
        //         if ($validator->fails()) {
        //             return $this->respondWithError($validator->errors());
        //         }
        //     }
        // }
       

        $owner = User::where('id_card', $idcard)->first() ;
        if (empty($owner)) {
                return $this->respondWithError(((getLang()=='th') ? 'ไม่พบข้อมูลผู้ใช้' : 'not found this user'));
        }

        //--- check id_card Other real User
        $otherUser = User::where('id_card', $post['id_card'])->where('id', '<>', $owner->id)->first() ;
        if (!empty($otherUser)) {
            return $this->respondWithError(((getLang()=='th') ? 'รหัสบัตรประชาชนซ้ำกับคนอื่นในระบบ' : 'id card has already exists'));
        }

        

        $uploadImg = Images::uploadImage($request, $domainId);
        if (!$uploadImg['result']) {
            return $this->respondWithError($uploadImg['error']);
        }
        if (isset($uploadImg)&&isset($uploadImg['file'])) {
            if (is_array($uploadImg['file'])) {
                foreach ($uploadImg['file'] as $key => $v) {
                    $img['id_card']  =  $idcard  ;
                    $img['domain_id']  =  $domainId ;
                    $img['path'] = $v['filePath'];
                    $img['image'] = $v['fileName'];
                    $img['file_name'] = $v['fileDisplayName'];
                    $img['file_code'] = $v['fileID'];
                    $img['file_extension'] = $v['fileExtension'];
                    $img['type'] = $post['file-type'][$key];
                    $img['created_at'] = Carbon::now();
                    Images::create($img);
                }
            }
        }

       
        $user = User::where('id_card', $idcard)->first();
        $registor = true ;
        $tempTable = false;
        if (empty($user)) {
            $registor  = false ;
            $tempTable = true;
            $user = UserTemp::where('id_card', $idcard)->first();
            if (empty($user)) {
                 return $this->respondWithError(((getLang()=='th') ? 'ไม่พบข้อมูลผู้ใช้' : 'Not found this user'));
            }
        }

        // var_dump($user);die;


        if ($user->id_card!=$post['id_card']) {
            // echo "change ID_CARD" ;
            $cn = DB::connection('mysql');
            $cn->beginTransaction();

            $has1= $cn->select($cn->raw("SELECT * FROM notifications WHERE id_card ='".$user->id_card."'"));
            $saved1 = true;
            if (!empty($has1)) {
                $saved1 = $cn->update($cn->raw("UPDATE notifications SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
            }

            $has2= $cn->select($cn->raw("SELECT * FROM role_user WHERE id_card ='".$user->id_card."'"));
            $saved2 = true;
            if (!empty($has2)) {
                $saved2 = $cn->update($cn->raw("UPDATE role_user SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
            }


            $has3= $cn->select($cn->raw("SELECT * FROM user_address WHERE id_card ='".$user->id_card."'"));
            $saved3 = true;
            if (!empty($has3)) {
                $saved3 = $cn->update($cn->raw("UPDATE user_address SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
            }



            $has4= $cn->select($cn->raw("SELECT * FROM user_auto_actives WHERE id_card ='".$user->id_card."'"));
            $saved4 = true;
            if (!empty($has4)) {
                $saved4 = $cn->update($cn->raw("UPDATE user_auto_actives SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
            }



            $has5= $cn->select($cn->raw("SELECT * FROM user_domains WHERE id_card ='".$user->id_card."'"));
            $saved5 = true;
            if (!empty($has5)) {
                $saved5 = $cn->update($cn->raw("UPDATE user_domains SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
            }


            $has6= $cn->select($cn->raw("SELECT * FROM user_images WHERE id_card ='".$user->id_card."'"));
            $saved6 = true;
            if (!empty($has6)) {
                $saved6 = $cn->update($cn->raw("UPDATE user_images SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
            }


            $has7= $cn->select($cn->raw("SELECT * FROM user_rooms WHERE id_card ='".$user->id_card."'"));
            $saved7 = true;
            if (!empty($has7)) {
                $saved7 = $cn->update($cn->raw("UPDATE user_rooms SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
            }



            $has8= $cn->select($cn->raw("SELECT * FROM  role_user WHERE id_card ='".$user->id_card."'"));
            $saved8 = true;
            if (!empty($has8)) {
                $saved8 = $cn->update($cn->raw("UPDATE role_user SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
            }


        
            if ($tempTable) {
                $has8= $cn->select($cn->raw("SELECT * FROM  user_temps WHERE id_card ='".$user->id_card."'"));
                $saved8 = true;
                if (!empty($has8)) {
                    $saved8 = $cn->update($cn->raw("UPDATE user_temps SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
                }
            } else {
                $has8= $cn->select($cn->raw("SELECT * FROM  users WHERE id_card ='".$user->id_card."'"));
                $saved8 = true;
                if (!empty($has8)) {
                    $saved8 = $cn->update($cn->raw("UPDATE users SET id_card = '".$post['id_card']."' WHERE id_card ='".$user->id_card."'"));
                }
            }

            // echo "save1:";var_dump($saved1);
            // echo "<BR>save2:"; var_dump($saved2);
            // echo "<BR>save3:"; var_dump($saved3);
            // echo "<BR>save4:"; var_dump($saved4);
            // echo "<BR>save5:"; var_dump($saved5);
            // echo "<BR>save6:"; var_dump($saved6);
            // echo "<BR>save7:"; var_dump($saved7);
            // echo "<BR>save8:"; var_dump($saved8);
           

            
            if (!$saved1||!$saved2||!$saved3||!$saved4||!$saved5||!$saved6||!$saved7||!$saved8) {
                $cn->rollBack();
                return $this->respondWithError(((getLang()=='th') ? 'เกิดความผิดพลาดในการแก้ไขข้อมูล' : 'transaction update error'));
            } else {
                $cn->commit();
            }

            // $cn->commit();
        }
       
        $user->fill($post)->save();

        if ($post['approve']=="true") {
            $user->joinDomain($domainId, 1);
            $notiMsg = "You id activate successed" ;
            $notiStatus = 3 ;
            $user->approveSendNoti($post['id_card'], $domainId);
        }

      

        //--- ถ้า Admin update ข้อมูล สถานะเป็น Review
        $sql = "SELECT *
                FROM  user_domains 
                WHERE id_card = '".$post['id_card']."' AND domain_id=".$domainId ;
        $userDomain = collect(DB::select(DB::raw($sql)))->first();
        if ($userDomain->approve!=1) {
            $notiMsg = "Admin reviewed your information" ;
            $notiStatus = 2 ;
            $user->joinDomain($domainId, 2);
        }
        if (isset($post['is_ban'])&&$post['is_ban']=="on"&&$userDomain->approve!=4) {
            $user->joinDomain($domainId, 4);
            $notiMsg = "You id were baned by Admin" ;
            $notiStatus = 1 ;
        } elseif ($userDomain->approve==4&&!isset($post['is_ban'])) {
            $user->joinDomain($domainId, 1);
            $notiMsg = "You id were unbaned by Admin" ;
            $notiStatus = 3 ;
        }

        // $roomData["name"] = $post["room"];
        // $roomData["name"] = 555;
        // $roomData['domain_id'] = $domainId;
        // $roomData['id_card'] = $post["id_card"];
        if (isset($post['user-room'])) {
            User::userAddRoom($post, $post['id_card']);
        }

        // $chkRoom =  $user->checkRoom($roomData,$domainId,$idcard) ;
        // if(!$chkRoom['result']){
        //     return $this->respondWithError($chkRoom['error']);
        // }
        
        $sql = "DELETE FROM  role_user WHERE id_card = '".$idcard."' AND domain_id=".$domainId ;
            $query = DB::DELETE(DB::raw($sql));
        if (isset($post['role'])) {
            $statusApprove = false;
            foreach ($post['role'] as $key => $role) {
                $user->makeUserRole($role, $domainId);
                if ($role=="admin"||$role=="officer") {
                    $statusApprove = true;
                }
            }
            if ($statusApprove&&$post['approve']=="true") {
                $user->joinDomain($domainId, 1);
            }
        }
        


        

        $user->insertAddress($domainId, $post);

        if ($registor&&isset($notiMsg)) {
            $notification = new Notification();
            $notificationData['domain_id'] =  $domainId;
            $notificationData['id_card'] =  $post["id_card"];
            $notificationData['message'] =  $notiMsg;
            $notificationData['status'] =  $notiStatus;
            $notificationData['ref_id'] =  $post["id_card"];
            $notificationData['type'] =  1;
            $notification->fill($notificationData)->save();

            if (isset($userDomain->noti_player_id)||isset($userDomain->noti_player_id_mobile)) {
                $parsedBody['direct'] = true;
                if (isset($userDomain->noti_player_id)) {
                    $parsedBody['user_id_list'][] = $userDomain->noti_player_id ;
                }
                if (isset($userDomain->noti_player_id_mobile)) {
                    $parsedBody['user_id_list'][] = $userDomain->noti_player_id_mobile ;
                }
                $parsedBody['message'] = $notiMsg ;
                Notification::sendNoti($parsedBody);
            }
        }

        


        $data['user'] = $post;
        return $this->respondWithItem($data);
    }

    public function destroy(Request $request, $domainId, $idcard)
    {
        $post = $request->except('api_token', '_method');
      
        if (!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError("คุณไม่มีสิทธิ์ใช้เงื่อนไขนี้ค่ะ");
        }
      
        

        $sql = "SELECT * FROM user_domains WHERE id_card='$idcard' AND approve = 1 LIMIT 1";
        $query = DB::select(DB::raw($sql));
        if (!empty($query)) {
            return $this->respondWithError($this->langMessage('ไม่สามารถลบข้อมูลผู้ใช้นี้ได้', 'cannot delete this user'));
        }


        $delete = " DELETE FROM user_address WHERE id_card='$idcard';
                    DELETE FROM user_auto_actives WHERE id_card='$idcard';
                    DELETE FROM user_domains WHERE id_card='$idcard';
                    DELETE FROM user_history_email WHERE id_card='$idcard';
                    DELETE FROM user_images WHERE id_card='$idcard';
                    DELETE FROM user_rooms WHERE id_card='$idcard';
                    DELETE FROM user_temps WHERE id_card='$idcard';
                    DELETE FROM users WHERE id_card='$idcard'; ";

        DB::delete(DB::raw($delete));
        return $this->respondWithItem($post);
    }

    public function approve(Request $request, $domainId, $idcard)
    {
        $post = $request->except('api_token', '_method');
      
        if (!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError("คุณไม่มีสิทธิ์ใช้เงื่อนไขนี้ค่ะ");
        }
      
        DB::update('UPDATE user_domains SET approve = ? ,approved_at = ?  WHERE id_card = ? AND domain_id = ? ', [1,Carbon::now(),$idcard,$domainId]);
        return $this->respondWithItem($post);
    }

    public function roomApprove(Request $request, $domainId, $idcard)
    {
        $post = $request->except('api_token', '_method');
        unset($post['api_token']);
        if (!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError("คุณไม่มีสิทธิ์ใช้เงื่อนไขนี้ค่ะ");
        }


        $post['approved_at'] = null ;
        $post['approved_by'] = null ;
        if ($post['approve']==1) {
            $post['approved_at'] = Carbon::now() ;
            $post['approved_by'] = Auth()->user()->id ;
        }

        $room = RoomUser::where('id_card', $idcard)
        ->where('room_id', $post['room_id']) ;
        if (empty($room->first())) {
             $post['id_card'] = $idcard;
            RoomUser::create($post);
        } else {
            $room->update($post);
        }
        return $this->respondWithItem($post);
    }

    public function waitApproveUpdate(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $idcard = auth()->user()->id_card ;
        unset($post['api_token']);

        $validator = $this->validator($post, $idcard);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $uploadImg = Images::uploadImage($request, $domainId);
        if (!$uploadImg['result']) {
            return $this->respondWithError($uploadImg['error']);
        }
        if (isset($uploadImg)&&isset($uploadImg['file'])) {
            if (is_array($uploadImg['file'])) {
                foreach ($uploadImg['file'] as $key => $v) {
                    $img['id_card']  =  $idcard  ;
                    $img['domain_id']  =  $domainId ;
                    $img['path'] = $v['filePath'];
                    $img['image'] = $v['fileName'];
                    $img['file_name'] = $v['fileDisplayName'];
                    $img['file_code'] = $v['fileID'];
                    $img['file_extension'] = $v['fileExtension'];
                    $img['type'] = $post['file-type'][$key];
                    $img['created_at'] = Carbon::now();
                    Images::create($img);
                }
            }
        }
        $user = User::where('id_card', $idcard)->first();
        if (empty($user)) {
            $user = UserTemp::where('id_card', $idcard)->first();
        }
        $user->fill($post)->save();

        if (isset($post['user-room'])) {
            User::userAddRoom($post, $idcard);
        }


        $user->insertAddress($domainId, $post);
        $data['user'] = $post;
        return $this->respondWithItem($data);
    }

   

    private function validator($data)
    {
        return Validator::make($data, [
           'id_card' => 'required|string|max:13',
           'first_name' => 'required|string|max:255',
           'last_name' => 'required|string|max:255',
           'email' => 'required|string|email|max:255',
            
        ]);
    }

    private function validatorUniq($data)
    {

        return Validator::make($data, [
            'id_card' => 'required|string|max:13|unique:user_temps',
        ]);
    }
    private function validatorRoom($data)
    {
        return Validator::make($data, [
            'room' => 'required|string|max:255'
        ]);
    }
}
