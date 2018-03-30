<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Room;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class LogActivityController extends Controller
{
    private $view = 'admin.log_activity';
    private $title ;
    private $route = 'log-activity';
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('auth');
        $this->title = (App::isLocale('en')) ? "Agent" : "เจ้าหน้าที่" ;
    }
    public function index($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $route = $domainId."/".$this->route ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route.'?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(), true);

        if (!isset($json['result'])) {
             $json['errors'] = $response->getBody()->getContents() ;
               return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']=="false") {
            return redirect('error')
                ->withError($json['errors']);
        }
        $users = $json['response']['user'] ;
      
        return view($this->view.'.index', compact('users', 'title', 'route', 'domainId', 'domainName'));
    }

    public function create($domainId)
    {
    }

   

    public function edit($domainId, $idcard)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $route = $domainId."/".$this->route;
        $edit = true;
        $route = url('').'/api/'.$domainId.'/create-admin/'.$idcard.'?api_token='.Auth()->User()->api_token ;
        $routePath = $this->route ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/create-admin/'.$idcard.'/edit?api_token='.Auth()->User()->api_token ;
        $res = $client->get($url);
        $json = json_decode($res->getBody()->getContents(), true);
        if (!isset($json['result'])) {
                $json['errors'] = $res->getBody()->getContents() ;
                return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']=="false") {
            return redirect('error')
                ->withError($json['errors']);
        }
        $data = $json['response']['user'] ;
        
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/master/role?api_token='.Auth()->User()->api_token ;
        $res = $client->get($url);
        $json = json_decode($res->getBody()->getContents(), true);
        if (!isset($json['result'])) {
                $json['errors'] = $response->getBody()->getContents() ;
                return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']=="false") {
            return redirect('error')
                ->withError($json['errors']);
        }
        $roles = $json['response']['roles'] ;
      
        unset($roles[2]);
        unset($roles[3]);

      

       
        return view($this->view.'.create', compact('title', 'route', 'roles', 'domainId', 'defaultRole', 'data', 'edit', 'routePath', 'domainName'));
    }

    public function update(Request $request, $domainId, $idcard)
    {
    }
}
