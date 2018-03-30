<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Route;
use stdClass ;

class OfficerController extends Controller
{
    private $route = 'setting/officer' ;
    private $title = 'Profile' ;
    private $view = 'setting.officer' ;

    public function __construct()
    {
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

        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/".$domainId."/".$this->route."?api_token=".Auth()->user()->api_token ;
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

        $data['logo_officer'] = $json['response']['logo_officer'] ;
        $data['name_officer'] = $json['response']['name_officer'] ;
        return view($this->view, compact('domainId', 'domainName', 'data'));
    }
}
