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
        App::setLocale(Cookie::get('rm_locale'));
        if (!isset($_COOKIE['rm_locale'])) {
            setcookie('rm_locale', 'th', time() + (86400 * 30), "/");
            App::setLocale('th');
        }

        $response = $next($request);

        $user = Auth()->user();
        if (isset($user)) {
            $user->active();
        }
        // dd($request->route()->parameter('domain'));
        // return $response->withCookie(cookie()->forever('rm_locale',  $cookie));
        return  $response;
    }
}
