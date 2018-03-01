<?php

namespace App\Http\Controllers\Parcel;
use App;
use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Route;
use stdClass ;
class PrintController extends Controller
{
    private $route = 'parcel/print' ;
    private $title  ;
    private $view = 'parcel.officer' ;

    public function __construct(){
        $this->title = App::isLocale('en') ? 'Letter / Parcel Post' : 'ใบบันทึกรายการจดหมาย / พัสดุ ลงทะเบียน' ;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;

        $startDate = $request->input('start_date',time());
        $endDate = $request->input('end_date',time()); 
        



        $title = $this->title ;
        $route = $domainId."/".$this->route ;
        $action = url('/api/'.$route)."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route."?api_token=".Auth()->user()->api_token ;
        if(isset($startDate))
            $url .= "&start_date=".$startDate ;
        if(isset($endDate))
            $url .= "&end_date=".$endDate ;

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

        $lists = $json['response']['parcel_officer'] ;
        
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/parcel/print/setting?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }
        $setting = $json['response'] ;


        $preview = true;
       
        return view($this->view.'.print',compact('title','route','domainId','domainName','lists','action','room','parcelTypes','suppliesTypes','setting','preview','startDate','endDate'));
    }


    public function getGift(Request $request,$domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;

        $startDate = $request->input('start_date',time());
        $endDate = $request->input('end_date',time()); 
        
        $title =  App::isLocale('en') ? 'Letter' : 'ใบขอรับของฝาก' ;
        $route = $domainId."/".$this->route ;
        $action = url('/api/'.$route)."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/".$domainId."/parcel/print-gift?api_token=".Auth()->user()->api_token ;
         if(isset($startDate))
            $url .= "&start_date=".$startDate ;
        if(isset($endDate))
            $url .= "&end_date=".$endDate ;
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

        $lists = $json['response']['parcel_officer'] ;
        
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/parcel/print/setting?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }
        $setting = $json['response'] ;

 
        $preview = true;
        
        return view($this->view.'.print_gift',compact('title','route','domainId','domainName','lists','action','room','parcelTypes','suppliesTypes','setting','preview','startDate','endDate'));
    }

    public function getGiftView(Request $request,$domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;

        $id = $request->input('id');
        
        $title =  App::isLocale('en') ? 'Letter' : 'ใบขอรับของฝาก' ;
        $route = $domainId."/".$this->route ;
        $action = url('/api/'.$route)."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/".$domainId."/parcel/print-gift/view?api_token=".Auth()->user()->api_token."&id=".$id ;
       
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

        $lists = $json['response']['parcel_officer'] ;
        
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/parcel/print/setting?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            return $json['errors'] ;
        }
        $setting = $json['response'] ;

 
        $preview = true;
        
        return view($this->view.'.print_gift_view',compact('title','route','domainId','domainName','lists','action','room','parcelTypes','suppliesTypes','setting','preview','startDate','endDate'));
    }

    public function getMail(Request $request,$domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;

        $startDate = $request->input('start_date',time());
        $endDate = $request->input('end_date',time()); 
        

        $title =  App::isLocale('en') ? 'Letter / Parcel' : 'ใบขอรับจดหมายและพัสดุลงทะเบียน' ;
        $route = $domainId."/".$this->route ;
        $action = url('/api/'.$route)."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/".$domainId."/parcel/print-mail?api_token=".Auth()->user()->api_token ;
        if(isset($startDate))
            $url .= "&start_date=".$startDate ;
        if(isset($endDate))
            $url .= "&end_date=".$endDate ;
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

        $lists = $json['response']['parcel_officer'] ;
        
    
        $preview = true;
        
        return view($this->view.'.print_mail',compact('title','route','domainId','domainName','lists','action','room','parcelTypes','suppliesTypes','setting','preview','startDate','endDate'));
    }

    public function getMailView(Request $request,$domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;

        $id = $request->input('id');

        $title =  App::isLocale('en') ? 'Letter / Parcel' : 'ใบขอรับจดหมายและพัสดุลงทะเบียน' ;
        $route = $domainId."/".$this->route ;
        $action = url('/api/'.$route)."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/".$domainId."/parcel/print-gift/view?api_token=".Auth()->user()->api_token."&id=".$id  ;
      
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

        $lists = $json['response']['parcel_officer'] ;
        
    
        $preview = true;
        
        return view($this->view.'.print_mail_view',compact('title','route','domainId','domainName','lists','action','room','parcelTypes','suppliesTypes','setting','preview','startDate','endDate'));
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
