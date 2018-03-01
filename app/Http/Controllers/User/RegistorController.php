<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\UserActive;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Route;
class RegistorController extends Controller
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
    protected $redirectTo = '/home';

    public function __construct()
    {
    }

    public function signup(Request $request){
        
        $post = $request->all();
        $request = Request::create('/api/signup', 'POST', ['data' => 'data_to_be_checked']);
        $response = Route::dispatch($request);
        $json = json_decode($response->getContent(),true); 
        if(!isset($json['result'])){
                $json['errors'] = $response->getContent() ;
                 return redirect()->back()
                ->withError($json['errors'])->withInput();
            }
        if($json['result']=="false")
        {
            return redirect()->back()
                ->withError($json['errors'])->withInput();
        }

       

        return redirect('1/dashboard');
    }

    public function facebook(){
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/province' ;
        $res =  $client->post($url,  ['form_params'=>['name'=>'']] );
        $json = json_decode($res->getBody()->getContents(),true); 
        $province = $json ;
        $domainId = 1 ;
        return view('front.facebook',compact('province','domainId'));
    }

    public function facebookSignUp(Request $request){
        $post = $request->all();
        $request = Request::create('/api/facebook_signup', 'POST', ['data' => 'data_to_be_checked']);
        $response = Route::dispatch($request);
        $json = json_decode($response->getContent(),true); 
        if(!isset($json['result'])){
                $json['errors'] = $response->getContent() ;
                 return redirect()->back()
                ->withError($json['errors'])->withInput();
            }
        if($json['result']=="false")
        {
            return redirect()->back()
                ->withError($json['errors'])->withInput();
        }
         return redirect('domain');
    }

    public function resetPassword(Request $request){
        $post = $request->all();
        $token = $post['token'] ;
        $sql = "SELECT *
                FROM password_resets
                WHERE (UNIX_TIMESTAMP(UTC_TIMESTAMP() ) BETWEEN UNIX_TIMESTAMP(created_at) AND (UNIX_TIMESTAMP(created_at)+(30*60))) AND token = '".$token."' AND active=0";
        $data = DB::select(DB::raw($sql));

        
        return view('auth.passwords.reset',compact('token','data'));
    } 
    public function activeCode(Request $request){
        $post = $request->all();
        $token = $post['token'] ;
        $sql = "SELECT *, CASE WHEN (UNIX_TIMESTAMP(NOW()) BETWEEN UNIX_TIMESTAMP(created_at) AND (UNIX_TIMESTAMP(created_at)+(30*60))) THEN 0
                ELSE 1 END as is_expire 
                FROM user_auto_actives
                WHERE  token = '".$token."'
                LIMIT 1";
        $data = collect(DB::select(DB::raw($sql)))->first();
        if(empty($data)){
            return redirect('notfound')->withError('token wrong');
        }

        if($data->is_expire){
            //--- token expire delete user
            UserActive::find($data->id)->delete();
            User::where('id_card',$data->id_card)->delete();
            return redirect('notfound')->withError('token expire please signup again');
        }

        $user = User::where('id_card',$data->id_card)->first();
        if(empty($user)){
             return redirect('notfound')->withError('not found this user');
        }
        Auth::loginUsingId($user->id, TRUE);
        //--- approve all domain
        DB::update("UPDATE user_domains SET approve=?,approved_at=? WHERE  id_card=? ", [1,Carbon::now(),$data->id_card]);
        //-- set token active
        UserActive::find($data->id)->delete();
        // echo "success activate" ;die;
        return redirect('1/dashboard');
        // return view('auth.passwords.reset',compact('token','data'));
    }
}
