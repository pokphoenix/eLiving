<?php
namespace App\Http\Middleware;

use App;
use App\Models\Domain;
use App\Models\Setting;
use Closure;
use Config;
use Cookie;
use Session;

class ApiServer
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
        // return response()->json(['result'=>'false','errors' =>'กรุณาอัพเดทโปรแกรมใหม่'], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        $query = Setting::where('keys', 'SERVER_STATUS')->where('status', 1)->first();
        $return = null ;
        if (!empty($query)) {
            $return = $query->values;
        }
        if (empty($return)) {
            $text = App::isLocale('en') ? 'server maintanance' : 'ปิดปรับปรุงระบบชั่วคราว' ;
            return response()->json(['result'=>'false','errors' =>  $text], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }

        $response = $next($request);
        return  $response;
    }
}
