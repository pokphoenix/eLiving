<?php

namespace App\Http\Controllers\API\Officer;

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

    public function search(Request $request){
      
    }

    public function index($domainId){

        $data['tasks'] = Task::getTaskListData($domainId,2);
        $data['master_status_history'] = StatusHistory::where('status',1)->get();
        $data['master_task_category'] = TaskCategory::getTaskCategory(2) ;
        $data['member_task'] = Search::memberTask($domainId,'');
        return $this->respondWithItem($data);
    } 
   
    public function show($domainId,$taskId){

      
        $data = Task::getTaskData($domainId,$taskId,2);
        return $this->respondWithItem($data);
    }

   
   
}
