<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Admin\PreWelcome;
use App\Models\Notification;
use App\Models\UserTemp;
use App\Models\User\PasswordReset;
use App\Models\User\UserActive;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegistorController extends ApiController
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

    //use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function signup(Request $request)
    {
        $post = $request->all();

        $validator = $this->validator($post);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $post['api_token'] =  md5(time().str_random(50));
        $post['password'] = bcrypt($post['password']);
        $post['avartar_id'] = rand(0,19) ;
      
        // $domain['name'] = $post['domain'];
        // $domain['residence_name'] = $post['residence_name'];
        // $domain['company_name'] = $post['company_name'];
        // $domain['unit'] = $post['unit'];

        // $role['name'] ='admin';
        $regisByAdmin = UserTemp::where('id_card',$post['id_card'])->first();

        if(!empty($regisByAdmin)){ 
            $post['first_name'] =  $regisByAdmin->first_name ;
            $post['last_name'] =  $regisByAdmin->last_name ;
            $post['email'] =  $regisByAdmin->email ;
            $post['tel'] =  $regisByAdmin->tel ;
        }

        $post['displayname'] = $post['first_name']." ".$post['last_name'];

        $check = User::where('displayname',$post['displayname'])->first();
        if(!empty($check)){
            return $this->respondWithError('displayname has already exits');
        }

        $user = new User();
        $user->fill($post)->save(); 
        // $user->syncData();

       

        //--- auto Domain 1 not approve when admin not create
        if(empty($regisByAdmin)){
            $user->joinDomain(1,0);
        }else{
            $sql = "SELECT role_id 
                FROM role_user ru
                LEFT JOIN roles r 
                ON r.id = ru.role_id 
                WHERE ru.id_card = '".$post['id_card']."' AND (r.name='officer' OR r.name='admin') 
                AND ru.domain_id=1" ;
            $query = DB::select(DB::raw($sql));
            if(empty($query)){
                //--- send activate mail to 
                $activeToken = md5(HASH_ACTIVE_MAIL.time().str_random(5));
                
                UserActive::where('id_card',$post['id_card'])->delete();

                $userActive =  new UserActive;
                $userActive->token =  $activeToken ;
                $userActive->email =  $post['email'];
                $userActive->id_card =  $post['id_card'];
                $userActive->created_at =  Carbon::now();
                $userActive->save();


                if(url('')!="http://localhost/laravel/rm"){
                     $data = [
                        'email'=>$post['email']
                        ,'link'=> url("active?token=".$activeToken)
                    ];
                    try {
                        Mail::send('mail.active_users',$data,function($message) use ($post){
                            $message->to($post['email'])
                                    ->subject('welcome to join - eLiving');
                                    
                        });
                    } catch(\Exception $e) {
                        return $this->respondWithError($e->getMessage());
                    }
                    if(count(Mail::failures()) > 0){
                        return $this->respondWithError('Failed to contact, please try again.');
                    }
                }

               
           }
        }

        $user->recent_domain = 1 ;
        $user->save();


        Auth::loginUsingId($user->id, TRUE);
        if(!$post['no_room']){
            $user->makeUserRole("user",1);
            if(isset($post['user-room'])){
                User::userAddRoom($post,$post['id_card']);
            }
        }
       
        $user->insertAddress(1,$post);

        $notiMsg = "New user registered";
        $notiStatus = 3;
        $notification = new Notification();
        $notificationData['domain_id'] =  1;
        $notificationData['id_card'] =  null;
        $notificationData['message'] =  $notiMsg;
        $notificationData['status'] =  $notiStatus;
        $notificationData['type'] =  0;
        $notificationData['role_id'] =  1;
        $notificationData['ref_id'] =  $post['id_card'];
        $notification->fill($notificationData)->save(); 
        $sql = "SELECT ud.noti_player_id FROM 
                role_user ru
                INNER JOIN user_domains ud
                ON ud.id_card = ru.id_card
                AND ud.domain_id = ru.domain_id
                AND ud.approve = 1
                WHERE ru.role_id = 1 and ru.domain_id = 1" ;
        $userDomain   =  DB::select(DB::raw($sql));
        $parsedBody = [];
        if(!empty($userDomain)){
            $parsedBody['direct'] = true;
            $parsedBody['message'] = $notiMsg ;
            foreach ($userDomain as $key => $u) {
                if(isset($u)){
                    $parsedBody['user_id_list'][] = $u->noti_player_id ;
                }
            }
        }
        if (isset($parsedBody['user_id_list'])){
            Notification::sendNoti($parsedBody);
        }
        // $domains = new Domain();
        // $domains->fill($domain)->save(); 
        // $user->domain()->sync([$domains->id =>['created_at'=>Carbon::now()]]);
        // $user->makeUserRole('admin');
        $user->prewelcome = PreWelcome::find(1);
        return $this->respondWithItem($user);
    }

    public function facebookSignUp(Request $request)
    {
        $post = $request->all();
        $validator = $this->validatorFacebook($post);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $token_url="https://graph.facebook.com/me?access_token=".$post['facebook_token'] ;
        $response = file_get_contents($token_url);
        $json = json_decode($response,true);
        if (!isset($json['id'])){
            return $this->respondWithError($json['error']);
        }
        if($json['id']!=$post['facebook_id']){
            return $this->respondWithError('invalid facebook please logout facebook');
        }
        
        $post['api_token'] =  md5(time().str_random(50));
        $post['password'] = bcrypt(str_random(5).$post['facebook_id']);
        $post['username'] = md5("fb".$post['facebook_id'].HASH_PROJECT.$post['id_card']) ;
        $post['displayname'] = $post['first_name']." ".$post['last_name'];
        // $domain['name'] = $post['domain'];
        // $domain['residence_name'] = $post['residence_name'];
        // $domain['company_name'] = $post['company_name'];
        // $domain['unit'] = $post['unit'];

        // $role['name'] ='admin';

        // $user = UserTemp::where('id_card',$post['id_card'])->first();
        $regisByAdmin = UserTemp::where('id_card',$post['id_card'])->first();

    
       
        $user = new User();
        $user->fill($post)->save(); 
        $user->syncData();

        //--- auto Domain 1 not approve when admin not create
        if(empty($regisByAdmin)){
            $user->joinDomain(1,0);
        }
       
        $user->recent_domain = 1 ;
        $user->save();

       
        Auth::loginUsingId($user->id, TRUE);
        if(!$post['no_room']){
            $user->makeUserRole("user",1);
            if(isset($post['user-room'])){
                User::userAddRoom($post,$post['id_card']);
            }
        }
       
        $user->insertAddress(1,$post);
        // $domains = new Domain();
        // $domains->fill($domain)->save(); 
        // $user->domain()->sync([$domains->id =>['created_at'=>Carbon::now()]]);
        // $user->makeUserRole('admin');

        $notiMsg = "New user registered";
        $notiStatus = 3;
        $notification = new Notification();
        $notificationData['domain_id'] =  1;
        $notificationData['id_card'] =  null;
        $notificationData['message'] =  $notiMsg;
        $notificationData['status'] =  $notiStatus;
        $notificationData['type'] =  0;
        $notificationData['role_id'] = 1;
        $notificationData['ref_id'] = $post['id_card'];
        $notification->fill($notificationData)->save(); 
        $sql = "SELECT ud.noti_player_id FROM 
                role_user ru
                INNER JOIN user_domains ud
                ON ud.id_card = ru.id_card
                AND ud.domain_id = ru.domain_id
                AND ud.approve = 1
                WHERE ru.role_id = 1 and ru.domain_id = 1" ;
        $userDomain   =  DB::select(DB::raw($sql));
        $parsedBody = [];
        if(!empty($userDomain)){
            $parsedBody['direct'] = true;
            $parsedBody['message'] = $notiMsg ;
            foreach ($userDomain as $key => $u) {
                if(isset($u)){
                    $parsedBody['user_id_list'][] = $u->noti_player_id ;
                }
            }
        }
        if (isset($parsedBody['user_id_list'])){
            Notification::sendNoti($parsedBody);
        }
       

        Auth::loginUsingId($user->id, TRUE);
        return $this->respondWithItem($user);
    }

    public function reset(Request $request){
        $post = $request->all();
        $validator = $this->validatorPassword($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $token = $post['token'];
        $sql = "SELECT *
                FROM password_resets
                WHERE (UNIX_TIMESTAMP(UTC_TIMESTAMP() ) BETWEEN UNIX_TIMESTAMP(created_at)
                AND (UNIX_TIMESTAMP(created_at)+(30*60))) AND token = '".$token."'
                AND email = '".$post['email']."' ORDER BY created_at DESC";
        $reset = DB::select(DB::raw($sql));
        if(empty($reset)){
            return $this->respondWithError('Token expire or wrong email');
        }
        if($reset[0]->active==1){
             return $this->respondWithError('This token used');
        }

        PasswordReset::where('email',$post['email'])->where('token',$token)->update(['active' => 1]);

        $save['password'] = bcrypt($post['password']);
        $user = User::where('email',$post['email'])->first();
        $userId= $user->id ;
        $user->update($save);
        return $this->respondWithItem($user);
    }


    public function resetPass(Request $request){
        $post = $request->all();
        $validator = $this->validatorEmail($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $user = User::where('email',$post['email'])->first();
        if(empty($user)){
            return $this->respondWithError("We can't find a user with that e-mail address.");
        }

        $resetToken = bcrypt($user->id.str_random(10)) ;
        $reset = new PasswordReset ;
        $reset->email = $post['email'];
        $reset->token = $resetToken ;
        $reset->created_at = Carbon::now();
        $reset->save();

        $data = [
            'email'=>$post['email']
            ,'link'=> url("resetpassword?token=".$resetToken)
        ];
        try {
            Mail::send('mail.reset_password_users',$data,function($message) use ($post){
                $message->to($post['email'])
                        ->subject('reset password - eLiving');
                        
            });
        } catch(\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
        if(count(Mail::failures()) > 0){
            return $this->respondWithError('Failed to contact, please try again.');
        }
        
        return $this->respondWithItem($reset);
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
            'id_card' => 'required|string|min:13|max:13|unique:users',

            // 'job_title' => 'required|string|max:255',
            // 'residence_name' => 'required|string|max:255',
            // 'domain' => 'required|string|max:255|unique:domains,name',
            // 'company_name' => 'required|string|max:255',
            // 'unit' => 'required|numeric',
            'agree' => 'required',
        ]);
    }
    private function validatorFacebook($data)
    {
        return Validator::make($data, [
           'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'facebook_id' => 'required|string|max:255|unique:users,facebook_id',
            'profile_url' => 'required|string|max:255',
            'id_card' => 'required|string|min:13|max:13|unique:users',
            'agree' => 'required',
        ]);
    }
     private function validatorEmail($data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255',
        ]);
    }
    private function validatorPassword($data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:5|max:40|confirmed',
        ]);
    }
}
