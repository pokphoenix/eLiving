<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
class DashboardController extends Controller
{
    private $view = 'main.dashboard';
    private $title = 'Dashboard';
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
    }
    public function index($domainId){

        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/dashboard?api_token='.Auth()->User()->api_token ;
        $response = $client->request('GET', $url);
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
        $domains = $json['response']['domain'] ;
        $notifications = $json['response']['notification'] ;
        $quotations = $json['response']['quotation'] ;
        $preWelcom = $json['response']['pre_welcom'] ;
        $lists = $json['response']['posts'] ;
        $canPost  =true;
        return view($this->view.'.index',compact('domains','title','domainId','domainName','notifications','quotations','preWelcom','lists','canPost'));
    }
    // public function waitApprove($domainId){
    //     $title = $this->title ;
    //     $client = new \GuzzleHttp\Client();
    //     $idcard = auth()->user()->id_card;

    //     if(auth()->user()->checkApprove()){
    //         return redirect(auth()->user()->recent_domain.'/dashboard');
    //     }

    //     $url = url('').'/api/'.$domainId.'/create-user/'.$idcard.'/edit?api_token='.Auth()->User()->api_token ;
    //     $res = $client->get($url);
    //     $json = json_decode($res->getBody()->getContents(),true); 
    //     if(!isset($json['result'])){
    //         $json['errors'] = $res->getBody()->getContents() ;
    //         return redirect()->back()
    //         ->withError($json['errors']);
    //     }
    //     if($json['result']==false)
    //     {
    //         return redirect()->back()
    //             ->withError($json['errors']);
    //     }



       
    //     $data = $json['response']['user'] ;
    //     $roomUser = $json['response']['room_user'] ;
    //     $address = $json['response']['address'] ;
    //     $docs = $json['response']['docs'] ;
    //     $action = url('').'/api/'.$domainId.'/wait-approve?api_token='.Auth()->User()->api_token ;
    //     $routePath = "" ;
    //     $wait = true ;
    //     $edit = true;

    //     $client = new \GuzzleHttp\Client();
    //     $url = url('').'/api/search/province?api_token='.Auth()->User()->api_token ;
    //     $res =  $client->post($url,  ['form_params'=>['name'=>'']] );
    //     $json = json_decode($res->getBody()->getContents(),true); 
    //     $province = $json ;

    //     $amphur = [];
    //     if(isset($address['province_id'])){
    //         $client = new \GuzzleHttp\Client();
    //         $url = url('').'/api/search/amphur-id?api_token='.Auth()->User()->api_token ;
    //         $res =  $client->post($url,  ['form_params'=>['id'=>$address['province_id']]] );
    //         $json = json_decode($res->getBody()->getContents(),true); 
    //         $amphur = $json['response']['amphurs'] ;
    //     }

    //     $district = [];
    //     if(isset($address['province_id'])){
    //         $client = new \GuzzleHttp\Client();
    //         $url = url('').'/api/search/district-id?api_token='.Auth()->User()->api_token ;
    //         $res =  $client->post($url,  ['form_params'=>['id'=>$address['amphur_id']]] );
    //         $json = json_decode($res->getBody()->getContents(),true); 
    //         $district = $json['response']['districts'] ;
    //     }


    //     return view('main.dashboard.wait',compact('domains','title','domainId','data','edit','address','docs','routePath','roomUser','wait','action','province','amphur','district'));
    // }


   
  
}
