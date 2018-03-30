<?php

namespace App\Http\Controllers\Main;

use App;
use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Route;
use stdClass ;

class ContactController extends Controller
{
    private $route = 'contact' ;
    private $title  ;
    private $view = 'main.contact' ;

    public function __construct()
    {
        $this->title = App::isLocale('en') ? 'Contact List' : 'รวมเบอร์ติดต่อ' ;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;


        $startDate = $request->input('start_date', time());
        $endDate = $request->input('end_date', time());
        



        $title = $this->title ;
        $route = $domainId."/".$this->route ;
        $action = url('/api/'.$route)."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route."?api_token=".Auth()->user()->api_token ;
        if (isset($startDate)) {
            $url .= "&start_date=".$startDate ;
        }
        if (isset($endDate)) {
            $url .= "&end_date=".$endDate ;
        }

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

        $lists = $json['response']['contacts'] ;
        
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/contact/type?api_token='.Auth()->User()->api_token ;
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
        $contactType = $json['response']['contact_type'] ;

        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'lists', 'action', 'contactType', 'startDate', 'endDate'));
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
   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($domainId, $id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $domainId, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($domainId, $id)
    {
    }
}
