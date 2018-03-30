<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Route;
use stdClass ;

class RoomController extends Controller
{
    private $route = 'rooms' ;
    private $title = 'Room' ;
    private $view = 'user.room' ;

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

        $title = $this->title ;
        $baseRoute = $this->route ;
        $route = $domainName."/".$baseRoute ;

        $client = new \GuzzleHttp\Client();
        $apiUrl = url('').'/api/'.$domainId."/".$baseRoute."?api_token=".Auth()->user()->api_token ;
        $response = $client->get($apiUrl);
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

        $rooms = $json['response']['rooms'] ;
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'rooms'));
    }

   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $domainId, $roomId, $taskId)
    {
          $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $title = $this->title ;
        
        $route = $domainId."/user/".$roomId."/task?api_token=".Auth()->user()->api_token ;
        
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route ;
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
        $tasks = $json['response']['tasks'] ;
        $taskCategory = $json['response']['master_task_category'] ;
        $taskMember = $json['response']['member_task'] ;
       
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'taskId', 'taskCategory', 'taskMember', 'roomId'));
    }

    public function create($domainId)
    {
          $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $title = $this->title ;
        $baseRoute = $this->route ;
        $route = $domainId."/".$baseRoute ;
        $apiUrl = url('')."/api/".$route."?api_token=".auth()->user()->api_token ;
        return view($this->view.'.create', compact('title', 'route', 'domainId', 'domainName', 'baseRoute', 'apiUrl'));
    }
    public function edit($domainId, $roomId)
    {
          $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $title = $this->title ;
        $baseRoute = $this->route ;
        $route = $domainId."/".$baseRoute."/".$roomId ;
        $url = url('')."/api/".$route."/edit?api_token=".auth()->user()->api_token ;
        $apiUrl = url('')."/api/".$route."?api_token=".auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
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
        $data = $json['response']['room'] ;
        $roomUser = $json['response']['room_user'] ;
        $edit = true;
        return view($this->view.'.create', compact('title', 'route', 'domainId', 'domainName', 'baseRoute', 'apiUrl', 'edit', 'data', 'roomId', 'roomUser'));
    }
}
