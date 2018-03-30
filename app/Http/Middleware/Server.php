<?php
namespace App\Http\Middleware;

use App;
use App\Models\Domain;
use App\Models\Setting;
use Closure;
use Config;
use Cookie;
use Session;

class Server
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
        $query = Setting::where('keys', 'SERVER_STATUS')->where('status', 1)->first();
        $return = null ;
        if (!empty($query)) {
            $return = $query->values;
        }
        if (empty($return)) {
            return redirect('/maintanance');
        }

        $response = $next($request);
        return  $response;
    }
}
