<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller ;
use DB;
use Route;
use stdClass ;
use Auth;

class ProfileController extends Controller
{
    private $route = 'profile' ;
    private $title = 'Profile' ;
    private $view = 'user.profile' ;

    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function changepass()
    {
        $apiUpdate = url('api/profile/changepass?api_token=').auth()->user()->api_token ;
        return view($this->view.'.changepass', compact('apiUpdate'));
    }
    public function username()
    {
        $apiUpdate = url('api/profile/username?api_token=').auth()->user()->api_token ;
        return view($this->view.'.username', compact('apiUpdate'));
    }

    public function room()
    {
        $apiUpdate = url('api/profile/room?api_token=').auth()->user()->api_token ;

        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/profile/room?api_token=".Auth()->user()->api_token ;
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
        $roomUser = $json['response']['room_user'] ;

        $domainId = auth()->user()->recent_domain ;
        $domainName = auth()->user()->getDomainName() ;

        return view($this->view.'.room', compact('apiUpdate', 'roomUser', 'domainId', 'domainName'));
    }
    public function address()
    {
        $apiUpdate = url('api/profile/address?api_token=').auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/profile/address?api_token=".Auth()->user()->api_token ;
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

        $address = $json['response']['user_address'] ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/province?api_token='.Auth()->User()->api_token ;
        $res =  $client->post($url, ['form_params'=>['name'=>'']]);
        $json = json_decode($res->getBody()->getContents(), true);
        $province = $json ;

        $domainId = auth()->user()->recent_domain ;
       
        return view($this->view.'.address', compact('apiUpdate', 'address', 'domainId', 'province'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        
        $title = $this->title ;
        $home = "/dashboard" ;
        $route = $this->route ;
        $action = $route."/show" ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$action."?api_token=".Auth()->user()->api_token ;

        $apiUpdate = url('')."/api/profile/update?api_token=".Auth()->user()->api_token ;

        $response = $client->get($url);
        // var_dump($response->getBody()->getContents());die;
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
        $data = $json['response']['user'] ;
        $addressCnt = $json['response']['address'] ;
        $attachmentCnt = $json['response']['attachment'] ;
        $roomCnt = $json['response']['room'] ;

        
       
        $show = true ;
        $edit = true ;
        return view($this->view.'.create', compact('title', 'route', 'data', 'show', 'home', 'edit', 'apiUpdate', 'attachmentCnt', 'addressCnt', 'roomCnt'));
    }
}
