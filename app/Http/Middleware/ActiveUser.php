<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Session;
use Config;
use Cookie;
use Auth;
class ActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        dd("test");
        return $next($request);
    }
}
