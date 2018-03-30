<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;
use Validator;

class BillController extends Controller
{
    private $route = 'bill' ;
    private $title ;
    private $view = 'bill' ;

    public function __construct()
    {
        $this->title = App::isLocale('en') ? 'Import Bill' : 'นำเข้าใบแจ้งหนี้' ;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $title = $this->title;
        $route = $domainId."/".$this->route ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route."?api_token=".auth()->user()->api_token ;
        //$url = url('').'/api/'.'bill';
        //echo  $url;
        $response = $client->get($url);
        //var_dump($response->getBody()->getContents());die;
        $json = json_decode($response->getBody()->getContents(), true);
       // var_dump($json);die;
        if (!isset($json['result'])) {
             $json['errors'] = $response->getBody()->getContents() ;
               return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']=="false") {
            return redirect('error')
                ->withError($json['errors']);
        }
        $lists = $json['response']['bill'] ;

        $action = url('/api/'.$route)."?api_token=".Auth()->user()->api_token ;
        return view($this->view.'.create', compact('domainName', 'title', 'lists', 'action'));
    }
    public function index($domainId)
    {
    }

    public function update(Request $request, $domainId)
    {
    }
}
