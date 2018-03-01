<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Domain;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ValidateController extends ApiController
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
    }

    public function username(Request $request){
        $data = $request->input('username');
        $query = User::where('username',$data)->first();
        echo (empty($query)) ? "true" : "false" ;

    }
    public function idcard(Request $request){
        $data = $request->input('id_card');
        $query = User::where('id_card',$data)->first();
        echo (empty($query)) ? "true" : "false" ;
        
    } 
    public function domainName(Request $request){
        $data = $request->input('name');
        $query = Domain::where('name',$data)->first();
        echo (empty($query)) ? "true" : "false" ;
        
    }
}
