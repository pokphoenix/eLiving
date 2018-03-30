<?php

namespace App\Http\Controllers\Master;

use App;
use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Route;
use stdClass ;

class EstickerController extends Controller
{
    private $route = 'master/contact' ;
    private $title  ;
    private $view = 'master.contact' ;

    public function __construct()
    {
        $this->title = App::isLocale('en') ? 'Contact List Type' : 'ประเภทรวมเบอร์ติดต่อ' ;
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
        $route = $this->route ;
        $action = url('/api/'.$domainId."/".$route)."?api_token=".Auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId."/".$route."?api_token=".Auth()->user()->api_token ;
       
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

        $lists = $json['response']['contact_type'] ;
        
       
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'lists', 'action'));
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