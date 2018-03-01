<?php

namespace App\Http\Controllers\Parking;
use App;
use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Route;
use stdClass ;
class BuyController extends Controller
{
    private $route = 'parking/buy' ;
    private $title = 'นิติ' ;
    private $view = 'parking.buy' ;

    public function __construct(){
        $this->title = App::isLocale('en') ? 'Sell E Coupon' : 'ขายคูปอง' ;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $route = $domainId."/".$this->route ;
        $action = url('/api/'.$route)."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route."?api_token=".Auth()->user()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 

        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return redirect('error')
                ->withError($json['errors']);
        }

        $lists = $json['response']['parking_buys'] ;
     

        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/".$domainId."/search/room?api_token=".Auth()->user()->api_token ;
        $response = $client->post($url,  ['form_params'=>['name'=>'']] );
        $json = json_decode($response->getBody()->getContents(),true); 

        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return redirect('error')
                ->withError($json['errors']);
        }

        $room = $json['response']['data'] ;
       

        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/".$domainId."/parking/package?api_token=".Auth()->user()->api_token ;

        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return redirect('error')
                ->withError($json['errors']);
        }

        $package = $json['response']['parking_packages'] ;

        return view($this->view.'.index',compact('title','route','domainId','domainName','lists','action','room','package'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($domainId)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($domainId)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$domainId,$taskId)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($domainId,$id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $domainId,$id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($domainId,$id)
    {
    } 
}
