<?php

namespace App\Http\Middleware;

use App;
use App\Models\Domain;
use Closure;
use Config;
use Cookie;
use Session;

class Locale
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
       
       

       

      
        // var_dump($_COOKIE['rm_locale']);die;
        // var_dump(Cookie::get('rm_locale'));die;

        if(!isset($_COOKIE['rm_locale'])) {
            setcookie('rm_locale', Config::get('app.locale'), time() + (86400 * 30), "/");
            App::setLocale(Config::get('app.locale'));
        }
        // $cookie = $_COOKIE['rm_locale'] ;

        App::setLocale(Cookie::get('rm_locale'));

       

        $response = $next($request);

       

       

        $user = Auth()->user();
        if(isset($user)){
            $user->active();
        }
      
       

       

        // dd($request->route()->parameter('domain'));
        // return $response->withCookie(cookie()->forever('rm_locale',  $cookie));
        return  $response;
    }
}
