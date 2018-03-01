<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {


       

        
        if (Auth::guard($guard)->check()) {
            return redirect('/domain');
        }else{
            if($guard=='api'){
                $response = ['result'=>false,'errors'=>'Unauthorized.'];
                return response($response);
            }
           
        }

       
       
    }
}
