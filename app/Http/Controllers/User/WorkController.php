<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;

class WorkController extends Controller
{
    private $route = 'user/work' ;
    private $title = '' ;
    private $view = 'user.work' ;

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

         // var_dump("expression");die;
        $title = $this->title ;
        $route = $domainId."/work/$roomId/user?api_token=".Auth()->user()->api_token ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId."/work/$roomId/user?api_token=".Auth()->user()->api_token ;
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
        $taskCategory = $json['response']['master_work_system_type'] ;
        $prioritize = $json['response']['master_prioritize'] ;
          $areaType = $json['response']['master_work_area_type'] ;
        $jobType = $json['response']['master_work_job_type'] ;
        $lang = getLang();
        $descriptionTitle = $lang=='en' ? 'Problem' : 'รายการที่แจ้ง';
        $memberTitle = $lang=='en' ? 'Technician' : 'ผู้ปฏิบัติ';
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'prioritize', 'taskCategory', 'roomId', 'descriptionTitle', 'jobType', 'areaType', 'memberTitle'));
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

         // var_dump("expression");die;
        $title = $this->title ;
        $route = $domainId."/user/suggest/system?api_token=".Auth()->user()->api_token ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId."/work/$roomId/user?api_token=".Auth()->user()->api_token ;

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
        $taskCategory = $json['response']['master_work_system_type'] ;
        $prioritize = $json['response']['master_prioritize'] ;
        $areaType = $json['response']['master_work_area_type'] ;
        $jobType = $json['response']['master_work_job_type'] ;
        $lang = getLang();
        $descriptionTitle = $lang=='en' ? 'Problem' : 'รายการที่แจ้ง';
        $memberTitle = $lang=='en' ? 'Technician' : 'ผู้ปฏิบัติ';
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'tasks', 'prioritize', 'taskCategory', 'taskId', 'roomId', 'descriptionTitle', 'jobType', 'areaType', 'memberTitle'));
    }
}
