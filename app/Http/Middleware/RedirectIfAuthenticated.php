<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
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

        return $next($request);
    }
}
