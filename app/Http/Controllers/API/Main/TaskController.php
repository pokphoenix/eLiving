<?php

namespace App\Http\Controllers\API\Main;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Images;
use App\Models\Notification;
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
use Illuminate\Support\Facades\Log;
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
    }

    public function search(Request $request)
    {
    }

    public function index($domainId)
    {
        if (!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('head.user')&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError('คุณไม่สามารถดูรายการนี้ได้');
        }
        $data['tasks'] = Task::getTaskListData($domainId, 1);
        $data['master_status_history'] = StatusHistory::where('status', 1)->get();


        $data['master_task_category'] = TaskCategory::getTaskCategory(1) ;
        $data['member_task'] = Search::memberTask($domainId, '');
        return $this->respondWithItem($data);
    }
    public function show($domainId, $taskId)
    {
        if (!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('head.user')&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError('คุณไม่สามารถดูรายการนี้ได้');
        }


        $data = Task::getTaskData($domainId, $taskId);
        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId)
    {
        $userId = Auth::user()->id ;
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $task = new Task();
        $task->title = $post['title'] ;

        if ($post['start']) {
            $task->start_task_at = Carbon::now() ;
        }

        $task->domain_id = $domainId ;
        $task->room_id = 0 ;
        $task->status = 1 ;
        $task->created_by = $userId ;
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
        $data['task'] = Task::find($task->id);
        $data['task_id'] = $task->id;
        return $this->respondWithItem($data);
    }
    public function update(Request $request, $domainId, $taskId)
    {
        $post = $request->except('api_token', '_method');
        $task = Task::find($taskId)->update($post);

        if (isset($post['due_dated_at'])) {
            $this->setHistory($domainId, $taskId, 10, $post) ;
        }

        if (isset($post['due_dated_complete'])) {
            $statusId = ($post['due_dated_complete']==1) ? 11 : 12 ;
            $this->setHistory($domainId, $taskId, $statusId) ;
        }
        $data = Task::getTaskData($domainId, $taskId);
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }

    public function destroy($domainId, $taskId)
    {

        $task = Task::find($taskId);
        if (($task->created_by!=Auth()->user()->id)&&(!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('headuser')&&!Auth()->user()->hasRole('admin'))) {
            return $this->respondWithError('คุณไม่สามารถลบงานนี้ได้ค่ะ');
        }

        $task->delete();

        Notification::where('type', 2)->where('ref_id', $taskId)->delete();

        $data['delete_task_id'] = $taskId ;
        return $this->respondWithItem($data);
    }

    private function setHistory($domainId, $taskId, $statusId, $data = null)
    {
        $history = new TaskHistory();
        $history->task_id = $taskId;
        $history->domain_id = $domainId;
        $history->status = $statusId ;
        $history->created_at = Carbon::now() ;
        $history->created_by = Auth::user()->id;
        if ($statusId==10) {
            $history->duedate_to = Carbon::Parse($data['due_dated_at']) ;
        }

        if ($statusId==7||$statusId==8||$statusId==9) {
            $history->task_comment_id = $data['comment_id'];
        }

        if ($statusId==20||$statusId==21) {
            $history->task_attach_id = $data['attach_id'] ;
        }
        if ($statusId==22||$statusId==23) {
            $history->task_category_id = $data['category_id'] ;
        }
        if ($statusId==3||$statusId==4) {
            $history->assign_to_user_id = $data['user_id'] ;
        }
        if ($statusId==25||$statusId==26||$statusId==31) {
            $history->checklist_id = $data['checklist_id'] ;
        }

        $history->save();
    }

    public function status(Request $request, $domainId, $taskId)
    {
        $post = $request->except('api_token', '_method');
        if ($post['status']==7) {
            $post['doned_at'] = Carbon::now();
        } else {
            $post['doned_at'] = null;
        }

        if ($post['status']==3) {
            $member = TaskMember::where('task_id', $taskId)->where('domain_id', $domainId)->count();
            if (!$member) {
                 return $this->respondWithError('กรุณามอบหมายงานให้เจ้าหน้าที่');
            }
        }
        $task = Task::find($taskId);
        $roomId = $task->room_id;
        $task->update($post);

        switch ($post['status']) {
            case 1:
                $statusId = 13 ;
                break;
            case 2:
                $statusId = 14 ;
                break;
            case 3:
                $statusId = 15 ;
                break;
            case 4:
                $statusId = 16 ;
                break;
            case 5:
                $statusId = 17 ;
                break;
            case 6:
                $statusId = 18 ;
                break;
            case 7:
                $statusId = 19 ;
                break;
        }

        $this->setHistory($domainId, $taskId, $statusId) ;

        // if($post['status']==4){
        //      QuotationVote::where('quotation_id',$quotationId)
        //     ->where('domain_id',$domainId)
        //     ->delete();
        // }
        $task =  Task::where('id', $taskId)->first() ;
        $lang= getLang();
        // if(!empty($task)){
        switch ($post['status']) {
            case 1:
                $statusTxt = ($lang=='en')  ? "Re submit" :  "รายการใหม่"  ;
                $notiStatus = 4 ;
                break;
            case 2:
                $statusTxt = ($lang=='en')  ? "Re submit" :  "รายการใหม่"  ;
                $notiStatus = 4 ;
                break;
            case 3:
                $statusTxt = ($lang=='en')  ? "Re submit" :  "รายการใหม่"  ;
                $notiStatus = 4 ;
                break;
            case 4:
                $statusTxt = ($lang=='en')  ? "Cancel" :  "ยกเลิก"  ;
                $notiStatus = 1 ;
                break;
            case 5:
                $statusTxt = ($lang=='en')  ? "In progress" :  "กำลังดำเนินการ"  ;
                $notiStatus = 2 ;
                break;
            case 6:
                $statusTxt = ($lang=='en')  ? "Pending" :  "รอดำเนินการ"  ;
                $notiStatus = 2 ;
                break;
            case 7:
                $notiMsg = $task->title.' status Done';
                $statusTxt = ($lang=='en')  ? "Done" :  "เสร็จแล้ว"  ;
                $notiStatus = 3 ;
                break;
        }

        if ($lang=='en') {
            $notiMsg = "task \"".cutStrlen($task->title, SUB_STR_MESSAGE)."\" status ".$statusTxt ;
        } else {
            $notiMsg = "แจ้งงาน \"".cutStrlen($task->title, SUB_STR_MESSAGE)."\" สถานะ  ".$statusTxt ;
        }

            //if($task->type==2){
                //--- send to owner
                 // Notification::addNotificationDirect($task->id_card,$domainId,$notiMsg,$notiStatus,2,$taskId);

               

            //}

            //--- send to viewer
            $sql = "select u.id_card,ud.noti_player_id,ud.noti_player_id_mobile 
                from task_viewers as tv
                inner join users as u 
                on tv.user_id = u.id 
                inner join user_domains as ud 
                on ud.id_card = u.id_card 
                and ud.domain_id = tv.domain_id 
                and ud.approve = 1
                where tv.task_id = $taskId and tv.domain_id = $domainId";
            $query = DB::select(DB::raw($sql));
           
        if (!empty($query)) {
            Notification::addNotificationMulti($query, $domainId, $notiMsg, $notiStatus, 2, $taskId);
        }
        // }
        
        

        $data = Task::getTaskData($domainId, $taskId);
        return $this->respondWithItem($data);
    }
    
   
    public function attachment($domainId, $taskId)
    {
        $data['attachment'] = TaskAttach::where('quotation_id', $quotationId)
            ->where('domain_id', $domainId)
            ->where('company_id', $companyId)
            ->get();
        $data['task_id'] = $taskId;
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

    public function attachmentStore(Request $request, $domainId, $taskId)
    {

        $post = $request->except('api_token', '_method');
        $attachments = (gettype($post['attachment'])=="string") ? (array)json_decode($post['attachment']) : $post['attachment']  ;
        try {
            if (count($attachments)>0) {
                $files = $this->saveImage($domainId, $attachments) ;

                if (!$files['result']) {
                    return $this->respondWithError($files['error']);
                }
                foreach ($files['path'] as $key => $i) {
                    $filesData[$key]['task_id'] = $taskId ;
                    $filesData[$key]['domain_id'] = $domainId ;
                    $filesData[$key]['path'] = $files['path'][$key] ;
                    $filesData[$key]['filename'] =  $files['filename'][$key] ;
                    $filesData[$key]['created_at'] =  Carbon::now() ;
                    $filesData[$key]['created_by'] =  auth()->user()->id ;
                }

                $attach = TaskAttach::create($filesData[0]);
                $history['attach_id'] = $attach->id ;
                $this->setHistory($domainId, $taskId, 20, $history) ;
            }
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }

        $data['task_attachs'] =  Task::getTaskAttach($domainId, $taskId);
        $data['task_historys'] = Task::getTaskHistory($domainId, $taskId);
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }
    public function attachmentDelete(Request $request, $domainId, $taskId, $attachId)
    {
        $attach = TaskAttach::find($attachId) ;
        if (empty($attach)) {
            return $this->respondWithError('ไม่พบรูป');
        }

        if ($attach->created_by!=Auth()->user()->id) {
            return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $attach->delete();

        $data['attach_id'] = $attachId ;
        $this->setHistory($domainId, $taskId, 21, $data) ;
        $data['task_attachs'] =  Task::getTaskAttach($domainId, $taskId);
        $data['task_historys'] = Task::getTaskHistory($domainId, $taskId);
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }


    public function commentStore(Request $request, $domainId, $taskId)
    {
        $post = $request->except('api_token', '_method');
        $comment = new TaskComment();
        $comment->task_id = $taskId ;
        $comment->domain_id = $domainId ;
        $comment->description = $post['description'] ;
        $comment->created_at = Carbon::now();
        $comment->created_by = Auth()->user()->id ;
        $comment->save();
        $data['comment_id'] = $comment->id ;

        $this->setHistory($domainId, $taskId, 7, $data) ;
        $data['task_historys'] = Task::getTaskHistory($domainId, $taskId);
        $data['task_comments'] = Task::getTaskComment($domainId, $taskId);
        $data['task_id'] = $taskId;

        $task = Task::find($taskId) ;
        $txt = $post['description'];
        $notiText = Auth()->user()->first_name." ".Auth()->user()->last_name." : ".$txt." @ ".$task->title ;

         //--- send to viewer
        $sql = "select distinct(id_card),noti_player_id from (
                    select u.id_card,ud.noti_player_id 
                    from task_viewers as tv
                    inner join users as u 
                    on tv.user_id = u.id 
                    inner join user_domains as ud 
                    on ud.id_card = u.id_card 
                    and ud.domain_id = tv.domain_id 
                    and ud.approve = 1
                    where tv.task_id = $taskId and tv.domain_id = $domainId
                    union all 
                    select u.id_card,ud.noti_player_id 
                    from task_members as tm
                    inner join users as u 
                    on tm.user_id = u.id 
                    inner join user_domains as ud 
                    on ud.id_card = u.id_card 
                    and ud.domain_id = tm.domain_id 
                    and ud.approve = 1
                    where tm.task_id = $taskId and tm.domain_id = $domainId
                ) x";
        $query = DB::select(DB::raw($sql));
        if (!empty($query)) {
            Notification::addNotificationMulti($query, $domainId, $notiText, 4, 2, $taskId, true);
        }

        return $this->respondWithItem($data);
    }

    public function commentUpdate(Request $request, $domainId, $taskId, $commentId)
    {
        $post = $request->except('api_token', '_method');
        $comment = TaskComment::find($commentId);
        if ($comment->created_by!=Auth()->user()->id) {
            return $this->respondWithError('ไม่สามารถแก้ไขข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $comment->update($post) ;
       
        $data['comment_id'] = $commentId ;
        $this->setHistory($domainId, $taskId, 8, $data) ;
        $data['task_comments'] = Task::getTaskComment($domainId, $taskId);
        $data['task_historys'] = Task::getTaskHistory($domainId, $taskId);
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }
    public function commentDelete(Request $request, $domainId, $taskId, $commentId)
    {
        $comment = TaskComment::find($commentId) ;
        
        if (!empty($comment)) {
            if ($comment->created_by!=Auth()->user()->id) {
                return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
            }
            $comment->delete();
        }
      

        $data['comment_id'] = $commentId ;
        $this->setHistory($domainId, $taskId, 9, $data) ;
        $data['task_comments'] = Task::getTaskComment($domainId, $taskId);
        $data['task_historys'] = Task::getTaskHistory($domainId, $taskId);
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }

    public function viewer(Request $request, $domainId, $taskId)
    {
        $userId = Auth()->user()->id;
        $viewer = TaskViewer::where('domain_id', $domainId)
        ->where('task_id', $taskId)
        ->where('user_id', $userId)
        ->first();
        if (empty($viewer)) {
            $viewer = new TaskViewer;
            $viewer->domain_id = $domainId;
            $viewer->user_id = $userId;
            $viewer->task_id = $taskId;
            $viewer->created_at = Carbon::now();
            $viewer->save();
            $txt = 'add' ;
        } else {
            $viewer->delete();
            $txt = 'delete' ;
        }
        

        $data['viewer'] = $txt ;
        return $this->respondWithItem($data);
    }

   
    public function category(Request $request, $domainId, $taskId, $categoryId)
    {
        $userId = Auth()->user()->id;
        $task = Task::where('domain_id', $domainId)
        ->where('id', $taskId)
        ->first();
        $his['category_id'] = $categoryId;
        if ($task->category_id==$categoryId) {
            $task->category_id = 0 ;
            $statusId = 23;
        } else {
            $task->category_id = $categoryId ;
            $statusId = 22;
        }
        $task->save();

        $this->setHistory($domainId, $taskId, $statusId, $his) ;
        $data['task_lastest_category_id'] = $categoryId ;
        $data['task_id'] = $taskId ;
        $data['task_category'] = TaskCategory::find($task->category_id);
        $data['task_historys'] = Task::getTaskHistory($domainId, $taskId);
        return $this->respondWithItem($data);
    }

    private function hasRole($role, $memberData, $domainId)
    {
        $sql = "SELECT role_id 
                FROM role_user ru
                LEFT JOIN roles r 
                ON r.id = ru.role_id 
                WHERE ru.id_card = '".$memberData->id_card."' AND r.name='".$role.
                "' AND ru.domain_id=".$domainId ;
        return collect(DB::select(DB::raw($sql)))->first();
    }

    public function member(Request $request, $domainId, $taskId, $memberId)
    {
        $userId = Auth()->user()->id;
        $memberData = User::find($memberId);
        // if(!Auth()->user()->hasRole('officer')){
        //     return $this->respondWithError('คุณไม่สามารถมอบหมายงานให้ผู้ใช้นี้ได้ค่ะ');
        // }
        if (!$this->hasRole('officer', $memberData, $domainId)&&!$this->hasRole('head.user', $memberData, $domainId)) {
            return $this->respondWithError('ไม่สามารถมอบหมายงานให้ผู้ใช้นี้ได้ค่ะ');
        }

        $taskMember = TaskMember::where('domain_id', $domainId)
        ->where('task_id', $taskId)
        ->where('user_id', $memberId)
        ->first();


        if (empty($taskMember)) {
            $member = new TaskMember;
            $member->domain_id = $domainId;
            $member->user_id = $memberId;
            $member->task_id = $taskId;
            $member->created_at = Carbon::now();
            $member->save();
            $statusId = 3;

            if ($memberId!=$userId) {
                $user = User::where('id', $memberId)->first();
                $notiMsg = "You were tasked id ".$taskId;
                $notiStatus = 4;
                $notiType = 2;
                if (!empty($user)) {
                    Notification::addNotificationDirect($user->id_card, $domainId, $notiMsg, $notiStatus, $notiType, $taskId);
                }
            }
        } else {
            $taskMember->delete();
            $txt = 'delete' ;
            $statusId = 4;
        }
        $his['user_id'] = $memberId ;
        $this->setHistory($domainId, $taskId, $statusId, $his) ;

       

        $data = Task::getTaskData($domainId, $taskId);
        $data['task_id'] = $taskId;

        return $this->respondWithItem($data);
    }

    public function checklistStore(Request $request, $domainId, $taskId)
    {
        $userId = Auth()->user()->id;
        $title = $request->input("title") ;
        $checklist = new TaskChecklist() ;
        $checklist->title = $title ;
        $checklist->task_id = $taskId ;
        $checklist->domain_id = $domainId ;
        $checklist->created_at = Carbon::now() ;
        $checklist->created_by = $userId ;
        $checklist->save();

        $his['checklist_id'] = $checklist->id ;

        $this->setHistory($domainId, $taskId, 25, $his) ;

        $data['task_checklists'] = Task::getTaskChecklist($domainId, $taskId);
        $data['task_historys'] = Task::getTaskHistory($domainId, $taskId);
        $data['lastest_id'] = $checklist->id ;
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }

    public function checklistItem(Request $request, $domainId, $taskId, $checklistId)
    {

        $userId = Auth()->user()->id;
        $title = $request->input("title") ;
        $checklist = new TaskChecklistItem() ;
        $checklist->title = $title ;
        $checklist->checklist_id = $checklistId ;
        $checklist->task_id = $taskId ;
        $checklist->domain_id = $domainId ;
        $checklist->save();

        $data['task_checklists'] = Task::getTaskChecklist($domainId, $taskId);
        $data['lastest_id'] = $checklistId ;
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }
    public function checklistItemDelete(Request $request, $domainId, $taskId, $checklistItemId)
    {
        $checklist = TaskChecklistItem::find($checklistItemId);
        $checklistId = $checklist->checklist_id ;
        $checklist->delete() ;
        $data['task_checklists'] = Task::getTaskChecklist($domainId, $taskId);
        $data['lastest_id'] = $checklistId ;
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }
    public function checklistItemUpdate(Request $request, $domainId, $taskId, $checklistItemId)
    {
        $post = $request->except('api_token', '_method');
        unset($post['api_token']);
        $userId = Auth()->user()->id;
        $member = TaskMember::where('domain_id', $domainId)
        ->where('task_id', $taskId)
        ->where('user_id', $userId)
        ->first();
        if (empty($member)) {
            return $this->respondWithError('ผู้ใช้นี้ไม่มีสิทธิ์บันทึกรายการนี้ได้ค่ะ');
        }

        if (!empty($post)) {
            TaskChecklistItem::find($checklistItemId)->update($post);
        } else {
            $checklist = TaskChecklistItem::find($checklistItemId);
            $checklist->status = ($checklist->status==0) ? 1 : 0 ;
            $checklist->save() ;
            $data['lastest_id'] = $checklist->checklist_id ;
        }

       
        $data['task_checklists'] = Task::getTaskChecklist($domainId, $taskId);
       
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }

    public function checklistDelete(Request $request, $domainId, $taskId, $checklistId)
    {
        $userId = Auth()->user()->id;
        $task = TaskChecklist::where('domain_id', $domainId)
        ->where('task_id', $taskId)
        ->where('id', $checklistId)
        ->delete();

        $task = TaskChecklistItem::where('domain_id', $domainId)
        ->where('task_id', $taskId)
        ->where('checklist_id', $checklistId)
        ->delete();

        $his['checklist_id'] = $checklistId ;
        $this->setHistory($domainId, $taskId, 26, $his) ;


        $data['task_checklists'] = Task::getTaskChecklist($domainId, $taskId);
        $data['task_historys'] = Task::getTaskHistory($domainId, $taskId);
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }
    public function checklistUpdate(Request $request, $domainId, $taskId, $checklistId)
    {
        $post = $request->except('api_token', '_method');

        unset($post['api_token']);

        $userId = Auth()->user()->id;
        $task = TaskChecklist::where('domain_id', $domainId)
        ->where('task_id', $taskId)
        ->where('id', $checklistId)
        ->update($post);

        $his['checklist_id'] = $checklistId ;
        $this->setHistory($domainId, $taskId, 31, $his) ;


        $data['task_checklists'] = Task::getTaskChecklist($domainId, $taskId);
        $data['task_historys'] = Task::getTaskHistory($domainId, $taskId);
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }

    public function filter(Request $request, $domainId)
    {
        $name = $request->input('name');
        $type = $request->input('type');
        $lang = App::getLocale() ;

        if (!empty($name)) {
            $name = addslashes($name);
        }

        $sqlCategorySelect = "name_en as name" ;
        $sqlCategoryWhere = "name_en like '%".$name."%'" ;
        if ($lang=="th") {
            $sqlCategorySelect = "name_th as name" ;
            $sqlCategoryWhere = "name_th like '%".$name."%'" ;
        }

        $sql = "SELECT id, $sqlCategorySelect ,color
                FROM master_task_category 
                WHERE status=1 
                AND type =$type 
                AND ( $sqlCategoryWhere ) " ;
        $category = DB::select(DB::raw($sql));
        $member = Search::memberTask($domainId, $name);
        $data['task_filter_member'] = $member ;
        $data['task_filter_category'] = $category ;
        return $this->respondWithItem($data);
    }

    public function searchFilter(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $search = "";
        if (isset($post['no_categoty'])&&$post['no_categoty']) {
            $search .=  ((!empty($search)) ? " OR " : "" )." tc.id is null " ;
        }

        if (isset($post['category'])&&!empty($post['category'])) {
            $categoryList = "";
            foreach ($post['category'] as $key => $c) {
                $categoryList .= ",$c";
            }
            $categoryList = substr($categoryList, 1);
            $search .= " tc.id in ($categoryList) " ;
        }

        if (isset($post['unsign'])&&$post['unsign']) {
            $search .=  ((!empty($search)) ? " OR " : "" )." u.id is null " ;
        }


        if (isset($post['member'])&&!empty($post['member'])) {
            $memberList = "";
            foreach ($post['member'] as $key => $m) {
                $memberList .= ",$m";
            }
            $memberList = substr($memberList, 1);
            $search .= ((!empty($search)) ? " OR " : "" )." tm.user_id in ($memberList) " ;
        }


        $searchQuery ="";
        if (!empty($search)) {
            $searchQuery = " AND ($search) " ;
        }
        $data['tasks'] = Task::getTaskListData($domainId, $post['type'], $searchQuery);
        return $this->respondWithItem($data);
    }

    

    private function validator($data)
    {
        return Validator::make($data, [
            'title' => 'required|string|max:255',
        ]);
    }
    private function validatorItem($data)
    {
        return Validator::make($data, [
            'item' => 'required|string|max:255',
        ]);
    }


    

    private function saveImage($domainId, $files)
    {
        try {
            $result = ['result'=>true,'error'=>''];

            if (!Images::validateImage($files)) {
                return ['result'=>false,'error'=> getLang()=='en' ? 'file size over than 500kb' : 'ไม่สามารถอัพไฟล์ขนาดเกิน 500kb' ];
            }
            foreach ($files as $key => $file) {
                if (gettype($file)=="array") {
                    $fileData = $file['data'];
                    $fileName = time().'_'.$file['name'];
                } else {
                    $fileData = $file->data ;
                    $fileName = time().'_'.$file->name;
                }
                list($mime, $data)   = explode(';', $fileData);
                list(, $data)       = explode(',', $data);
                $data = base64_decode($data);
               
                $folderName = $domainId."/".date('Ym') ;
                if (!is_dir(public_path('storage/'.$folderName))) {
                    File::makeDirectory(public_path('storage/'.$folderName), 0755, true);
                }
                $savePath = public_path('storage/'.$folderName.'/').$fileName;
                file_put_contents($savePath, $data);
                $result['path'][$key] = $folderName;
                $result['filename'][$key] = $fileName;
            }
        } catch (\Exception $e) {
            $result = ['result'=>false,'error'=>$e->getMessage()] ;
        }
        return $result ;
    }
}
