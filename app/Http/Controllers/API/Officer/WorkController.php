<?php

namespace App\Http\Controllers\API\Officer;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Images;
use App\Models\Master\WorkAreaType;
use App\Models\Master\WorkJobType;
use App\Models\Master\WorkPioritize;
use App\Models\Master\WorkSystemType;
use App\Models\Notification;
use App\Models\Room;
use App\Models\Search;
use App\Models\StatusHistory;
use App\Models\Work\Work;
use App\Models\Work\WorkAttach;
use App\Models\Work\WorkComment;
use App\Models\Work\WorkHistory;
use App\Models\Work\WorkMember;
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
    }

    public function search(Request $request)
    {
    }

    public function index($domainId)
    {
        if (!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError('คุณไม่สามารถดูรายการนี้ได้');
        }
        $data['works'] = Work::getWorkListData($domainId, 1);
        
        $data['master_prioritize'] = WorkPioritize::getData() ;
        $data['master_status_history'] = StatusHistory::where('status', 1)->get();
        $data['master_work_system_type'] = WorkSystemType::getData() ;
        $data['master_work_area_type'] = WorkAreaType::getData() ;
        $data['master_work_job_type'] = WorkJobType::getData() ;
        $data['member_officer'] = Search::memberTask($domainId, '');
        return $this->respondWithItem($data);
    }
    public function show($domainId, $workId)
    {
        if (!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('head.user')&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError('คุณไม่สามารถดูรายการนี้ได้');
        }


        $data = Work::getData($domainId, $workId);
        return $this->respondWithItem($data);
    }
    public function printView($domainId, $workId)
    {
        $data['work'] = Work::getWork($domainId, $workId);
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

        $work = new Work();
        $work->title = $post['title'] ;

        if ($post['start']) {
            $work->start_work_at = Carbon::now() ;
        }

        $work->domain_id = $domainId ;
        $work->room_id = 0 ;
        $work->status = 1 ;
        $work->created_by = $userId ;
        $work->save();

        $history = new WorkHistory();
        $history->work_id = $work->id;
        $history->domain_id = $domainId;
        $history->status = StatusHistory::getStatus('created') ;
        $history->created_at = Carbon::now() ;
        $history->created_by = $userId;
        $history->save();

        $viewer = new WorkViewer;
        $viewer->domain_id = $domainId;
        $viewer->user_id = $userId;
        $viewer->work_id = $work->id;
        $viewer->created_at = Carbon::now();
        $viewer->save();
        $data['work'] = Work::find($work->id);
        $data['work_id'] = $work->id;
        return $this->respondWithItem($data);
    }
    public function update(Request $request, $domainId, $workId)
    {
        $post = $request->except('api_token', '_method');
        $work = Work::find($workId)->update($post);

        if (isset($post['due_dated_at'])) {
            $this->setHistory($domainId, $workId, 10, $post) ;
        }

        if (isset($post['due_dated_complete'])) {
            $statusId = ($post['due_dated_complete']==1) ? 11 : 12 ;
            $this->setHistory($domainId, $workId, $statusId) ;
        }
        $data = Work::getData($domainId, $workId);
        $data['work_id'] = $workId;
        return $this->respondWithItem($data);
    }

    public function destroy($domainId, $workId)
    {

        $work = Work::find($workId);
        if (($work->created_by!=Auth()->user()->id)&&(!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('headuser')&&!Auth()->user()->hasRole('admin'))) {
            return $this->respondWithError('คุณไม่สามารถลบงานนี้ได้ค่ะ');
        }

        $work->delete();

        Notification::where('type', 2)->where('ref_id', $workId)->delete();

        $data['delete_work_id'] = $workId ;
        return $this->respondWithItem($data);
    }

    private function setHistory($domainId, $workId, $statusId, $data = null)
    {
        $history = new WorkHistory();
        $history->work_id = $workId;
        $history->domain_id = $domainId;
        $history->status = $statusId ;
        $history->created_at = Carbon::now() ;
        $history->created_by = Auth::user()->id;
        if ($statusId==10) {
            $history->duedate_to = Carbon::Parse($data['due_dated_at']) ;
        }

        if ($statusId==7||$statusId==8||$statusId==9) {
            $history->work_comment_id = $data['comment_id'];
        }

        if ($statusId==20||$statusId==21) {
            $history->work_attach_id = $data['attach_id'] ;
        }
        if ($statusId==22||$statusId==23) {
            $history->work_category_id = $data['category_id'] ;
        }
        if ($statusId==3||$statusId==4) {
            $history->assign_to_user_id = $data['user_id'] ;
        }
        if ($statusId==25||$statusId==26||$statusId==31) {
            $history->checklist_id = $data['checklist_id'] ;
        }

        $history->save();
    }

    public function status(Request $request, $domainId, $workId)
    {
        $post = $request->except('api_token', '_method');
        if ($post['status']==7) {
            $post['doned_at'] = Carbon::now();
        } else {
            $post['doned_at'] = null;
        }

        // if($post['status']==3){
        //     $member = WorkMember::where('work_id',$workId)->where('domain_id',$domainId)->count();
        //     if(!$member){
        //          return $this->respondWithError('กรุณามอบหมายงานให้เจ้าหน้าที่');
        //     }
        // }



        $work = Work::find($workId);

     
        if ($post['status']==3) {
            $post['requested_by'] = Auth()->user()->id;
            $post['requested_at'] = Carbon::now();
        } elseif ($post['status']==4) {
            $post['requested_by'] = null;
            $post['requested_at'] = null;
        }
        $roomId = $work->room_id;
        $work->update($post);

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

        $this->setHistory($domainId, $workId, $statusId) ;

        // if($post['status']==4){
        //      QuotationVote::where('quotation_id',$quotationId)
        //     ->where('domain_id',$domainId)
        //     ->delete();
        // }
        $work =  Work::where('id', $workId)->first() ;
        $lang = getLang();
        // if(!empty($work)){
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
                $notiMsg = $work->title.' status Done';
                $statusTxt = ($lang=='en')  ? "Done" :  "เสร็จแล้ว"  ;
                $notiStatus = 3 ;
                break;
        }

        if ($lang=='en') {
            $notiMsg = "work \"".cutStrlen($work->title, SUB_STR_MESSAGE)."\" status ".$statusTxt ;
        } else {
            $notiMsg = "แจ้งงาน \"".cutStrlen($work->title, SUB_STR_MESSAGE)."\" สถานะ  ".$statusTxt ;
        }

            //if($work->type==2){
                //--- send to owner
                 // Notification::addNotificationDirect($work->id_card,$domainId,$notiMsg,$notiStatus,2,$workId);

               

            //}

           
        // }
        
        

        $data = Work::getData($domainId, $workId);
        return $this->respondWithItem($data);
    }
    
   
    public function attachment($domainId, $workId)
    {
        $data['attachment'] = WorkAttach::where('quotation_id', $quotationId)
            ->where('domain_id', $domainId)
            ->where('company_id', $companyId)
            ->get();
        $data['work_id'] = $workId;
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

    public function attachmentStore(Request $request, $domainId, $workId)
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
                    $filesData[$key]['work_id'] = $workId ;
                    $filesData[$key]['domain_id'] = $domainId ;
                    $filesData[$key]['path'] = $files['path'][$key] ;
                    $filesData[$key]['filename'] =  $files['filename'][$key] ;
                    $filesData[$key]['created_at'] =  Carbon::now() ;
                    $filesData[$key]['created_by'] =  auth()->user()->id ;
                }

                $attach = WorkAttach::create($filesData[0]);
                $history['attach_id'] = $attach->id ;
                $this->setHistory($domainId, $workId, 20, $history) ;
            }
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }

        $data['work_attachs'] =  Work::getWorkAttach($domainId, $workId);
        $data['task_historys'] = Work::getWorkHistory($domainId, $workId);
        $data['work_id'] = $workId;
        return $this->respondWithItem($data);
    }
    public function attachmentDelete(Request $request, $domainId, $workId, $attachId)
    {
        $attach = WorkAttach::find($attachId) ;
        if (empty($attach)) {
            return $this->respondWithError('ไม่พบรูป');
        }

        if ($attach->created_by!=Auth()->user()->id) {
            return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $attach->delete();

        $data['attach_id'] = $attachId ;
        $this->setHistory($domainId, $workId, 21, $data) ;
        $data['work_attachs'] =  Work::getWorkAttach($domainId, $workId);
        $data['task_historys'] = Work::getWorkHistory($domainId, $workId);
        $data['work_id'] = $workId;
        return $this->respondWithItem($data);
    }


    public function commentStore(Request $request, $domainId, $workId)
    {
        $post = $request->except('api_token', '_method');
        $comment = new WorkComment();
        $comment->work_id = $workId ;
        $comment->domain_id = $domainId ;
        $comment->description = $post['description'] ;
        $comment->created_at = Carbon::now();
        $comment->created_by = Auth()->user()->id ;
        $comment->save();
        $data['comment_id'] = $comment->id ;

        $this->setHistory($domainId, $workId, 7, $data) ;
        $data['task_historys'] = Work::getWorkHistory($domainId, $workId);
        $data['work_comments'] = Work::getWorkComment($domainId, $workId);
        $data['work_id'] = $workId;

        $work = Work::find($workId) ;
        $txt = $post['description'];
        $notiText = Auth()->user()->first_name." ".Auth()->user()->last_name." : ".$txt." @ ".$work->title ;

        //  //--- send to viewer
        // $sql = "select distinct(id_card),noti_player_id from (
        //             select u.id_card,ud.noti_player_id
        //             from work_viewers as tv
        //             inner join users as u
        //             on tv.user_id = u.id
        //             inner join user_domains as ud
        //             on ud.id_card = u.id_card
        //             and ud.domain_id = tv.domain_id
        //             and ud.approve = 1
        //             where tv.work_id = $workId and tv.domain_id = $domainId
        //             union all
        //             select u.id_card,ud.noti_player_id
        //             from work_members as tm
        //             inner join users as u
        //             on tm.user_id = u.id
        //             inner join user_domains as ud
        //             on ud.id_card = u.id_card
        //             and ud.domain_id = tm.domain_id
        //             and ud.approve = 1
        //             where tm.work_id = $workId and tm.domain_id = $domainId
        //         ) x";
        // $query = DB::select(DB::raw($sql));
        // if(!empty($query)){
        //     Notification::addNotificationMulti($query,$domainId,$notiText,4,2,$workId,true);
        // }

        return $this->respondWithItem($data);
    }

    public function commentUpdate(Request $request, $domainId, $workId, $commentId)
    {
        $post = $request->except('api_token', '_method');
        $comment = WorkComment::find($commentId);
        if ($comment->created_by!=Auth()->user()->id) {
            return $this->respondWithError('ไม่สามารถแก้ไขข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $comment->update($post) ;
       
        $data['comment_id'] = $commentId ;
        $this->setHistory($domainId, $workId, 8, $data) ;
        $data['work_comments'] = Work::getWorkComment($domainId, $workId);
        $data['task_historys'] = Work::getWorkHistory($domainId, $workId);
        $data['work_id'] = $workId;
        return $this->respondWithItem($data);
    }
    public function commentDelete(Request $request, $domainId, $workId, $commentId)
    {
        $comment = WorkComment::find($commentId) ;
        
        if (!empty($comment)) {
            if ($comment->created_by!=Auth()->user()->id) {
                return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
            }
            $comment->delete();
        }
      

        $data['comment_id'] = $commentId ;
        $this->setHistory($domainId, $workId, 9, $data) ;
        $data['work_comments'] = Work::getWorkComment($domainId, $workId);
        $data['task_historys'] = Work::getWorkHistory($domainId, $workId);
        $data['work_id'] = $workId;
        return $this->respondWithItem($data);
    }

   

   
    public function category(Request $request, $domainId, $workId, $categoryId)
    {
        $userId = Auth()->user()->id;
        $work = Work::where('domain_id', $domainId)
        ->where('id', $workId)
        ->first();
        $his['category_id'] = $categoryId;
        if ($work->category_id==$categoryId) {
            $work->category_id = 0 ;
            $statusId = 23;
        } else {
            $work->category_id = $categoryId ;
            $statusId = 22;
        }
        $work->save();

        $this->setHistory($domainId, $workId, $statusId, $his) ;
        $data['work_lastest_category_id'] = $categoryId ;
        $data['work_id'] = $workId ;
        $data['task_category']  = WorkSystemType::find($work->category_id);
        $data['task_historys'] = Work::getWorkHistory($domainId, $workId);
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

    public function member(Request $request, $domainId, $workId, $memberId)
    {
        $userId = Auth()->user()->id;
        $memberData = User::find($memberId);
        // if(!Auth()->user()->hasRole('officer')){
        //     return $this->respondWithError('คุณไม่สามารถมอบหมายงานให้ผู้ใช้นี้ได้ค่ะ');
        // }
        if (!$this->hasRole('officer', $memberData, $domainId)) {
            return $this->respondWithError('ไม่สามารถมอบหมายงานให้ผู้ใช้นี้ได้ค่ะ');
        }

        $work = Work::find($workId);
        if (empty($work)) {
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูลนี้', 'not found data'));
        }
        if ($work->technician_by==$memberId) {
            $work->technician_by = null;
            $work->technician_at = null;
            $statusId = 4;
        } else {
            $work->technician_by = $memberId;
            $work->technician_at = Carbon::now();
            $statusId = 3;
        }
        $work->save();

        // $WorkMember = WorkMember::where('domain_id',$domainId)
        // ->where('work_id',$workId)
        // ->where('user_id',$memberId)
        // ->first();


        // if(empty($WorkMember)){
        //     $member = new WorkMember;
        //     $member->domain_id = $domainId;
        //     $member->user_id = $memberId;
        //     $member->work_id = $workId;
        //     $member->created_at = Carbon::now();
        //     $member->save();
        //     $statusId = 3;

        //     if($memberId!=$userId){
        //         $user = User::where('id',$memberId)->first();
        //         $notiMsg = "You were worked id ".$workId;
        //         $notiStatus = 4;
        //         $notiType = 2;
        //         if(!empty($user)){
        //             Notification::addNotificationDirect($user->id_card,$domainId,$notiMsg,$notiStatus,$notiType,$workId);
        //         }
        //     }
            


        // }else{
        //     $WorkMember->delete();
        //     $txt = 'delete' ;
        //     $statusId = 4;
        // }
        $his['user_id'] = $memberId ;
        $this->setHistory($domainId, $workId, $statusId, $his) ;

       

        $data = Work::getData($domainId, $workId);
        $data['work_id'] = $workId;

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
                FROM master_work_category 
                WHERE status=1 
                AND type =$type 
                AND ( $sqlCategoryWhere ) " ;
        $category = DB::select(DB::raw($sql));
        $member = Search::memberwork($domainId, $name);
        $data['work_filter_member'] = $member ;
        $data['work_filter_category'] = $category ;
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
        $data['works'] = Work::getworkListData($domainId, $post['type'], $searchQuery);
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
