<?php

namespace App\Http\Controllers\Officer;
use App\Http\Controllers\Controller ;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;
class ResolutionController extends Controller
{
    private $route = 'resolution' ;
    private $title = 'โหวตมติ' ;
    private $view = 'officer.resolution' ;

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
        $route = $domainId."/".$this->route.'?api_token='.Auth()->User()->api_token ;
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
        $lists = $json['response']['resolutions'] ;
        $status_history = $json['response']['status_history'] ;
        $taskDone = [];
        foreach ($lists as $list){
            if($list['status']==7){
                $taskDone[] = $list ;
            }
        }
        usort($taskDone, function($a, $b) {
            return $b['doned_at']-$a['doned_at'];
        });

        $hasHeaduser = Auth()->user()->hasRole('head.user');


        return view($this->view.'.index',compact('title','route','domainId','lists','status_history','taskDone','hasHeaduser'));
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
    public function show(Request $request,$domainId,$Id)
    {
        $title = $this->title ;
        $route = $domainId."/".$this->route.'?api_token='.Auth()->User()->api_token ;
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
        $lists = $json['response']['resolutions'] ;
        $taskDone = [];
        foreach ($lists as $list){
            if($list['status']==7){
                $taskDone[] = $list ;
            }
        }
        usort($taskDone, function($a, $b) {
            return $b['doned_at']-$a['doned_at'];
        });

        $hasHeaduser = Auth()->user()->hasRole('head.user');

        return view($this->view.'.index',compact('title','route','domainId','lists','Id','taskDone','hasHeaduser'));
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
