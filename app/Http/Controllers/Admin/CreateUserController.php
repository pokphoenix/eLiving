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

class CreateUserController extends Controller
{
    private $view = 'admin.create_user';
    private $title = 'User / ผู้ใช้งาน';
    private $route = 'create-user';
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
        $this->title = App::isLocale('en') ? "User" : "ผู้ใช้งาน" ;
    }
    public function index($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $title = $this->title ;
        $route = $domainName."/".$this->route ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId."/".$this->route.'?api_token='.Auth()->User()->api_token ;
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
        $totaluser = $json['response']['totaluser'] ;
        return view($this->view.'.index', compact('users', 'title', 'route', 'domainId', 'domainName', 'totaluser'));
    }

    public function create($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $title = $this->title ;
        $route = url('').'/api/'.$domainId.'/create-user?api_token='.Auth()->User()->api_token ;
        $routePath = $this->route ;
        // $client = new \GuzzleHttp\Client();
        // $url = url('').'/api/'.$domainId.'/create-user/init?api_token='.Auth()->User()->api_token ;

        // $response = $client->get($url);
        // $json = json_decode($response->getBody()->getContents(),true);
        // if(!isset($json['result'])){
        //         $json['errors'] = $response->getBody()->getContents() ;
        //         return redirect()->back()
        //         ->withError($json['errors']);
        //     }
        // if($json['result']=="false")
        // {
        //     return redirect()->back()
        //         ->withError($json['errors']);
        // }
       

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
    
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/province?api_token='.Auth()->User()->api_token ;
        $res =  $client->post($url, ['form_params'=>['name'=>'']]);
        $json = json_decode($res->getBody()->getContents(), true);
        $province = $json ;
      

        $defaultRole = "user" ;
        return view($this->view.'.create', compact('title', 'route', 'roles', 'domainId', 'domainName', 'defaultRole', 'routePath', 'province'));
    }

    public function store(Request $request, $domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/create-user?api_token='.Auth()->User()->api_token ;

        $data = $request->all() ;
        // var_dump($request->all());die;

        $response = $client->post($url, ['form_params'=>$data]);

   

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

       
        return redirect($domainId."/".$this->route)->with('success', 'สร้างผู้ใช้ สำเร็จ');
    }

    public function edit($domainId, $idcard)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $title = $this->title ;
        $route = $domainId."/".$this->route;
        $edit = true;
        $route = url('').'/api/'.$domainId.'/create-user/'.$idcard.'?api_token='.Auth()->User()->api_token ;
        $routePath = $this->route ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/create-user/'.$idcard.'/edit?api_token='.Auth()->User()->api_token ;
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
        $isApprove = $json['response']['user_approve'] ;
        $roomUser = $json['response']['room_user'] ;
        $address = $json['response']['address'] ;

        $docs = $json['response']['docs'] ;


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
      
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/province?api_token='.Auth()->User()->api_token ;
        $res =  $client->post($url, ['form_params'=>['name'=>'']]);
        $json = json_decode($res->getBody()->getContents(), true);
        $province = $json ;

        $amphur = [];
        if (isset($address['province_id'])) {
            $client = new \GuzzleHttp\Client();
            $url = url('').'/api/search/amphur-id?api_token='.Auth()->User()->api_token ;
            $res =  $client->post($url, ['form_params'=>['id'=>$address['province_id']]]);
            $json = json_decode($res->getBody()->getContents(), true);
            $amphur = $json['response']['amphurs'] ;
        }

        $district = [];
        if (isset($address['province_id'])) {
            $client = new \GuzzleHttp\Client();
            $url = url('').'/api/search/district-id?api_token='.Auth()->User()->api_token ;
            $res =  $client->post($url, ['form_params'=>['id'=>$address['amphur_id']]]);
            $json = json_decode($res->getBody()->getContents(), true);
            $district = $json['response']['districts'] ;
        }

       
        return view($this->view.'.create', compact('title', 'route', 'roles', 'domainId', 'domainName', 'defaultRole', 'data', 'edit', 'address', 'docs', 'routePath', 'roomUser', 'isApprove', 'province', 'amphur', 'district'));
    }

    public function update(Request $request, $domainId, $idcard)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/create-user/'.$idcard.'?api_token='.Auth()->User()->api_token ;
        $post = $request->all();
        $post['_method'] = "PUT" ;
        $response = $client->POST($url, ['form_params'=>$post]);
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

       
        return redirect($domainId."/".$this->route)->with('success', 'อัพเดทข้อมูล สำเร็จ');
    }

    public function approve(Request $request, $domainId, $idcard)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/create-user/'.$idcard.'/approve?api_token='.Auth()->User()->api_token ;
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

       
        return redirect($domainId."/".$this->route)->with('success', 'อัพเดทข้อมูล สำเร็จ');
    }
}
