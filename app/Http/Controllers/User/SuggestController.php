<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Route;
use stdClass ;

class SuggestController extends Controller
{
    private $route = 'user/suggest/system' ;
    private $title = '' ;
    private $view = 'user.suggest' ;

    public function __construct()
    {
        // var_dump("expression");die;
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

         // var_dump("expression");die;
        $title = $this->title ;
        $route = $domainId."/user/suggest/system?api_token=".Auth()->user()->api_token ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId."/".$this->route."?api_token=".Auth()->user()->api_token ;

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

        $tasks = $json['response']['suggests'] ;
        $statusHistory = $json['response']['master_status_history'] ;
        $taskCategory = $json['response']['master_suggest_category'] ;
       
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'statusHistory', 'taskCategory'));
    }

   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $domainId, $taskId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

         // var_dump("expression");die;
        $title = $this->title ;
        $route = $domainId."/user/suggest/system?api_token=".Auth()->user()->api_token ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId."/".$this->route."?api_token=".Auth()->user()->api_token ;

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

        $tasks = $json['response']['suggests'] ;
        $statusHistory = $json['response']['master_status_history'] ;
        $taskCategory = $json['response']['master_suggest_category'] ;
       
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'statusHistory', 'taskCategory', 'taskId'));
    }
}
