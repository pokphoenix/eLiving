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
class CreateAdminController extends Controller
{
    private $view = 'admin.create_admin';
    private $title ;
    private $route = 'create-admin';
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
    public function index($domainId){
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $route = $domainName."/".$this->route ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId."/".$this->route.'?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 

        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }
        $users = $json['response']['user'] ;
        $totaluser = $json['response']['totaluser'] ;
        return view($this->view.'.index',compact('users','title','route','domainId','domainName','totaluser'));
    }

    public function create($domainId){
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;
        $title = $this->title ;
        $route = url('').'/api/'.$domainId.'/create-admin?api_token='.Auth()->User()->api_token ;
        $routePath = $this->route ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/master/role?api_token='.Auth()->User()->api_token ;
        $res = $client->get($url);
        $json = json_decode($res->getBody()->getContents(),true); 
        if(!isset($json['result'])){
                $json['errors'] = $response->getBody()->getContents() ;
                return redirect()->back()
                ->withError($json['errors']);
            }
        if($json['result']=="false")
        {
            return redirect()->back()
                ->withError($json['errors']);
        }
       
        $roles = $json['response']['roles'] ;
        unset($roles[2]);
        unset($roles[3]);
      

        $defaultRole = "admin" ;
        return view($this->view.'.create',compact('title','route','roles','domainId','domainName','defaultRole','routePath'));
    }

   

    public function edit($domainId,$idcard){
         $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;
        $title = $this->title ;
        $route = $domainId."/".$this->route;
        $edit = true;
        $route = url('').'/api/'.$domainId.'/create-admin/'.$idcard.'?api_token='.Auth()->User()->api_token ;
        $routePath = $this->route ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/create-admin/'.$idcard.'/edit?api_token='.Auth()->User()->api_token ;
        $res = $client->get($url);
        $json = json_decode($res->getBody()->getContents(),true); 
        if(!isset($json['result'])){
                $json['errors'] = $res->getBody()->getContents() ;
                return redirect()->back()
                ->withError($json['errors']);
            }
        if($json['result']=="false")
        {
            return redirect()->back()
                ->withError($json['errors']);
        }
        $data = $json['response']['user'] ;
        
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/master/role?api_token='.Auth()->User()->api_token ;
        $res = $client->get($url);
        $json = json_decode($res->getBody()->getContents(),true); 
        if(!isset($json['result'])){
                $json['errors'] = $response->getBody()->getContents() ;
                return redirect()->back()
                ->withError($json['errors']);
            }
        if($json['result']=="false")
        {
            return redirect()->back()
                ->withError($json['errors']);
        }
        $roles = $json['response']['roles'] ;
      
        unset($roles[2]);
        unset($roles[3]);

      

       
        return view($this->view.'.create',compact('title','route','roles','domainId','domainName','defaultRole','data','edit','routePath'));
    }

    public function update(Request $request,$domainId,$idcard){
         $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/create-user/'.$idcard.'?api_token='.Auth()->User()->api_token ;
        $post = $request->all();
        $post['_method'] = "PUT" ;
        $response = $client->POST($url,  ['form_params'=>$post] );
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
                $json['errors'] = $response->getBody()->getContents() ;
                 return redirect()->back()
                ->withError($json['errors']);
            }
        if($json['result']=="false")
        {
            return redirect()->back()
                ->withError($json['errors']);
        }

       
        return redirect($domainId."/".$this->route)->with('success','อัพเดทข้อมูล สำเร็จ');
    }

    public function approve(Request $request,$domainId,$idcard){
         $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/create-user/'.$idcard.'/approve?api_token='.Auth()->User()->api_token ;
        $res = $client->get($url);
        $json = json_decode($res->getBody()->getContents(),true); 

        if(!isset($json['result'])){
                $json['errors'] = $response->getBody()->getContents() ;
                 return redirect()->back()
                ->withError($json['errors']);
            }
        if($json['result']=="false")
        {
            return redirect()->back()
                ->withError($json['errors']);
        }

       
        return redirect($domainId."/".$this->route)->with('success','อัพเดทข้อมูล สำเร็จ');
    }

    

}
