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
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        // $domainId = $post['domain_id'] ;
        $domainId = 1 ;
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $post['api_token'] =  md5(time().str_random(50));
        $post['password'] = bcrypt($post['password']);
        $post['avartar_id'] = rand(0, 19) ;
      

        if (!isset($post['id_card'])) {
            $uniq = md5(uniqid(rand(), true)) ;
            $post['id_card'] = "tmp".substr($uniq, 0, 10);
        }
        if (empty($post['id_card'])) {
            $uniq = md5(uniqid(rand(), true)) ;
            $post['id_card'] = "tmp".substr($uniq, 0, 10);
        }

       



        // $domain['name'] = $post['domain'];
        // $domain['residence_name'] = $post['residence_name'];
        // $domain['company_name'] = $post['company_name'];
        // $domain['unit'] = $post['unit'];

        // $role['name'] ='admin';
        $regisByAdmin = UserTemp::where('id_card', $post['id_card'])->first();

        if (!empty($regisByAdmin)) {
            $post['first_name'] =  $regisByAdmin->first_name ;
            $post['last_name'] =  $regisByAdmin->last_name ;
            $post['email'] =  $regisByAdmin->email ;
            $post['tel'] =  $regisByAdmin->tel ;
        }

        $post['displayname'] = $post['first_name']." ".$post['last_name'];

        $check = User::where('displayname', $post['displayname'])->first();
        if (!empty($check)) {
            return $this->respondWithError('displayname has already exits');
        }

        $user = new User();
        $user->fill($post)->save();
        // $user->syncData();

       

        //--- auto Domain 1 not approve when admin not create
        if (empty($regisByAdmin)) {
            $user->joinDomain($domainId, 0);
        } else {
            UserTemp::where('id_card', $post['id_card'])->delete();
            $sql = "SELECT role_id 
                FROM role_user ru
                LEFT JOIN roles r 
                ON r.id = ru.role_id 
                WHERE ru.id_card = '".$post['id_card']."' AND (r.name='officer' OR r.name='admin') 
                AND ru.domain_id=$domainId" ;
            $query = DB::select(DB::raw($sql));
            if (empty($query)) {
                //--- send activate mail to
                $activeToken = md5(HASH_ACTIVE_MAIL.time().str_random(5));
                
                UserActive::where('id_card', $post['id_card'])->delete();

                $userActive =  new UserActive;
                $userActive->token =  $activeToken ;
                $userActive->email =  $post['email'];
                $userActive->id_card =  $post['id_card'];
                $userActive->created_at =  Carbon::now();
                $userActive->save();


                if (url('')!="http://localhost/laravel/rm") {
                     $data = [
                        'name'=> $user->first_name." ".$user->last_name
                        ,'username'=> $userName
                        ,'email'=> $email
                        ,'create_date'=> date('d/m/Y')
                        ,'link'=> url("active?token=".$resetToken)
                     ];

                     try {
                         Mail::send('mail.active_users', $data, function ($message) use ($post) {
                            $message->to($post['email'])
                                    ->subject('welcome to join - eLiving');
                         });
                     } catch (\Exception $e) {
                            return $this->respondWithError($e->getMessage());
                        }
                        if (count(Mail::failures()) > 0) {
                            return $this->respondWithError('Failed to contact, please try again.');
                        }
                }
            }
        }

        $user->recent_domain = $domainId ;
        $user->save();


        Auth::loginUsingId($user->id, true);
        if (!$post['no_room']) {
            $user->makeUserRole("user", $domainId);
            if (isset($post['user-room'])) {
                User::userAddRoom($post, $post['id_card']);
            }
        }
       
        $user->insertAddress($domainId, $post);
        $notiMsg =  (getLang()=='en') ? "New user registered" : "ผู้ใช้สมัครไอดีใหม่" ;
        $notiStatus = 3;
        $notification = new Notification();
        $notificationData['domain_id'] =  $domainId;
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
                WHERE ru.role_id = 1 and ru.domain_id = $domainId" ;
        $userDomain   =  DB::select(DB::raw($sql));
        $parsedBody = [];
        if (!empty($userDomain)) {
            $parsedBody['direct'] = true;
            $parsedBody['message'] = $notiMsg ;
            foreach ($userDomain as $key => $u) {
                if (isset($u)) {
                    $parsedBody['user_id_list'][] = $u->noti_player_id ;
                }
            }
        }
        if (isset($parsedBody['user_id_list'])) {
            Notification::sendNoti($parsedBody);
        }
        // $domains = new Domain();
        // $domains->fill($domain)->save();
        // $user->domain()->sync([$domains->id =>['created_at'=>Carbon::now()]]);
        // $user->makeUserRole('admin');
        $preWelcome = PreWelcome::where('domain_id', $domainId)->first();
        if (empty($preWelcome)) {
            $preWelcome = new \stdClass ;
            $preWelcome->text =  (getLang()=='en') ? "Welcome to eLiving" : "ยินดีต้อนรับ" ;
        }

         $user->prewelcome = $preWelcome ;

        return $this->respondWithItem($user);
    }

    public function facebookSignUp(Request $request)
    {
        $post = $request->except('api_token', '_method');
        $validator = $this->validatorFacebook($post);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $token_url="https://graph.facebook.com/me?access_token=".$post['facebook_token'] ;
        $response = file_get_contents($token_url);
        $json = json_decode($response, true);
        if (!isset($json['id'])) {
            return $this->respondWithError($json['error']);
        }
        if ($json['id']!=$post['facebook_id']) {
            return $this->respondWithError('invalid facebook please logout facebook');
        }

        if (!isset($post['id_card'])) {
            $uniq = md5(uniqid(rand(), true)) ;
            $post['id_card'] = "tmp".substr($uniq, 0, 10);
        }
        if (empty($post['id_card'])) {
            $uniq = md5(uniqid(rand(), true)) ;
            $post['id_card'] = "tmp".substr($uniq, 0, 10);
        }




        $domainId = 1 ;
        
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
        $regisByAdmin = UserTemp::where('id_card', $post['id_card'])->first();

    
       
        $user = new User();
        $user->fill($post)->save();
        $user->syncData();

        //--- auto Domain 1 not approve when admin not create
        if (empty($regisByAdmin)) {
            $user->joinDomain($domainId, 0);
        } else {
            UserTemp::where('id_card', $post['id_card'])->delete();
        }
       
        $user->recent_domain = $domainId ;
        $user->save();

       
        Auth::loginUsingId($user->id, true);
        if (!$post['no_room']) {
            $user->makeUserRole("user", $domainId);
            if (isset($post['user-room'])) {
                User::userAddRoom($post, $post['id_card']);
            }
        }
       
        $user->insertAddress($domainId, $post);
        // $domains = new Domain();
        // $domains->fill($domain)->save();
        // $user->domain()->sync([$domains->id =>['created_at'=>Carbon::now()]]);
        // $user->makeUserRole('admin');

        $notiMsg =  (getLang()=='en') ? "New user registered" : "ผู้ใช้สมัครไอดีใหม่" ;
        $notiStatus = 3;
        $notification = new Notification();
        $notificationData['domain_id'] =  $domainId;
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
                WHERE ru.role_id = 1 and ru.domain_id = $domainId" ;
        $userDomain   =  DB::select(DB::raw($sql));
        $parsedBody = [];
        if (!empty($userDomain)) {
            $parsedBody['direct'] = true;
            $parsedBody['message'] = $notiMsg ;
            foreach ($userDomain as $key => $u) {
                if (isset($u)) {
                    $parsedBody['user_id_list'][] = $u->noti_player_id ;
                }
            }
        }
        if (isset($parsedBody['user_id_list'])) {
            Notification::sendNoti($parsedBody);
        }
       

        Auth::loginUsingId($user->id, true);
        return $this->respondWithItem($user);
    }

    public function reset(Request $request)
    {
        $post = $request->except('api_token', '_method');
        $validator = $this->validatorPassword($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $token = $post['token'];
        $sql = "SELECT *
                FROM password_resets
                WHERE (UNIX_TIMESTAMP() BETWEEN UNIX_TIMESTAMP(created_at)
                AND (UNIX_TIMESTAMP(created_at)+(30*60))) AND token = '".$token."'
                AND username = '".$post['username']."' ORDER BY created_at DESC";
        $reset = DB::select(DB::raw($sql));
        if (empty($reset)) {
            return $this->respondWithError($this->langMessage('ชื่อผู้ใช้ไม่ถูกต้อง หรือ โทเคนหมดอายุ', 'Token expire or wrong username'));
        }
        if ($reset[0]->active==1) {
             return $this->respondWithError($this->langMessage('โทเคนนี้เคยถูกใช้ไปแล้ว', 'This token used'));
        }

        PasswordReset::where('username', $post['username'])->where('token', $token)->update(['active' => 1]);

        $save['password'] = bcrypt($post['password']);
        $user = User::where('username', $post['username'])->first();
        $userId= $user->id ;
        $user->update($save);
        return $this->respondWithItem($user);
    }


    public function resetPass(Request $request)
    {
        $post = $request->except('api_token', '_method');
       
        $validator = $this->validatorReset($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $user = User::where('id_card', $post['id_card'])->first();
        if (empty($user)) {
            return $this->respondWithError($this->langMessage("ไม่พบรหัสประชาชนที่ระบุ", "We can't find a user with that id card."));
        }

        $email = $user->email;

        list($mailName,$mailServer) = explode('@', $email) ;

        $mailServer = explode('.', $mailServer) ;

        $mailExtension = "";
        foreach ($mailServer as $key => $m) {
            if ($key>0) {
                $mailExtension .=".$m";
            }
        }


        $hashEmail = substr($mailName, 0, 2);
        $hashEmail = str_pad($hashEmail, mb_strlen($mailName), 'x', STR_PAD_RIGHT);

        $hashEmailServer = substr($mailServer[0], 0, 2);
        $hashEmailServer = str_pad($hashEmailServer, mb_strlen($mailServer[0]), 'x', STR_PAD_RIGHT);

        $returnEmail = $hashEmail."@".$hashEmailServer.$mailExtension;


        $userName = $user->username;

        $resetToken = bcrypt($user->id.str_random(10)) ;
        $reset = new PasswordReset ;
        $reset->username = $userName ;
        $reset->token = $resetToken ;
        $reset->created_at = Carbon::now();
        $reset->save();

        $data = [
            'name'=> $user->first_name." ".$user->last_name
            ,'username'=> $userName
            ,'email'=> $email
            ,'create_date'=> date('d/m/Y')
            ,'link'=> url("resetpassword?token=".$resetToken)
        ];
        try {
            Mail::send('mail.reset_password_users', $data, function ($message) use ($email) {
                $message->to($email)
                        ->subject('reset password - eLiving');
            });
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
        if (count(Mail::failures()) > 0) {
            return $this->respondWithError('Failed to contact, please try again.');
        }
        $reset->return_email = $returnEmail;
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
            // 'id_card' => 'required|string|max:13|unique:users',
            // 'domain_id' => 'required|numeric',

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
            'id_card' => 'required|string|max:13|unique:users',
            'agree' => 'required',
        ]);
    }
    private function validatorReset($data)
    {
        return Validator::make($data, [
           'id_card' => 'required|string|max:13',
        ]);
    }
    private function validatorPassword($data)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:5|max:40|confirmed',
        ]);
    }
}
