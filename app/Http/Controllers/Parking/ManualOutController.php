<?php

namespace App\Http\Controllers\Parking;

use App;
use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Route;
use stdClass ;

class ManualOutController extends Controller
{
    private $route = 'manual/parking/out' ;
    private $title  ;
    private $view = 'parking.manual_out' ;

    public function __construct()
    {

        $this->title = App::isLocale('en') ? 'Parking Manual Out' : 'บันทึก อีคูปอง ย้อนหลังขาออก' ;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($domainId)
    {
        $apiToken = Auth()->user()->api_token;
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $title = $this->title ;
        $route = $domainId."/".$this->route ;
    
        $action = url("/api/$domainId/parking/manual-out")."?api_token=$apiToken" ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/$domainId/parking/manual-out?api_token=$apiToken" ;


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

        $listIns = $json['response']['parking_manual_in'] ;
        $listOuts = $json['response']['parking_manual_out'] ;

        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/master/debt-type?api_token=".Auth()->user()->api_token ;
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
        $debtType = $json['response']['debt_type'] ;
        
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'listIns', 'action', 'listOuts', 'debtType'));
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
    public function show(Request $request, $domainId, $taskId)
    {
    }

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
