<?php

namespace App\Http\Controllers\Officer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller ;
use DB;
use Route;
use stdClass ;
use Auth;
class RoutineController extends Controller
{
    private $route = 'routine' ;
    private $title = 'นิติ' ;
    private $view = 'officer.routine' ;

    public function __construct(){
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($domainId)
    {
        $title = $this->title ;
        $route = $domainId."/".$this->route."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route ;

        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 

        if(!isset($json['result'])){
            return $response->getContent() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }

        $cards = $json['response']['routines'] ;
        $routineCategory = $json['response']['routine_category'] ;

        return view($this->view.'.index',compact('title','route','domainId','cards','routineCategory'));
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

    public function view($domainId){

        $title = $this->title ;
        
        $route = $domainId."/".$this->route."/view?api_token=".Auth()->user()->api_token ;
  
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }
        $cards = $json['response']['routines'] ;

        return view($this->view.'.view',compact('title','route','domainId','tasks','statusHistory','taskCategory','taskMember'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$domainId,$routineId)
    {
        $title = $this->title ;
        
        $route = $domainId."/".$this->route."?api_token=".Auth()->user()->api_token ;
  
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }
        $cards = $json['response']['routines'] ;
        $cardId = $routineId; 
        $routineCategory = $json['response']['routine_category'] ;

        return view($this->view.'.index',compact('title','route','domainId','cards','cardId','routineCategory'));
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
