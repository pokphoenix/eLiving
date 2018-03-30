<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;

class TaskController extends Controller
{
    private $route = 'task' ;
    private $title = 'นิติ' ;
    private $view = 'main.task' ;

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
        $route = $domainId."/".$this->route."?api_token=".Auth()->user()->api_token ;
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
        $taskDone = [];
        foreach ($tasks as $task) {
            if ($task['status']==7) {
                $taskDone[] = $task ;
            }
        }

        usort($taskDone, function ($a, $b) {
            $ad = new DateTime($a['doned_at']);
            $bd = new DateTime($b['doned_at']);
            if ($ad == $bd) {
                return 0;
            }
            return $ad > $bd ? -1 : 1;
        });

        $statusHistory = $json['response']['master_status_history'] ;
        $taskCategory = $json['response']['master_task_category'] ;
        $taskMember = $json['response']['member_task'] ;
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'statusHistory', 'taskCategory', 'taskMember', 'taskDone'));
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
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        
        $route = $domainId."/".$this->route."?api_token=".Auth()->user()->api_token ;
  
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
        $taskDone = [];
        foreach ($tasks as $task) {
            if ($task['status']==7) {
                $taskDone[] = $task ;
            }
        }

        usort($taskDone, function ($a, $b) {
            return $b['doned_at']-$a['doned_at'];
        });
        $taskCategory = $json['response']['master_task_category'] ;
        $taskMember = $json['response']['member_task'] ;
       
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'taskId', 'taskCategory', 'taskMember', 'taskDone'));
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
