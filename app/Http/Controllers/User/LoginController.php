<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class LoginController extends Controller
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
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function show()
    {
        return view('auth.login');
    }
    public function signin(Request $request)
    {
        $post = $request->all();
        $request = Request::create('/api/signin', 'POST', ['data' => 'data_to_be_checked']);
        $response = Route::dispatch($request);
        $json = json_decode($response->getContent(), true);
        if (!isset($json['result'])) {
                $json['errors'] = $response->getContent() ;
                 return redirect()->back()
                ->withError($json['errors'])->withInput();
        }
        if ($json['result']=="false") {
            return redirect()->back()
                ->withError($json['errors'])->withInput();
        }

       
        return redirect('domain');
    }
    public function facebookSignIn(Request $request)
    {
        $post = $request->all();
        $request = Request::create('/api/facebook_signin', 'POST', ['data' => 'data_to_be_checked']);
        $response = Route::dispatch($request);
        $json = json_decode($response->getContent(), true);
        if (!isset($json['result'])) {
                $json['errors'] = $response->getContent() ;
                 return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']=="false") {
            if ($json['errors']=="not found") {
                 return redirect('signup_facebook');
            }

            return redirect('error')
                ->withError($json['errors']);
        }

       
        return redirect('domain');
    }
}
