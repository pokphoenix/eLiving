<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\LogActivity;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoginController extends ApiController
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/domain';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    protected function guard()
    {
      return Auth::guard('api');
    }

    public function signin(Request $request){
        $post = $request->all();
        $validator = $this->validator($post);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $active = 0 ;
        if(isset($post['remember'])){
            $active = 1 ;
        }

        if (!Auth::attempt(['username' => $post['username'], 'password' => $post['password'] ],$active)) {
            return $this->respondWithError('username or password invalid');
        }
       

        LogActivity::SetLogActivity(1);

        return $this->respondWithItem(Auth()->user());
    } 

    public function facebookSignin(Request $request){
        $post = $request->all();
        $active = 0 ;
        if(isset($post['remember'])){
            $active = 1 ;
        }

        $sql = "SELECT id
                FROM users u
                WHERE facebook_id = ".$post['fb'];
        $user = DB::select(DB::raw($sql));
        if(!$user){
            return $this->respondWithError('not found');
        }
        Auth::loginUsingId($user[0]->id);
        return $this->respondWithItem(Auth()->user());
    }

     private function validator($data)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:5',
        ]);
    }
}
