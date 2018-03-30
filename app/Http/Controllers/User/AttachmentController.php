<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller ;
use DB;
use Route;
use stdClass ;
use Auth;

class AttachmentController extends Controller
{
    private $route = 'profile' ;
    private $title = 'Profile' ;
    private $view = 'user.profile' ;

    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apiUpdate = url('api/profile/attach?api_token=').auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/profile/attach?api_token=".Auth()->user()->api_token ;
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

        $docs = $json['response']['docs'] ;

        $canDelAttach = true ;

        $domainId = auth()->user()->recent_domain ;
        $domainName = auth()->user()->getDomainName() ;
        return view($this->view.'.attachment', compact('apiUpdate', 'docs', 'domainId', 'domainName', 'province', 'canDelAttach'));
    }
}
