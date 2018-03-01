<?php
namespace App\Http\Middleware;

use App;
use App\Models\Domain;
use Closure;
use Config;
use Cookie;
use Session;

class DomainMiddelware
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
    
       if($request->route()->uri!="error"&&$request->route()->uri!="logout"){
            $domain = $request->route()->parameter('domain') ;
            $query = Domain::where('url_name',$domain)->first();
            $lang = App::isLocale('en');
            $error = ($lang) ?'Not found this domain' : 'คุณไม่สามารถเข้าหน้านี้ได้ กรุณาติดต่อเจ้าหน้าที่';
            if(empty($query)){
                return redirect('/error')->withErrors([$error]);
            }

            $check = Auth()->user()->checkApprove($query->id);
            if(!$check){
                return redirect('/error')->withErrors([$error]);
            }
            
            // $request->route()->setParameter('domain',  $query->id);
        }
        $response = $next($request);

        return  $response;
    }
}