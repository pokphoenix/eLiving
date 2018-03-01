<?php

namespace App\Http\Controllers\API\Main;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Notification;
use App\Models\Post\Post;
use App\Models\Post\PostAttach;
use App\Models\Post\PostBan;
use App\Models\Post\PostComment;
use App\Models\Post\PostHistory;
use App\Models\Post\PostLike;
use App\Models\Room;
use App\Models\Search;
use App\Models\StatusHistory;

use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class PostController extends ApiController
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
       
       
    }

    public function search(Request $request){
      
    }

    public function index($domainId){
        
        $data['posts'] = Post::getListData($domainId,1);
        $ban = PostBan::where('user_id',Auth()->user()->id)
                            ->where('domain_id',$domainId)
                            ->first();
        $data['can_post'] = (empty($ban)) ? "true" : "false" ;

        $sql = "select pb.*
                ,u.id as member_id
                ,CONCAT( u.first_name,' ',u.last_name) as member_name 
                ,u.first_name 
                ,u.last_name 
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                FROM post_ban as pb
                JOIN users as u 
                ON u.id = pb.user_id
                WHERE pb.domain_id = $domainId
                ORDER BY pb.created_at DESC";
        $querys   =  DB::select(DB::raw($sql));

        $data['member_baned'] =  $querys ;
        return $this->respondWithItem($data);
    } 
    public function show($domainId,$id){
        if(!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('head.user')&&!Auth()->user()->hasRole('admin')){
          return $this->respondWithError('คุณไม่สามารถดูรายการนี้ได้');
        }


        $data = Task::getTaskData($domainId,$id);
        return $this->respondWithItem($data);
    }

    public function store(Request $request,$domainId){
        $userId = Auth::user()->id ;
        $post = $request->all();

        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $insert = new Post();
        $insert->description = $post['description'] ;


        $insert->public_start_at = (isset($post['start'])&& !empty($post['start'])) ? Carbon::Parse($post['start']) : Carbon::now();
        
        if(isset($post['end'])&& !empty($post['end']) ){
            $insert->public_end_at = Carbon::Parse($post['end']) ;
        }
        if(isset($post['is_never'])&& !empty($post['is_never'])&&$post['is_never']=="true"){
            $insert->public_end_at = null ;
        }

        $insert->domain_id = $domainId ;
        $insert->status = 1 ;
        $insert->type = 1 ;
        $insert->created_by = $userId ;
        $insert->save();

        $history = new PostHistory();
        $history->post_id = $insert->id;
        $history->domain_id = $domainId;
        $history->status = StatusHistory::getStatus('created') ;
        $history->created_at = Carbon::now() ;
        $history->created_by = $userId;
        $history->save();

        $attachments = (gettype($post['file_upload'])=="string") ? (array)json_decode($post['file_upload']) : $post['file_upload']  ;
        try{
            if(count($attachments)>0){
                $files = $this->saveImage($domainId,$attachments) ;
               
                if(!$files['result']){
                    return $this->respondWithError($files['error']);
                }
                foreach ($files['path'] as $key => $i) {
                    $filesData[$key]['post_id'] = $insert->id ;
                    $filesData[$key]['domain_id'] = $domainId ;
                    $filesData[$key]['path'] = $files['path'][$key] ;
                    $filesData[$key]['filename'] =  $files['filename'][$key] ;
                    $filesData[$key]['name'] =  $files['name'][$key] ;
                    $filesData[$key]['created_at'] =  Carbon::now() ;
                    $filesData[$key]['created_by'] =  auth()->user()->id ;
                }

                PostAttach::insert($filesData);
                // $history['attach_id'] = $attach->id ;
                // $this->setHistory($domainId,$insert->id,20,$history) ;

            }

        }catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }



        $data['post'] = Post::find($insert->id);
        $data['post_id'] = $insert->id;
        return $this->respondWithItem($data);
    }  
    public function update(Request $request,$domainId,$id){
        $userId = Auth::user()->id ;
        $post = $request->all();
        $update = Post::find($id);

        if($update->type==3){
            if(($update->created_by!=Auth()->user()->id)&&!Auth()->user()->hasRole('officer')){
                return $this->respondWithError('คุณไม่สามารถแก้ไขงานนี้ได้ค่ะ');
            }
        }else{
            if(($update->created_by!=Auth()->user()->id)&&!Auth()->user()->hasRole('admin')){
                return $this->respondWithError('คุณไม่สามารถแก้ไขงานนี้ได้ค่ะ');
            }
        }
        
       
    

        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $update->public_start_at = (isset($post['start'])&& !empty($post['start'])) ? Carbon::Parse($post['start']) : Carbon::now();

        if(isset($post['end'])&&!empty($post['end']) ){
            $update->public_end_at = Carbon::Parse($post['end']) ;
        }
        if(isset($post['is_never'])&&!empty($post['is_never'])&&$post['is_never']=="true"){
            $update->public_end_at = null ;
        }

        $publicRole = "user" ;
        if(isset($post['role'])){
            $publicRole = "" ;
            foreach ($post['role'] as $key => $role) {
                $publicRole .= ",$role" ;
            }
            $publicRole = substr($publicRole, 1);

        } 
        

 

        $update->description = $post['description'] ;
        $update->prioritize = $post['prioritize'] ;
        $update->domain_id = $domainId ;
        $update->created_by = $userId ;
        $update->public_role = $publicRole ;
        $update->save();

        $history = new PostHistory();
        $history->post_id = $id;
        $history->domain_id = $domainId;
        $history->status = StatusHistory::getStatus('update') ;
        $history->created_at = Carbon::now() ;
        $history->created_by = $userId;
        $history->save();

        $attachments = (gettype($post['file_upload'])=="string") ? (array)json_decode($post['file_upload']) : $post['file_upload']  ;
        try{
            if(count($attachments)>0){
                $files = $this->saveImage($domainId,$attachments) ;
               
                if(!$files['result']){
                    return $this->respondWithError($files['error']);
                }
                foreach ($files['path'] as $key => $i) {
                    $filesData[$key]['post_id'] = $id ;
                    $filesData[$key]['domain_id'] = $domainId ;
                    $filesData[$key]['path'] = $files['path'][$key] ;
                    $filesData[$key]['filename'] =  $files['filename'][$key] ;
                    $filesData[$key]['name'] =  $files['name'][$key] ;
                    $filesData[$key]['created_at'] =  Carbon::now() ;
                    $filesData[$key]['created_by'] =  auth()->user()->id ;
                }

                PostAttach::insert($filesData);
                // $history['attach_id'] = $attach->id ;
                // $this->setHistory($domainId,$insert->id,20,$history) ;

            }

        }catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }



        $data['post'] = Post::find($id);
        $data['post_id'] = $id;


        return $this->respondWithItem($data);
    } 

    public function destroy($domainId,$id){

        $query = Post::find($id);

        if($query->type==3){
            if(($query->created_by!=Auth()->user()->id)&&!Auth()->user()->hasRole('officer')){
                return $this->respondWithError('คุณไม่สามารถลบงานนี้ได้ค่ะ');
            }
        }else{
            if(($query->created_by!=Auth()->user()->id)&&!Auth()->user()->hasRole('admin')){
                return $this->respondWithError('คุณไม่สามารถลบงานนี้ได้ค่ะ');
            }
        }
        $attachs = PostAttach::where('post_id',$id)->get();
        foreach ($attachs as $key => $a) {
            $source = base_path('public/storage/'.$a->path."/".$a->filename);
            if(file_exists($source)){
                  unlink($source);
            }
        }
        PostAttach::where('post_id',$id)->delete();

       

        $query->delete();


      
       

        $data['delete_post_id'] = $id ;
        return $this->respondWithItem($data);
    }

    private function setHistory($domainId,$id,$statusId,$data=null){
        $history = new PostHistory();
        $history->post_id = $id;
        $history->domain_id = $domainId;
        $history->status = $statusId ;
        $history->created_at = Carbon::now() ;
        $history->created_by = Auth::user()->id;
        if($statusId==10){
            $history->duedate_to = Carbon::Parse($data['due_dated_at']) ;
        }

        if ($statusId==7||$statusId==8||$statusId==9){
            $history->post_comment_id = $data['comment_id'];
        }

        if($statusId==20||$statusId==21){
            $history->post_attach_id = $data['attach_id'] ;
        }
        // if($statusId==22||$statusId==23){
        //     $history->task_category_id = $data['category_id'] ;
        // }
        if($statusId==3||$statusId==4){
            $history->assign_to_user_id = $data['user_id'] ;
        }
        // if($statusId==25||$statusId==26||$statusId==31){
        //     $history->checklist_id = $data['checklist_id'] ;
        // }

        $history->save();
    }

    public function status(Request $request,$domainId,$id){
        $post = $request->all();
        if($post['status']==7){
            $post['doned_at'] = Carbon::now();
        }else{
            $post['doned_at'] = null;
        }

        if($post['status']==3){
            $member = TaskMember::where('post_id',$id)->where('domain_id',$domainId)->count();
            if(!$member){
                 return $this->respondWithError('กรุณามอบหมายงานให้เจ้าหน้าที่');
            }
        }
        $task = Task::find($id);
        $roomId = $task->room_id;
        $task->update($post);

        switch ($post['status']) {
            case 1:$statusId = 13 ;break;
            case 2:$statusId = 14 ;break;
            case 3:$statusId = 15 ;break;
            case 4:$statusId = 16 ;break;
            case 5:$statusId = 17 ;break;
            case 6:$statusId = 18 ;break;
            case 7:$statusId = 19 ;break;
        }

        $this->setHistory($domainId,$id,$statusId) ;

        // if($post['status']==4){
        //      QuotationVote::where('quotation_id',$quotationId)
        //     ->where('domain_id',$domainId)
        //     ->delete();
        // }
        $task =  Task::where('id',$id)->first() ;
        // if(!empty($task)){
            switch ($post['status']) {
                case 1:
                    $notiMsg = $task->title.' status Re submit';
                    $notiStatus = 4 ;
                    break;
                case 2:
                    $notiMsg = $task->title.' status To do';
                    $notiStatus = 4 ;
                    break;
                case 3:
                    $notiMsg = $task->title.' status Accept';
                    $notiStatus = 4 ;
                    break;
                case 4:
                    $notiMsg = $task->title.' status Cancel';
                    $notiStatus = 1 ;
                    break;
                case 5:
                    $notiMsg = $task->title.' status In progress';
                    $notiStatus = 2 ;
                    break;
                case 6:
                    $notiMsg = $task->title.' status Pending';
                    $notiStatus = 2 ;
                    break;
                case 7:
                    $notiMsg = $task->title.' status Done';
                    $notiStatus = 3 ;
                    break;
            }
            //if($task->type==2){
                //--- send to owner
                 // Notification::addNotificationDirect($task->id_card,$domainId,$notiMsg,$notiStatus,2,$id);

               

            //}

            //--- send to viewer
            $sql = "select u.id_card,ud.noti_player_id 
                from task_viewers as tv
                inner join users as u 
                on tv.user_id = u.id 
                inner join user_domains as ud 
                on ud.id_card = u.id_card 
                and ud.domain_id = tv.domain_id 
                and ud.approve = 1
                where tv.post_id = $id and tv.domain_id = $domainId";
            $query = DB::select(DB::raw($sql));
            if(!empty($query)){
                Notification::addNotificationMulti($query,$domainId,$notiMsg,$notiStatus,2,$id);
            }
        // }
        
        

        $data = Task::getTaskData($domainId,$id);
        return $this->respondWithItem($data);
    }  
    
   
    public function attachment($domainId,$id){
        $data['attachment'] = TaskAttach::where('quotation_id',$quotationId)
            ->where('domain_id',$domainId)
            ->where('company_id',$companyId)
            ->get();
        $data['post_id'] = $id;     
        return $this->respondWithItem($data);
    }
    // public function companyAttachmentStore($domainId,$quotationId,$companyId){
    //     QuotationCompanyAttach::where('quotation_id',$quotationId)
    //         ->where('domain_id',$domainId)
    //         ->where('company_id',$companyId)
    //         ->delete();
        

    //     QuotationCompanyAttach::insert($data);
    //     return $this->respondWithItem($data);
    // }

    public function attachmentStore(Request $request,$domainId,$id){

        $post = $request->all();
        $attachments = (gettype($post['attachment'])=="string") ? (array)json_decode($post['attachment']) : $post['attachment']  ;
        try{
            if(count($attachments)>0){
                $files = $this->saveImage($domainId,$attachments) ;

                if(!$files['result']){
                    return $this->respondWithError($files['error']);
                }
                foreach ($files['path'] as $key => $i) {
                    $filesData[$key]['post_id'] = $id ;
                    $filesData[$key]['domain_id'] = $domainId ;
                    $filesData[$key]['path'] = $files['path'][$key] ;
                    $filesData[$key]['filename'] =  $files['filename'][$key] ;
                    $filesData[$key]['created_at'] =  Carbon::now() ;
                    $filesData[$key]['created_by'] =  auth()->user()->id ;
                }

                $attach = TaskAttach::create($filesData[0]);
                $history['attach_id'] = $attach->id ;
                $this->setHistory($domainId,$id,20,$history) ;

            }

        }catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }

        $data['task_attachs'] =  Task::getTaskAttach($domainId,$id);
        $data['task_historys'] = Task::getTaskHistory($domainId,$id);
        $data['post_id'] = $id;
        return $this->respondWithItem($data);
    }
    public function attachmentDelete(Request $request,$domainId,$id,$attachId){

        $attach = PostAttach::find($attachId);
        if(empty($attach)){
            return $this->respondWithError('ไม่พบรูป');
        }

        if($attach->created_by!=Auth()->user()->id&&!Auth()->user()->hasRole('admin')){
            return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $attach->delete();

        $data['attach_id'] = $attachId ;
        $this->setHistory($domainId,$id,21,$data) ;
        $data['post_id'] = $id;     
        return $this->respondWithItem($data);
    }


    public function commentStore(Request $request,$domainId,$id){
        $post = $request->all();
        $comment = new PostComment();
        $comment->post_id = $id ;
        $comment->domain_id = $domainId ;
        $comment->description = $post['description'] ;
        $comment->created_at = Carbon::now();
        $comment->created_by = Auth()->user()->id ;
        $comment->save();
       
        $data['post_comments'] = Post::getComment($domainId,$id);
        $data['post_id'] = $id;     
        return $this->respondWithItem($data);
    }

    public function commentUpdate(Request $request,$domainId,$id,$commentId){
        $post = $request->all();
        $comment = TaskComment::find($commentId);
        if($comment->created_by!=Auth()->user()->id){
            return $this->respondWithError('ไม่สามารถแก้ไขข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $comment->update($post) ;
       
        $data['comment_id'] = $commentId ;
        $this->setHistory($domainId,$id,8,$data) ;
        $data['task_comments'] = Task::getTaskComment($domainId,$id);
        $data['task_historys'] = Task::getTaskHistory($domainId,$id);
        $data['post_id'] = $id;
        return $this->respondWithItem($data);
    }
    public function commentDelete(Request $request,$domainId,$id,$commentId){
        $comment = TaskComment::find($commentId) ;
        
        if(!empty($comment)){
            if($comment->created_by!=Auth()->user()->id){
                return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
            }
            $comment->delete();
        }
      

        $data['comment_id'] = $commentId ;
        $this->setHistory($domainId,$id,9,$data) ;
        $data['task_comments'] = Task::getTaskComment($domainId,$id);
        $data['task_historys'] = Task::getTaskHistory($domainId,$id);
        $data['post_id'] = $id;
        return $this->respondWithItem($data);
    }

    public function viewer(Request $request,$domainId,$id){
        $userId = Auth()->user()->id;
        $viewer = TaskViewer::where('domain_id',$domainId)
        ->where('post_id',$id)
        ->where('user_id',$userId)
        ->first();
        if(empty($viewer)){
            $viewer = new TaskViewer;
            $viewer->domain_id = $domainId;
            $viewer->user_id = $userId;
            $viewer->post_id = $id;
            $viewer->created_at = Carbon::now();
            $viewer->save();
            $txt = 'add' ;
        }else{
            $viewer->delete();
            $txt = 'delete' ;
        }
        

        $data['viewer'] = $txt ;
        return $this->respondWithItem($data);
    } 

   
    public function category(Request $request,$domainId,$id,$categoryId){
        $userId = Auth()->user()->id;
        $task = Task::where('domain_id',$domainId)
        ->where('id',$id)
        ->first();
        $his['category_id'] = $categoryId;
        if ($task->category_id==$categoryId){
            $task->category_id = 0 ;
            $statusId = 23;
        }else{
            $task->category_id = $categoryId ;
            $statusId = 22;
        }
        $task->save();

        $this->setHistory($domainId,$id,$statusId,$his) ;
        $data['task_lastest_category_id'] = $categoryId ;
        $data['post_id'] = $id ;
        $data['task_category'] = TaskCategory::find($task->category_id);
        $data['task_historys'] = Task::getTaskHistory($domainId,$id);
        return $this->respondWithItem($data);
    }

    private function hasRole($role,$memberData,$domainId){
        $sql = "SELECT role_id 
                FROM role_user ru
                LEFT JOIN roles r 
                ON r.id = ru.role_id 
                WHERE ru.id_card = '".$memberData->id_card."' AND r.name='".$role.
                "' AND ru.domain_id=".$domainId ;
        return collect(DB::select(DB::raw($sql)))->first(); 
    }

    public function like(Request $request,$domainId,$id){
        $userId = Auth()->user()->id;
       
        $query = PostLike::where('post_id',$id)->where('user_id',$userId)->first();
        if(empty($query)){
            $query = new PostLike;
            $query->post_id = $id;
            $query->domain_id = $domainId;
            $query->user_id = $userId;
            $query->status_like = 1 ;
        }else{
            $query->status_like = ($query->status_like) ? 0 : 1 ;
        }
        $statusLike = $query->status_like ;
        $query->save();



        $cntLike = PostLike::where('post_id',$id)->where('status_like',1)->count();
        $cntComment = PostComment::where('post_id',$id)->count();

       // $this->setHistory($domainId,$id,$statusId,$his) ;

      

        // $data = Task::getTaskData($domainId,$id);
        $data['post_id'] = $id;
        $data['post_like'] = $cntLike;
        $data['post_comment'] = $cntComment;
        $data['like_status'] =  $statusLike;


        return $this->respondWithItem($data);
    } 

    public function ban(Request $request,$domainId,$id){
        $userId = Auth()->user()->id;
        // $memberData = User::find($memberId);
        // if(!Auth()->user()->hasRole('officer')){
        //     return $this->respondWithError('คุณไม่สามารถมอบหมายงานให้ผู้ใช้นี้ได้ค่ะ');
        // }
        if(!Auth()->user()->hasRole('admin')){
            return $this->respondWithError($this->langMessage('คุณไม่สามารถใช้งานระบบนี้ได้ค่ะ','no permission'));
        }
        $query = Post::find($id) ;
        if(empty($query)){
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูล','no data'));
        }
        $postBan = PostBan::where('user_id',$query->created_by)->where('domain_id',$domainId)->first();
        if(!empty($postBan)){
            return $this->respondWithItem(['text'=>'success']);
        }

        $ban = new PostBan();
        $ban->user_id = $query->created_by ;
        $ban->created_at = Carbon::now();
        $ban->created_by = Auth()->user()->id;
        $ban->domain_id = $domainId;
        $ban->save();
        return $this->respondWithItem(['text'=>'success']);
    } 
    public function unBan(Request $request,$domainId,$id){
        $userId = Auth()->user()->id;
        if(!Auth()->user()->hasRole('admin')){
            return $this->respondWithError($this->langMessage('คุณไม่สามารถใช้งานระบบนี้ได้ค่ะ','no permission'));
        }
        PostBan::where('user_id',$id)->where('domain_id',$domainId)->delete();
        return $this->respondWithItem(['text'=>'success']);
    } 

    public function checklistStore(Request $request,$domainId,$id){
        $userId = Auth()->user()->id;
        $title = $request->input("title") ;
        $checklist = new TaskChecklist() ;
        $checklist->title = $title ;
        $checklist->post_id = $id ;
        $checklist->domain_id = $domainId ;
        $checklist->created_at = Carbon::now() ;
        $checklist->created_by = $userId ;
        $checklist->save();

        $his['checklist_id'] = $checklist->id ;

        $this->setHistory($domainId,$id,25,$his) ;

        $data['task_checklists'] = Task::getTaskChecklist($domainId,$id);
        $data['task_historys'] = Task::getTaskHistory($domainId,$id);
        $data['lastest_id'] = $checklist->id ;
        $data['post_id'] = $id;
        return $this->respondWithItem($data);
    }

    public function checklistItem(Request $request,$domainId,$id,$checklistId){

        $userId = Auth()->user()->id;
        $title = $request->input("title") ;
        $checklist = new TaskChecklistItem() ;
        $checklist->title = $title ;
        $checklist->checklist_id = $checklistId ;
        $checklist->post_id = $id ;
        $checklist->domain_id = $domainId ;
        $checklist->save();

        $data['task_checklists'] = Task::getTaskChecklist($domainId,$id);
        $data['lastest_id'] = $checklistId ;
        $data['post_id'] = $id;
        return $this->respondWithItem($data);
    } 
    public function checklistItemDelete(Request $request,$domainId,$id,$checklistItemId){
        $checklist = TaskChecklistItem::find($checklistItemId);
        $checklistId = $checklist->checklist_id ;
        $checklist->delete() ;
        $data['task_checklists'] = Task::getTaskChecklist($domainId,$id);
        $data['lastest_id'] = $checklistId ;
        $data['post_id'] = $id;
        return $this->respondWithItem($data);
    } 
    public function checklistItemUpdate(Request $request,$domainId,$id,$checklistItemId){
        $post = $request->all();
        unset($post['api_token']);
        $userId = Auth()->user()->id;
        $member = TaskMember::where('domain_id',$domainId)
        ->where('post_id',$id)
        ->where('user_id',$userId)
        ->first();
        if(empty($member)){
            return $this->respondWithError('ผู้ใช้นี้ไม่มีสิทธิ์บันทึกรายการนี้ได้ค่ะ');
        }

        if(!empty($post)){
            TaskChecklistItem::find($checklistItemId)->update($post);
        }else{
            $checklist = TaskChecklistItem::find($checklistItemId);
            $checklist->status = ($checklist->status==0) ? 1 : 0 ;
            $checklist->save() ;
            $data['lastest_id'] = $checklist->checklist_id ;
        }

       
        $data['task_checklists'] = Task::getTaskChecklist($domainId,$id);
       
        $data['post_id'] = $id;
        return $this->respondWithItem($data);
    }

    public function checklistDelete(Request $request,$domainId,$id,$checklistId){
        $userId = Auth()->user()->id;
        $task = TaskChecklist::where('domain_id',$domainId)
        ->where('post_id',$id)
        ->where('id',$checklistId)
        ->delete();

        $task = TaskChecklistItem::where('domain_id',$domainId)
        ->where('post_id',$id)
        ->where('checklist_id',$checklistId)
        ->delete();

        $his['checklist_id'] = $checklistId ;
        $this->setHistory($domainId,$id,26,$his) ;


        $data['task_checklists'] = Task::getTaskChecklist($domainId,$id);
        $data['task_historys'] = Task::getTaskHistory($domainId,$id);
        $data['post_id'] = $id;
        return $this->respondWithItem($data);
    }
    public function checklistUpdate(Request $request,$domainId,$id,$checklistId){
        $post = $request->all();

        unset($post['api_token']);

        $userId = Auth()->user()->id;
        $task = TaskChecklist::where('domain_id',$domainId)
        ->where('post_id',$id)
        ->where('id',$checklistId)
        ->update($post);

        $his['checklist_id'] = $checklistId ;
        $this->setHistory($domainId,$id,31,$his) ;


        $data['task_checklists'] = Task::getTaskChecklist($domainId,$id);
        $data['task_historys'] = Task::getTaskHistory($domainId,$id);
        $data['post_id'] = $id;
        return $this->respondWithItem($data);
    } 

    public function filter(Request $request,$domainId){
        $name = $request->input('name');
        $type = $request->input('type');
        $lang = App::getLocale() ;

        if(!empty($name)){
            $name = addslashes($name);
        }

        $sqlCategorySelect = "name_en as name" ;
        $sqlCategoryWhere = "name_en like '%".$name."%'" ;
        if($lang=="th"){
            $sqlCategorySelect = "name_th as name" ;
            $sqlCategoryWhere = "name_th like '%".$name."%'" ;
        }

        $sql = "SELECT id, $sqlCategorySelect ,color
                FROM master_task_category 
                WHERE status=1 
                AND type =$type 
                AND ( $sqlCategoryWhere ) " ;
        $category = DB::select(DB::raw($sql));
        $member = Search::memberTask($domainId,$name);
        $data['task_filter_member'] = $member ; 
        $data['task_filter_category'] = $category ;
        return $this->respondWithItem($data);
    }

    public function searchFilter(Request $request,$domainId){
        $post = $request->all();
        $search = "";
        if(isset($post['no_categoty'])&&$post['no_categoty']){
            $search .=  ((!empty($search)) ? " OR " : "" )." tc.id is null " ;
        }

        if(isset($post['category'])&&!empty($post['category'])){
            $categoryList = "";
            foreach ($post['category'] as $key => $c) {
                $categoryList .= ",$c";
            }
            $categoryList = substr($categoryList,1);
            $search .= " tc.id in ($categoryList) " ;
        }

        if(isset($post['unsign'])&&$post['unsign']){
            $search .=  ((!empty($search)) ? " OR " : "" )." u.id is null " ;
        } 


        if(isset($post['member'])&&!empty($post['member'])){
            $memberList = "";
            foreach ($post['member'] as $key => $m) {
                $memberList .= ",$m";
            }
            $memberList = substr($memberList,1);
            $search .= ((!empty($search)) ? " OR " : "" )." tm.user_id in ($memberList) " ;
        }


        $searchQuery ="";
        if(!empty($search)){
            $searchQuery = " AND ($search) " ;
        }
        $data['tasks'] = Task::getTaskListData($domainId,$post['type'],$searchQuery);
        return $this->respondWithItem($data);
    }

    

    private function validator($data)
    {
        return Validator::make($data, [
            'description' => 'required|string',
        ]);
    }
    private function validatorItem($data)
    {
        return Validator::make($data, [
            'item' => 'required|string|max:255',
        ]);
    }

    private function saveImage($domainId,$files)
    {

        try {
            $result = ['result'=>true,'error'=>''];

            foreach($files as $key=>$file){
                
                if(gettype($file)=="array"){
                    $fileData = $file['data'];
                    $fileName = time().'_'.$file['name'];
                    $name = $file['name'];
                }else{
                    $fileData = $file->data ;
                    $fileName = time().'_'.$file->name;
                    $name = $file->name;
                }
                list($mime, $data)   = explode(';', $fileData);
                list(, $data)       = explode(',', $data);
                $data = base64_decode($data);
               
                $folderName = $domainId."/".date('Ym') ;
                if (!is_dir(public_path('storage/'.$folderName))) {
                    File::makeDirectory(public_path('storage/'.$folderName),0755,true);  
                }
                $savePath = public_path('storage/'.$folderName.'/').$fileName;
                file_put_contents($savePath, $data);
                $result['path'][$key] = $folderName;
                $result['filename'][$key] = $fileName;
                $result['name'][$key] = $name;

            }
        }catch (\Exception $e) {
            $result = ['result'=>false,'error'=>$e->getMessage()] ;
        }
        return $result ;
    }
}
