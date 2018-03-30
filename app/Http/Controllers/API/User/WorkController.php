<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Master\SuggestCategory;
use App\Models\Master\WorkAreaType;
use App\Models\Master\WorkJobType;
use App\Models\Master\WorkPioritize;
use App\Models\Master\WorkSystemType;
use App\Models\Room;
use App\Models\Search;
use App\Models\StatusHistory;
use App\Models\Suggest\Suggest;
use App\Models\Task\TaskAttach;
use App\Models\Task\TaskCategory;
use App\Models\Task\TaskChecklist;
use App\Models\Task\TaskChecklistItem;
use App\Models\Task\TaskComment;
use App\Models\Task\TaskHistory;
use App\Models\Task\TaskMember;
use App\Models\Task\TaskViewer;
use App\Models\Work\Work;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class WorkController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function __construct()
    {
        // $this->middleware('auth:api');
    }

  

    public function index($domainId, $roomId)
    {
        $url = url('');
        $lang = getLang();
        $sql = "select t.*,tc.name_$lang as category_name 
                ,tc.color as category_color
                ,tc.name_$lang
                from works as t 
                left join master_work_system_type as tc 
                on t.category_id = tc.id 
              
                WHERE t.created_by = ".Auth::user()->id."
                AND t.domain_id = $domainId
                AND t.room_id =$roomId
               
                ORDER BY t.created_at DESC" ;
        $query   =  DB::select(DB::raw($sql));
        $data['works'] = [];
        if (!empty($query)) {
            foreach ($query as $key => $work) {
                $data['works'][$work->id]['id'] = $work->id ;
                $data['works'][$work->id]['title'] = $work->title ;
                $data['works'][$work->id]['created_at'] = strtotime($work->created_at) ;
                $data['works'][$work->id]['status'] = $work->status ;
                $data['works'][$work->id]['status_text'] = Work::statusText($work->status) ;
                $data['works'][$work->id]['status_color'] = Work::statusColor($work->status) ;
               
                $data['works'][$work->id]['domain_id'] = $work->domain_id ;
               
                $data['works'][$work->id]['category_id'] = $work->category_id;
                $data['works'][$work->id]['category_name'] = $work->category_name ;
                $data['works'][$work->id]['category_color'] = $work->category_color  ;
            }
        }
      
        $data['works'] = array_values($data['works']);
        $data['master_prioritize'] = WorkPioritize::getData() ;
        $data['master_work_system_type'] = WorkSystemType::getData() ;
        $data['master_work_area_type'] = WorkAreaType::getData() ;
        $data['master_work_job_type'] = WorkJobType::getData() ;
       
        return $this->respondWithItem($data);
    }
   
    public function show($domainId, $roomId, $workId)
    {
       
        $data = Work::getData($domainId, $workId, 2);
        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId, $roomId)
    {
        $userId = Auth::user()->id ;
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $work = new Work();
        $work->title = $post['title'] ;
        $work->category_id = $post['category_id'] ;
        $work->pioritized = $post['pioritized'] ;
        $work->pioritized_desc = isset($post['pioritized_desc']) ? $post['pioritized_desc'] : null ;
        $work->domain_id = $domainId ;
        $work->room_id = $roomId ;
        $work->status = 1 ;
        $work->created_by = $userId ;

        $work->save();

       

        $data = Work::getData($domainId, $work->id, 2);
        return $this->respondWithItem($data);
    }
    
    private function validator($data)
    {
        return Validator::make($data, [
            'title' => 'required|string|max:255',
            'category_id' => 'required|numeric',
        ]);
    }
}
