<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;

class PhoneDirectoryController extends Controller
{
    private $route = 'phone' ;
    private $title ;
    private $view = 'officer.phone' ;

    public function __construct()
    {
        $this->title = (App::isLocale('en')) ? "Phone Directory" : "เบอร์โทรสำคัญ" ;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $route = $this->route ;
        $action =  url('').'/api/'.$domainId.'/'.$route.'?api_token='.Auth()->User()->api_token ;

        
        $client = new \GuzzleHttp\Client();
        $url = $action;
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
        $phoneDirectory = $json['response']['phone_directory'] ;
        $data = $phoneDirectory['text'];

        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'data', 'action'));
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
    public function show(Request $request, $domainId, $Id)
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
