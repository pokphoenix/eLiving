<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;
use Validator;

class MoveServerController extends Controller
{
    private $route = 'move-server' ;
    private $title = '' ;
    private $view = 'backend.move_server' ;

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
        return view($this->view.'.index', compact('domainName'));
    }

    public function update(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');

        $validator = $this->validator($post);
        if ($validator->fails()) {
            return redirect()->back()->withError($validator->errors());
        }


        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $oldUrl = trim($post['old_url']);
        $newUrl = trim($post['new_url']);

      

        $sql = "UPDATE  users SET profile_url = REPLACE(profile_url, '$oldUrl', '$newUrl') WHERE profile_url is not null;
                UPDATE  e_sticker SET qrcode = REPLACE(qrcode, '$oldUrl', '$newUrl') ;
                UPDATE quotation_setting SET 
                logo_left = REPLACE(logo_left, '$oldUrl', '$newUrl')
                ,logo_right = REPLACE(logo_right, '$oldUrl', '$newUrl');
                UPDATE settings SET `values`= REPLACE(`values`, '$oldUrl', '$newUrl')  
                WHERE `keys`='LOGO_DOMAIN' OR `keys`='LOGO_OFFICER' ;
                " ;
        DB::update(DB::raw($sql));
        return redirect()->back()->with('success', 'update success');
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

    private function validator($data)
    {
        return Validator::make($data, [
            'old_url' => 'required|string',
            'new_url' => 'required|string',
        ]);
    }
}
