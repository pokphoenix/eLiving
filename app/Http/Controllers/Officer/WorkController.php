<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use App\Models\Setting;
use Auth;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;

class WorkController extends Controller
{
    private $route = 'officer/work' ;
    private $title = 'นิติ' ;
    private $view = 'officer.work' ;

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

        $tasks = $json['response']['works'] ;
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
        $taskCategory = $json['response']['master_work_system_type'] ;
        $prioritize = $json['response']['master_prioritize'] ;
        $areaType = $json['response']['master_work_area_type'] ;
        $jobType = $json['response']['master_work_job_type'] ;
        $taskMember = $json['response']['member_officer'] ;
        $lang = getLang();
        $descriptionTitle = $lang=='en' ? 'Problem' : 'รายการที่แจ้ง';
        $memberTitle = $lang=='en' ? 'Technician' : 'ผู้ปฏิบัติ';
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'statusHistory', 'taskCategory', 'taskMember', 'taskDone', 'prioritize', 'descriptionTitle', 'memberTitle', 'areaType', 'jobType'));
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
        $tasks = $json['response']['works'] ;
        $taskDone = [];
        foreach ($tasks as $task) {
            if ($task['status']==7) {
                $taskDone[] = $task ;
            }
        }

        usort($taskDone, function ($a, $b) {
            return $b['doned_at']-$a['doned_at'];
        });
        $statusHistory = $json['response']['master_status_history'] ;
        $taskCategory = $json['response']['master_work_system_type'] ;
        $prioritize = $json['response']['master_prioritize'] ;
        $taskMember = $json['response']['member_officer'] ;
        $areaType = $json['response']['master_work_area_type'] ;
        $jobType = $json['response']['master_work_job_type'] ;
        $lang = getLang();
        $descriptionTitle = $lang=='en' ? 'Problem' : 'รายการที่แจ้ง';
        $memberTitle = $lang=='en' ? 'Technician' : 'ผู้ปฏิบัติ';
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'taskId', 'taskCategory', 'taskMember', 'taskDone', 'prioritize', 'descriptionTitle', 'memberTitle', 'areaType', 'jobType'));
    }

    public function printView(Request $request, $domainId, $taskId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        
  
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId."/".$this->route."/$taskId/print?api_token=".Auth()->user()->api_token ;
       
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
        $work = $json['response']['work'] ;
       
        $preview = true;

        $data['logo_domain'] = Setting::getVal($domainId, 'LOGO_DOMAIN');

        $lang = getLang();
        $descriptionTitle = $lang=='en' ? 'Problem' : 'รายการที่แจ้ง';
        $memberTitle = $lang=='en' ? 'Technician' : 'ผู้ปฏิบัติ';
        return view($this->view.'.print', compact('title', 'route', 'domainId', 'domainName', 'data', 'taskId', 'taskCategory', 'taskMember', 'taskDone', 'prioritize', 'descriptionTitle', 'memberTitle', 'preview', 'work'));
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
