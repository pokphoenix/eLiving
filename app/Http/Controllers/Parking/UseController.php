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
class UseController extends Controller
{
    private $route  ;
    private $title ;
    private $view = 'parking.use' ;

    public function __construct(){
        $this->title = App::isLocale('en') ? 'Use Coupon' : 'ใช้คูปอง' ;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($domainId,$roomId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;

        
        $title = $this->title ;
        $route = "/parking/$roomId/use" ;
        $action = url('/api/'.$domainId.$route)."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.$route."?api_token=".Auth()->user()->api_token ;
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

        $lists = $json['response']['parking_use'] ;
        // $remainHour = $json['response']['remain_hour'] ;
        $package = $json['response']['parking_buy_from_room'] ;
     
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/province?api_token='.Auth()->User()->api_token ;
        $res =  $client->post($url,  ['form_params'=>['name'=>'']] );
        $json = json_decode($res->getBody()->getContents(),true); 
        $province = $json ;

        return view($this->view.'.index',compact('title','route','domainId','domainName','lists','action','roomId','province','package'));
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
