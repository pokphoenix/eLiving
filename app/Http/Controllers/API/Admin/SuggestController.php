<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Images;
use App\Models\Master\SuggestCategory;
use App\Models\Notification;
use App\Models\Room;
use App\Models\Search;
use App\Models\StatusHistory;
use App\Models\Suggest\Suggest;
use App\Models\Suggest\SuggestAttach;
use App\Models\Suggest\SuggestComment;
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

class SuggestController extends ApiController
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

        $data['tasks'] = Suggest::getSuggestListData($domainId, 1);
      
        $data['master_suggest_category'] = SuggestCategory::getCategory() ;
      
        return $this->respondWithItem($data);
    }
    public function show($domainId, $taskId)
    {
        if (!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('head.user')&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError('คุณไม่สามารถดูรายการนี้ได้');
        }


        $data = Suggest::getSuggestData($domainId, $taskId);
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
        $data['task'] = Suggest::find($task->id);
        $data['task_id'] = $task->id;
        return $this->respondWithItem($data);
    }
    public function update(Request $request, $domainId, $taskId)
    {
        $post = $request->except('api_token', '_method');
        $task = Suggest::find($taskId)->update($post);

        if (isset($post['due_dated_at'])) {
            $this->setHistory($domainId, $taskId, 10, $post) ;
        }

        if (isset($post['due_dated_complete'])) {
            $statusId = ($post['due_dated_complete']==1) ? 11 : 12 ;
            $this->setHistory($domainId, $taskId, $statusId) ;
        }
        $data = Suggest::getSuggestData($domainId, $taskId);
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }

    public function destroy($domainId, $taskId)
    {

        $task = Suggest::find($taskId);
        if (($task->created_by!=Auth()->user()->id)&&(!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('headuser')&&!Auth()->user()->hasRole('admin'))) {
            return $this->respondWithError('คุณไม่สามารถลบงานนี้ได้ค่ะ');
        }

        $task->delete();

        // Notification::where('type',2)->where('ref_id',$taskId)->delete();

        $data['delete_suggest_id'] = $taskId ;
        return $this->respondWithItem($data);
    }

    public function commentStore(Request $request, $domainId, $taskId)
    {
        $post = $request->except('api_token', '_method');
        $comment = new SuggestComment();
        $comment->suggest_id = $taskId ;
        $comment->domain_id = $domainId ;
        $comment->description = $post['description'] ;
        $comment->created_at = Carbon::now();
        $comment->created_by = Auth()->user()->id ;
        $comment->save();
        $data['comment_id'] = $comment->id ;
        // $data['task_historys'] = Suggest::getSuggestHistory($domainId,$taskId);
        $data['suggest_comments'] = Suggest::getSuggestComment($domainId, $taskId);
        $data['suggest_id'] = $taskId;
        $task = Suggest::find($taskId) ;
        $txt = $post['description'];
        $notiText = Auth()->user()->first_name." ".Auth()->user()->last_name." : ".$txt." @ ".$task->title ;
        return $this->respondWithItem($data);
    }

    public function commentUpdate(Request $request, $domainId, $taskId, $commentId)
    {
        $post = $request->except('api_token', '_method');
        $comment = SuggestComment::find($commentId);
        if ($comment->created_by!=Auth()->user()->id) {
            return $this->respondWithError('ไม่สามารถแก้ไขข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $comment->update($post) ;
        $data['comment_id'] = $commentId ;
        $data['suggest_comments'] = Suggest::getSuggestComment($domainId, $taskId);
        $data['suggest_id'] = $taskId;
        return $this->respondWithItem($data);
    }
    public function commentDelete(Request $request, $domainId, $taskId, $commentId)
    {
        $comment = SuggestComment::find($commentId) ;
        
        if (!empty($comment)) {
            if ($comment->created_by!=Auth()->user()->id&&!Auth()->user()->hasRole('admin')) {
                return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
            }
            $comment->delete();
        }
        $data['comment_id'] = $commentId ;
        $data['suggest_comments'] = Suggest::getSuggestComment($domainId, $taskId);
        $data['suggest_id'] = $taskId;
        return $this->respondWithItem($data);
    }

    public function attachment($domainId, $taskId)
    {
        $data['attachment'] = SuggestAttach::where('quotation_id', $quotationId)
            ->where('domain_id', $domainId)
            ->where('company_id', $companyId)
            ->get();
        $data['task_id'] = $taskId;
        return $this->respondWithItem($data);
    }
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
                    $filesData[$key]['suggest_id'] = $taskId ;
                    $filesData[$key]['domain_id'] = $domainId ;
                    $filesData[$key]['path'] = $files['path'][$key] ;
                    $filesData[$key]['filename'] =  $files['filename'][$key] ;
                    $filesData[$key]['created_at'] =  Carbon::now() ;
                    $filesData[$key]['created_by'] =  auth()->user()->id ;
                }

                $attach = SuggestAttach::create($filesData[0]);
                $history['attach_id'] = $attach->id ;
            }
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }

        $data['suggest_attachs'] =  Suggest::getSuggestAttach($domainId, $taskId);
       
        $data['suggest_id'] = $taskId;
        return $this->respondWithItem($data);
    }
    public function attachmentDelete(Request $request, $domainId, $taskId, $attachId)
    {
        $attach = SuggestAttach::find($attachId) ;
        if (empty($attach)) {
            return $this->respondWithError('ไม่พบรูป');
        }
        if ($attach->created_by!=Auth()->user()->id&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $attach->delete();
        $data['attach_id'] = $attachId ;
        $data['suggest_attachs'] =  Suggest::getSuggestAttach($domainId, $taskId);
        $data['suggest_id'] = $taskId;
        return $this->respondWithItem($data);
    }

    public function status(Request $request, $domainId, $taskId)
    {
        if (!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError($this->langMessage('ไม่สามารถใช้งานระบบนี้ได้', 'Not Permission'));
        }
        $post = $request->except('api_token', '_method');
        if ($post['status']==7) {
            $post['doned_at'] = Carbon::now();
        } else {
            $post['doned_at'] = null;
        }
        $task = Suggest::find($taskId);
        $roomId = $task->room_id;
        $task->update($post);
        $data = Suggest::getSuggestData($domainId, $taskId);
        return $this->respondWithItem($data);
    }
    
    public function category(Request $request, $domainId, $taskId, $categoryId)
    {
        $userId = Auth()->user()->id;
        $task = Suggest::where('domain_id', $domainId)
        ->where('id', $taskId)
        ->first();
        $his['category_id'] = $categoryId;
        if ($task->category_id==$categoryId) {
            $task->category_id = 0 ;
        } else {
            $task->category_id = $categoryId ;
        }
        $task->save();

      
        $data['suggest_lastest_category_id'] = $categoryId ;
        $data['suggest_id'] = $taskId ;
        $data['suggest_category'] = SuggestCategory::find($task->category_id);
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

   

    public function filter(Request $request, $domainId)
    {
        $name = $request->input('name');
       
        $lang = getLang() ;

        if (!empty($name)) {
            $name = addslashes($name);
        }

        $sqlCategorySelect = "name_$lang as name" ;
        $sqlCategoryWhere = "name_$lang like '%".$name."%'" ;

        $sql = "SELECT id, $sqlCategorySelect ,color
                FROM master_suggest_category 
                WHERE status=1 
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
        $data['tasks'] = Suggest::getSuggestListData($domainId, $post['type'], $searchQuery);
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
