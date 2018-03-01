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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
class LogActivityController extends ApiController
{
    private $view = 'admin.log_activity';
    private $title = '';
    private $route = 'log-activity';
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
    }


    public function index($domainId){


        try {
            $sql = "SELECT
                        u.username,u.id_card,u.first_name,u.last_name,u.created_at
                        ,CASE WHEN la.activity = 1 THEN 'login' ELSE 'logout' END as activity_text
                        ,la.created_at
                    FROM log_activity la
                    INNER JOIN users u on u.id = la.user_id
                    WHERE la.domain_id = $domainId 
                    ORDER BY la.created_at DESC
                    " ;
            $query = DB::select(DB::raw($sql));
            $data['user'] =  $query ;
           
        }catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
       
        return $this->respondWithItem($data);
    }

    public function init($domainId){
        $data['roles'] = Role::all();

        $data['districts'] = District::all();
        $data['amphurs'] = Amphur::all();
        $data['provinces'] = Province::all();

        return $this->respondWithItem($data);
    }

    public function store(Request $request,$domainId){
        $post = $request->all();

        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }


        $post['api_token'] =  md5(time().str_random(50));
        $post['password'] = bcrypt($post['password']);
        
        $user = new User();
        $user->recent_domain = 1 ;
        $user->fill($post)->save(); 

       
        $user->joinDomain($domainId,1);
       
        foreach ($post['role'] as $key => $role) {
            $user->makeUserRole($role,$domainId);
           
        }

        $data['user'] = $post;
        return $this->respondWithItem($post);
    }

    public function edit($domainId,$idcard){


        $query = User::getEditData($domainId,$idcard);
        // $user = User::where("id_card",$idcard)->first();
        if(empty($query)){
            $query = UserTemp::getEditData($domainId,$idcard);
        }

        if(empty($query)){
            return $this->respondWithError('not found this user');
        }

       

        $data['user'] = $query ;

        $sql = "SELECT approve FROM user_domains 
                WHERE id_card =$idcard
                AND domain_id =$domainId" ;
        $query = collect(DB::select(DB::raw($sql)))->first();

        $data['user_approve'] = $query->approve ;
        $data['room_user'] = RoomUser::from('user_rooms as ru')
                    ->join('rooms as r','r.id','=','ru.room_id')  
                    ->where('ru.id_card',$idcard)
                    ->select(DB::raw( "ru.*,CONCAT( IFNULL(r.name_prefix,''),IFNULL(r.name,''),IFNULL(r.name_surfix,'') ) as text_name" ))
                    ->get();
        $data['address'] = User::getAddress($domainId,$idcard);
        $data['docs'] = User::getImage($domainId,$idcard);
        return $this->respondWithItem($data);
    }

    public function update(Request $request,$domainId,$idcard){
        $post = $request->all();
        unset($post['api_token']);

      
        $validator = $this->validatorEdit($post,$idcard);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        
      

        $user = User::where('id_card',$idcard)->first();
        $user->fill($post)->save();


        //--- ถ้า Admin update ข้อมูล สถานะเป็น Review
        $sql = "SELECT *
                FROM  user_domains 
                WHERE id_card = $idcard AND domain_id=".$domainId ;
        $userDomain = collect(DB::select(DB::raw($sql)))->first();
        if($userDomain->approve!=1){
            $notiMsg = "Admin reviewed your information" ;
            $notiStatus = 2 ;
            $user->joinDomain($domainId,2);
        }
        if(isset($post['is_ban'])&&$post['is_ban']=="on"&&$userDomain->approve!=4){
            $user->joinDomain($domainId,4);
            $notiMsg = "You id were baned by Admin" ;
            $notiStatus = 1 ;
        }elseif($userDomain->approve==4&&!isset($post['is_ban'])){
            $user->joinDomain($domainId,1);
            $notiMsg = "You id were unbaned by Admin" ;
            $notiStatus = 3 ;
        }

        $sql = "DELETE FROM  role_user WHERE id_card = '".$post["id_card"]."' AND domain_id=".$domainId ;
            $query = DB::DELETE(DB::raw($sql));
        if(isset($post['role'])){
            foreach ($post['role'] as $key => $role) {
                $user->makeUserRole($role,$domainId);
            }
            $user->joinDomain($domainId,1);
        }
        $data['user'] = $post;
        return $this->respondWithItem($data);
    }

    

   

    private function validator($data)
    {
        return Validator::make($data, [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'tel' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:5|max:40|confirmed',
            'id_card' => 'required|string|min:13|max:13|unique:users'
        ]);
    }

    private function validatorEdit($data)
    {
        return Validator::make($data, [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'tel' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255',
            'username' => 'required|string|max:255',
            'id_card' => 'required|string|min:13|max:13'
        ]);
    }
    

}
