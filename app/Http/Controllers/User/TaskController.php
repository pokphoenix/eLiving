<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Route;
use stdClass ;

class TaskController extends Controller
{
    private $route = 'user/task' ;
    private $title = 'นิติ' ;
    private $view = 'user.task' ;

    public function __construct()
    {
        // var_dump("expression");die;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($domainId, $roomId)
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
        $statusHistory = $json['response']['master_status_history'] ;
        $taskCategory = $json['response']['master_task_category'] ;
        $taskMember = $json['response']['member_task'] ;
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'statusHistory', 'taskCategory', 'taskMember', 'roomId'));
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
}
