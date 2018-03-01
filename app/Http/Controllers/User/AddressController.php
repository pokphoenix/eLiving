<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller ;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;
class AddressController extends Controller
{
    private $route = 'profile' ;
    private $title = 'Profile' ;
    private $view = 'user.profile' ;

    public function __construct(){
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apiUpdate = url('api/profile/address?api_token=').auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/profile/address?api_token=".Auth()->user()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }

        $userAddress = $json['response']['user_address'] ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/province?api_token='.Auth()->User()->api_token ;
        $res =  $client->post($url,  ['form_params'=>['name'=>'']] );
        $json = json_decode($res->getBody()->getContents(),true); 
        $province = $json ;

        $domainId = auth()->user()->recent_domain ;
        $domainName = auth()->user()->getDomainName() ;
        return view($this->view.'.address',compact('apiUpdate','userAddress','domainId','domainName','province'));
    }

    public function create()
    {
        $title =  App::isLocale('en') ? 'Create Address' : 'เพิ่มที่อยู่';
        $apiUpdate = url('api/profile/address?api_token=').auth()->user()->api_token ;
        

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/province?api_token='.Auth()->User()->api_token ;
        $res =  $client->post($url,  ['form_params'=>['name'=>'']] );
        $json = json_decode($res->getBody()->getContents(),true); 
        $province = $json ;
         $showAddressName = true;
        $domainId = auth()->user()->recent_domain ;
         $domainName = auth()->user()->getDomainName() ;
        return view($this->view.'.address-create',compact('apiUpdate','domainId','domainName','province','title','showAddressName'));
    }

    public function edit($id)
    {
        $title = App::isLocale('en') ? 'Edit Address' : 'แก้ไขที่อยู่';
        $apiUpdate = url('api/profile/address/'.$id.'?api_token=').auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/profile/address/$id/edit?api_token=".Auth()->user()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }

        $userAddress = $json['response']['user_address'] ;
        $address = $json['response']['address'] ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/province?api_token='.Auth()->User()->api_token ;
        $res =  $client->post($url,  ['form_params'=>['name'=>'']] );
        $json = json_decode($res->getBody()->getContents(),true); 
        $province = $json ;

        $amphur = [];
        if(isset($address['province_id'])){
            $client = new \GuzzleHttp\Client();
            $url = url('').'/api/search/amphur-id?api_token='.Auth()->User()->api_token ;
            $res =  $client->post($url,  ['form_params'=>['id'=>$address['province_id']]] );
            $json = json_decode($res->getBody()->getContents(),true); 
            $amphur = $json['response']['amphurs'] ;
        }

        $district = [];
        if(isset($address['province_id'])){
            $client = new \GuzzleHttp\Client();
            $url = url('').'/api/search/district-id?api_token='.Auth()->User()->api_token ;
            $res =  $client->post($url,  ['form_params'=>['id'=>$address['amphur_id']]] );
            $json = json_decode($res->getBody()->getContents(),true); 
            $district = $json['response']['districts'] ;
        }

        $edit = true;
        $domainId = auth()->user()->recent_domain ;
        $domainName = auth()->user()->getDomainName() ;
        return view($this->view.'.address-create',compact('apiUpdate','edit','userAddress','address','domainId','domainName','province','amphur','district','title'));
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
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }
        $data = $json['response']['user'] ;

        $show = true ;
        $edit = true ;
        return view($this->view.'.create',compact('title','route','data','show','home','edit','apiUpdate'));
    }


}
