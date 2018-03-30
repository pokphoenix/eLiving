<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Route;
use stdClass ;

class DomainController extends Controller
{
    private $route = 'setting/domain' ;
    private $title = 'Profile' ;
    private $view = 'setting.domain' ;

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

        $data['logo_domain'] = $json['response']['logo_domain'] ;
        $data['name_domain'] = $json['response']['name_domain'] ;
        return view($this->view, compact('domainId', 'domainName', 'data'));
    }
}
