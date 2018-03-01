<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Images;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\Search;
use App\Models\StatusHistory;
use App\Models\Task\Task;
use App\Models\Task\TaskCategory;
use App\Models\Task\TaskHistory;
use App\Models\Task\TaskViewer;
use App\Models\User\UserHistoryEmail;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ProfileController extends ApiController
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

  

    public function index($domainId,$roomId){
        $url = url('');
        $sql = "select t.*,tc.name_en as category_name 
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
              
                WHERE t.created_by = ".Auth::user()->id."
                AND t.type = 2
                AND t.domain_id = $domainId
                AND t.room_id = $roomId
                ORDER BY created_at DESC" ;
        $tasks   =  DB::select(DB::raw($sql));
        $data['tasks'] = [];
        if (!empty($tasks )){
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
        $data['master_status_history'] = StatusHistory::where('status',1)->get();
        $data['master_task_category'] = TaskCategory::where('status',1)->where('type',2)->get();
        $data['member_task'] = Search::memberTask($domainId,'');
        return $this->respondWithItem($data);
    } 
   
    public function show(){
        $data['user'] = auth()->user()->getProfile() ;
        $domainId = auth()->user()->recent_domain;
        $idcard = auth()->user()->id_card;
        $sql2 = "SELECT count(*) as cnt
                FROM  user_address 
                WHERE id_card = '".$idcard."' AND domain_id=$domainId" ;
        $data['address'] = DB::select(DB::raw($sql2))[0]->cnt; 

        $sql2 = "SELECT count(*) as cnt
                FROM  user_images 
                WHERE id_card = '".$idcard."' AND domain_id=$domainId" ;
        $data['attachment'] = DB::select(DB::raw($sql2))[0]->cnt ; 

        $sql2 = "SELECT  count(r.id) as cnt
                FROM  user_rooms ur
                JOIN rooms r
                ON ur.room_id = r.id
                WHERE ur.id_card = '".$idcard."' AND r.domain_id=$domainId" ;
        $data['room'] = DB::select(DB::raw($sql2))[0]->cnt ; 
        return $this->respondWithItem($data);
    }

    public function store(Request $request,$domainId,$roomId){
        $userId = Auth::user()->id ;
        $post = $request->all();
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
        $data = Task::getTaskData($domainId,$task->id,2);
        return $this->respondWithItem($data);
    }  
    
    public function update(Request $request)
    {
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $userId =  auth()->user()->id ;

        $repeat = User::where('id_card',$post['id_card'])->where('id','<>',$userId)->first();
        if(!empty($repeat)){
            return $this->respondWithError('เลขบัตรประชาชนซ้ำกับผู้อื่นค่ะ');
        }

        $emailOld = auth()->user()->email ;
        $emailNew = $post['email'] ;
        if($emailOld!=$emailNew) {
            $history = new UserHistoryEmail();
            $history->created_at = Carbon::now();
            $history->created_by = $userId;
            $history->email_old = $emailOld ;
            $history->email = $emailNew ;
            $history->save();
        }

        $user = User::find($userId);


        $user->fill($post)->save(); 

        $domainId = auth()->user()->recent_domain ;
        $idcard = auth()->user()->id_card ;
        $sql = "SELECT *
                FROM  user_domains 
                WHERE id_card = $idcard AND domain_id=".$domainId ;
        $userDomain = collect(DB::select(DB::raw($sql)))->first();
        if($userDomain->approve!=1){
            $user->joinDomain( $domainId ,3); //สถานะเป็น Re submit
        }

       

        return $this->respondWithItem($user);
    }

     public function changePassUpdate(Request $request)
    {
        $post = $request->all();
        $validator = $this->validatorPassword($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        
        if($post['old_password']==$post['new_password']){
            return $this->respondWithError('cannot use same password');
        }

        $userId = auth()->user()->id ;
        $hashedPassword = User::find($userId)->password;
        if (!Hash::check($post['old_password'], $hashedPassword)) {
            return $this->respondWithError('old password invalid');
        }
        $save['password'] = bcrypt($post['new_password']);
        $user = User::find($userId)->update($save);
        return $this->respondWithItem($user);
    }

    // public function address(Request $request)
    // {
    //     $idcard = auth()->user()->id_card ;
    //     $domainId = auth()->user()->recent_domain ;
    //     $data['user_address'] =  User::getAddressList($domainId,$idcard);
    //     return $this->respondWithItem($data);
    // }

    //  public function addressStore(Request $request)
    // {
    //     $post = $request->all();
    //     var_dump($post);die;
    //     return $this->respondWithItem($data);
    // }

    public function room(Request $request)
    {
        $idcard = auth()->user()->id_card ;
        $data['room_user'] = RoomUser::from('user_rooms as ru')
                    ->join('rooms as r','r.id','=','ru.room_id')  
                    ->where('ru.id_card',$idcard)
                    ->select(DB::raw( "ru.*,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as text_name" ))
                    ->get();
        return $this->respondWithItem($data);
    }

    public function roomUpdate(Request $request)
    {
        $post = $request->all();
        if(isset($post['user-room'])){
            User::userAddRoom($post,auth()->user()->id_card);
        }
        $data['text'] = 'success' ;
        return $this->respondWithItem($data);
    }  
    public function avatar(Request $request)
    {
        $post = $request->all();
        unset($post['api_token']);
        $validator = $this->validatorAvatar($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $userId = auth()->user()->id ;

        $user = User::where('id',$userId)->first() ;
        $user->fill($post)->save();
        $data['text'] = 'success' ;
        return $this->respondWithItem($data);
    }
    public function uploadProfileImg(Request $request)
    {
        $post = $request->all();
        unset($post['api_token']);
        $validator = $this->validatorAvatar($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $uploadImg = Images::uploadImage($request,Auth()->user()->recent_domain,false,true);
        if(!$uploadImg['result']){
            return $this->respondWithError($uploadImg['error']);
        }
        if(isset($uploadImg)&&isset($uploadImg['file'])){
            if(is_array($uploadImg['file'])){
                foreach ($uploadImg['file'] as $key => $v) {
                    $post['profile_url'] = url('public/profile/'.$v['fileName']);
                }
            }
        }


       

        // $uploadImg = $this->uploadImg($request,Auth()->user()->recent_domain);
        // if(!$uploadImg['result']){
        //     return $this->respondWithError($uploadImg['error']);
        // }
        // if(isset($uploadImg)&&isset($uploadImg['imageName'])){
        //    if(is_array($uploadImg['imageName'])){
        //         foreach ($uploadImg['imageName'] as $key => $v) {
        //            $post['profile_url'] = url('public/storage/upload/'.$uploadImg['imagePath'][$key].$uploadImg['imageName'][$key]);


        //         }
        //     }else{
        //         $post['profile_url'] = url('public/storage/upload/'.$uploadImg['imagePath'].$uploadImg['imageName']);
        //     }

        // }
        unset($post['doc_file']);
        $userId = auth()->user()->id ;
        $user = User::where('id',$userId)->first() ;
        $user->fill($post)->save();
        $data['text'] = 'success' ;
        return $this->respondWithItem($data);
    }
    

    private function validator($data)
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'id_card' => 'required|string|min:13|max:13',
        ]);
    } 
    private function validatorPassword($data)
    {
        return Validator::make($data, [
            'old_password' => 'required|string|min:5|max:40|',
            'new_password' => 'required|string|min:5|max:40|confirmed',
        ]);
    }
    private function validatorAvatar($data)
    {
        return Validator::make($data, [
            'avatar_id' => 'numeric'
        ]);
    }
    
    private function uploadImg($request,$domainId){
        return  uploadfile($request,'doc_file',$domainId,['w'=>150,'h'=>150]) ;
    }
}
