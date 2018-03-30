<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Room;
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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class TaskController extends ApiController
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
        $lang =  getLang();
        $sql = "select t.*,tc.name_$lang as category_name 
                ,tc.color as category_color 
              
                ,IFNULL(t2.cnt,0) as success_checklist
                ,IFNULL(t3.cnt,0) as total_checklist
              
                from tasks as t 
                left join ( 
                    SELECT task_id,count(task_id) as cnt
                    FROM task_checklist_items WHERE domain_id=$domainId AND status=1
                    GROUP BY task_id
                ) t2
                ON t2.task_id = t.id
                left join ( 
                    SELECT task_id,count(task_id) as cnt
                    FROM task_checklist_items WHERE domain_id=$domainId
                    GROUP BY task_id
                ) t3 
                ON t3.task_id = t.id

             

                left join master_task_category as tc 
                on t.category_id = tc.id 
              
                WHERE t.type = 2
                AND t.domain_id = $domainId
                AND t.room_id = $roomId
                ORDER BY t.created_at DESC" ;
        $tasks   =  DB::select(DB::raw($sql));
        $data['tasks'] = [];
        if (!empty($tasks)) {
            foreach ($tasks as $key => $task) {
                $data['tasks'][$task->id]['id'] = $task->id ;
                $data['tasks'][$task->id]['title'] = $task->title ;
                $data['tasks'][$task->id]['created_at'] = strtotime($task->created_at) ;
                $data['tasks'][$task->id]['status'] = $task->status ;
                $data['tasks'][$task->id]['status_text'] = Task::statusText($task->status) ;
                $data['tasks'][$task->id]['status_color'] = Task::statusColor($task->status) ;
                $data['tasks'][$task->id]['pioritized'] = $task->pioritized ;
                $data['tasks'][$task->id]['is_issues'] = $task->is_issues ;
                $data['tasks'][$task->id]['due_dated_at'] = $task->due_dated_at ;
                $data['tasks'][$task->id]['due_dated_complete'] = $task->due_dated_complete ;
                $data['tasks'][$task->id]['domain_id'] = $task->domain_id ;
                $data['tasks'][$task->id]['start_task_at'] = $task->start_task_at ;
                $data['tasks'][$task->id]['due_date_complete_at'] = $task->due_date_complete_at ;
                $data['tasks'][$task->id]['category_id'] = $task->category_id;
                $data['tasks'][$task->id]['category_name'] = $task->category_name ;
                $data['tasks'][$task->id]['category_color'] = $task->category_color ;
                $data['tasks'][$task->id]['checklist_success'] = $task->success_checklist ;
                $data['tasks'][$task->id]['checklist_total'] = $task->total_checklist ;
            }
        }
      
        $data['tasks'] = array_values($data['tasks']);
        $data['master_status_history'] = StatusHistory::where('status', 1)->get();
        $data['master_task_category'] = TaskCategory::getTaskCategory(2) ;
       
        $data['member_task'] = Search::memberTask($domainId, '');
        return $this->respondWithItem($data);
    }
   
    public function show($domainId, $roomId, $taskId)
    {
        $data = Task::getTaskData($domainId, $taskId, 2);
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

        $task = new Task();
        $task->title = $post['title'] ;
        $task->category_id = $post['category_id'] ;
        $task->domain_id = $domainId ;
        $task->room_id = $roomId ;
        $task->status = 1 ;
        $task->created_by = $userId ;
        $task->type = 2 ;
        $task->save();

        $history = new TaskHistory();
        $history->task_id = $task->id;
        $history->domain_id = $domainId;
        $history->status = StatusHistory::getStatus('created') ;
        $history->created_at = Carbon::now() ;
        $history->created_by = $userId;
        $history->save();

        $viewer = new TaskViewer;
        $viewer->domain_id = $domainId;
        $viewer->user_id = $userId;
        $viewer->task_id = $task->id;
        $viewer->created_at = Carbon::now();
        $viewer->save();
        $data = Task::getTaskData($domainId, $task->id, 2);
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
