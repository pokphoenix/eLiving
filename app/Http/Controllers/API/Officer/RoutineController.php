<?php

namespace App\Http\Controllers\API\Officer;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Room;
use App\Models\Routine\Routine;
use App\Models\Search;
use App\Models\StatusHistory;
use App\Models\Task\Task;
use App\Models\Task\TaskAttach;
use App\Models\Task\TaskCategory;
use App\Models\Task\TaskChecklist;
use App\Models\Task\TaskChecklistItem;
use App\Models\Task\TaskComment;
use App\Models\Task\TaskHistory;
use App\Models\Task\TaskMember;
use App\Models\Task\TaskViewer;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class RoutineController extends ApiController
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

    public function search(Request $request)
    {
    }

    public function index($domainId)
    {
  
        // $sql = "select t.*,tc.name_en as category_name
        //         ,tc.color as category_color
        //         ,u.id as member_id
        //         ,CONCAT( u.first_name,' ',u.last_name) as member_name
        //         ,CONCAT( '".url('')."/public/img/profile/',avartar_id,'.png') as member_img
        //         ,IFNULL(t2.cnt,0) as success_checklist
        //         ,IFNULL(t3.cnt,0) as total_checklist
        //         ,t4.file_path
        //         from tasks as t
        //         left join (
        //             SELECT task_id,count(task_id) as cnt
        //             FROM task_checklist_items WHERE domain_id=$domainId AND status=1
        //             GROUP BY task_id
        //         ) t2
        //         ON t2.task_id = t.id
        //         left join (
        //             SELECT task_id,count(task_id) as cnt
        //             FROM task_checklist_items WHERE domain_id=$domainId
        //             GROUP BY task_id
        //         ) t3
        //         ON t3.task_id = t.id

        //         left join (SELECT ta.*,CONCAT( '".url('')."/public/storage/',ta.path,'/',ta.filename) as file_path
        //             FROM task_attachments ta
        //             WHERE ta.domain_id=$domainId
        //             ORDER BY ta.id DESC) t4
        //         ON t4.task_id = t.id

        //         left join master_task_category as tc
        //         on t.category_id = tc.id
        //         left join task_members as tm
        //         on tm.task_id = t.id
        //         and tm.domain_id = $domainId
        //         left join users as u
        //         on tm.user_id = u.id
        //         WHERE t.domain_id = $domainId
        //         AND t.type=2
        //         ORDER BY created_at DESC" ;
        // $tasks   =  DB::select(DB::raw($sql));
        // $data['tasks'] = [];
        // if (!empty($tasks )){
        //     foreach ($tasks as $key => $task) {
        //         $data['tasks'][$task->id]['id'] = $task->id ;
        //         $data['tasks'][$task->id]['title'] = $task->title ;
        //         $data['tasks'][$task->id]['created_at'] = strtotime($task->created_at) ;
        //         $data['tasks'][$task->id]['status'] = $task->status ;
        //         $data['tasks'][$task->id]['status_text'] = Task::statusText($task->status) ;
        //         $data['tasks'][$task->id]['status_color'] = Task::statusColor($task->status) ;
        //         $data['tasks'][$task->id]['pioritized'] = $task->pioritized ;
        //         $data['tasks'][$task->id]['is_issues'] = $task->is_issues ;
        //         $data['tasks'][$task->id]['due_dated_at'] = $task->due_dated_at ;
        //         $data['tasks'][$task->id]['due_dated_complete'] = $task->due_dated_complete ;
        //         $data['tasks'][$task->id]['domain_id'] = $task->domain_id ;
        //         $data['tasks'][$task->id]['start_task_at'] = $task->start_task_at ;
        //         $data['tasks'][$task->id]['due_date_complete_at'] = $task->due_date_complete_at ;
        //         $data['tasks'][$task->id]['category_id'] = $task->category_id;
        //         $data['tasks'][$task->id]['category_name'] = $task->category_name ;
        //         $data['tasks'][$task->id]['category_color'] = $task->category_color ;
        //         $data['tasks'][$task->id]['checklist_success'] = $task->success_checklist ;
        //         $data['tasks'][$task->id]['checklist_total'] = $task->total_checklist ;
        //         $data['tasks'][$task->id]['file_path'] = $task->file_path ;
               
        //         if(isset($task->member_id)){
        //             $member['member_id'] = $task->member_id;
        //             $member['member_name'] = $task->member_name;
        //             $member['member_img'] = $task->member_img;
        //             $data['tasks'][$task->id]['members'][] =  $member ;
        //         }else{
        //             $data['tasks'][$task->id]['members'] = [];
        //         }
        //     }
        // }
        
        $routines = Routine::all();

        $data['routines'] = $routines;
        $data['routine_category'] = Routine::category();
      
        return $this->respondWithItem($data);
    }
   
    public function show($domainId, $cardId)
    {
        $data = Routine::getData($domainId, $cardId);
        $data['routine_category'] = Routine::category();
        return $this->respondWithItem($data);
    }

    public function view($domainId)
    {

        $sql = "SELECT u.id,CONCAT( u.first_name,' ',u.last_name) as text
                
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                FROM routine 
                WHERE is_never = 1
               
                WHERE tm.domain_id =$domainId AND tm.task_id=$taskId ";
        return  DB::select(DB::raw($sql));

        $data['routine_category'] = Routine::category();
        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        if (isset($post['is_never'])) {
            unset($post['repeat_ended_at']);
            if ($post['is_never']=="on") {
                $post['is_never'] = 1;
            }
        } else {
            $post['is_never'] = 0;
        }
        if (isset($post['is_all_day'])) {
            $post['started_at'] = Carbon::today();
            $post['ended_at'] = Carbon::tomorrow();
            if ($post['is_all_day']=="on") {
                $post['is_all_day'] = 1;
            }
        } else {
            $post['is_all_day'] = 0;
        }


        $routine = new Routine ;
        $routine->title = $post['title'];
        if (isset($post['started_at'])) {
            $routine->started_at = $post['started_at'];
        }
        if (isset($post['ended_at'])) {
            $routine->ended_at = $post['ended_at'];
        }
        if (isset($post['repeat_ended_at'])) {
            $routine->started_at = $post['repeat_ended_at'];
        }
        if (isset($post['is_never'])) {
            $routine->is_never = $post['is_never'];
        }
        if (isset($post['is_all_day'])) {
            $routine->is_all_day =  $post['is_all_day'] ;
        }
        
        $routine->domain_id = $domainId;
        $routine->category_id = $post['category_id'];
        $routine->repeat_type = $post['repeat_type'];
        $routine->created_at = Carbon::now();
        $routine->created_by = auth()->user()->id;
        $routine->save();
        $data['routine'] = $routine;
        return $this->respondWithItem($data);
    }

    public function update(Request $request, $domainId, $cardId)
    {
        $post = $request->except('api_token', '_method');

        if (isset($post['is_never'])) {
            unset($post['repeat_ended_at']);
            if ($post['is_never']=="on") {
                $post['is_never'] = 1;
            }
        } else {
            $post['is_never'] = 0;
        }
        if (isset($post['is_all_day'])) {
            $post['started_at'] = Carbon::today();
            $post['ended_at'] = Carbon::tomorrow();
            if ($post['is_all_day']=="on") {
                $post['is_all_day'] = 1;
            }
        } else {
            $post['is_all_day'] = 0;
        }


        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $routine = Routine::find($cardId)->update($post);
        $data = Routine::getData($domainId, $cardId);
        return $this->respondWithItem($data);
    }
   

    private function validator($data)
    {
        return Validator::make($data, [
            'title' => 'required|string|max:255',
            'category_id' => 'required|in:1,2',
            'repeat_type' => 'required',
        ]);
    }
}
