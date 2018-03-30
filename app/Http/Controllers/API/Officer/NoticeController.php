<?php

namespace App\Http\Controllers\API\Officer;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Images;
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

class NoticeController extends ApiController
{
    private $type = 3 ;

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
        $likeRoleQuery = "";
        $searchQuery = "";
        // $sql = "SELECT r.name as role_name
        //         FROM role_user  ru
        //         JOIN roles r ON r.id=ru.role_id
        //         WHERE ru.id_card = '".Auth()->user()->id_card."' AND ru.domain_id=".$domainId ;
        // $roles = DB::select(DB::raw($sql));
        // if (!empty($roles)){
        //     $roleQuery = "";
        //     foreach ($roles as $key => $r) {
        //         $roleQuery .= " or p.public_role like '%".$r->role_name."%' ";
        //     }
        //     $roleQuery = substr($roleQuery,3);
        //     $likeRoleQuery .= " AND ( $roleQuery  ) ";
        // }

        // $searchQuery = "AND (now() BETWEEN  p.public_start_at  AND p.public_end_at  OR now() >  p.public_start_at )  ".$likeRoleQuery ;
        $data['posts'] = Post::getListData($domainId, $this->type, $searchQuery);
     
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

        $insert = new Post();
        $insert->description = $post['description'] ;
        $insert->prioritize = $post['prioritize'] ;

        $insert->public_start_at = (isset($post['start'])&& !empty($post['start'])) ? Carbon::Parse($post['start']) : Carbon::now();
        
        if (isset($post['end'])&& !empty($post['end'])) {
            $insert->public_end_at = Carbon::Parse($post['end']) ;
        }
        if (isset($post['is_never'])&& !empty($post['is_never'])&&$post['is_never']=="true") {
            $insert->public_end_at = null ;
        }
        $publicRole = "user" ;
        if (isset($post['role'])) {
            $publicRole = "" ;
            foreach ($post['role'] as $key => $role) {
                $publicRole .= ",$role" ;
            }
            $publicRole = substr($publicRole, 1);
        }
        


        $insert->domain_id = $domainId ;
        $insert->status = 1 ;
        $insert->type = $this->type ;
        $insert->created_by = $userId ;
        $insert->public_role = $publicRole ;
        $insert->save();

        $history = new PostHistory();
        $history->post_id = $insert->id;
        $history->domain_id = $domainId;
        $history->status = StatusHistory::getStatus('created') ;
        $history->created_at = Carbon::now() ;
        $history->created_by = $userId;
        $history->save();

        $attachments = (gettype($post['file_upload'])=="string") ? (array)json_decode($post['file_upload']) : $post['file_upload']  ;
        try {
            if (count($attachments)>0) {
                $files = $this->saveImage($domainId, $attachments) ;
               
                if (!$files['result']) {
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
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }



        $data['post'] = Post::find($insert->id);
        $data['post_id'] = $insert->id;
        return $this->respondWithItem($data);
    }

    public function edit(Request $request, $domainId, $id)
    {
        $searchQuery = " AND p.id=$id " ;
        $query = Post::getListData($domainId, $this->type, $searchQuery);

        $data['post'] = (!empty($query)) ? $query[0] :  $query ;
        return $this->respondWithItem($data);
    }
    
    private function setHistory($domainId, $id, $statusId, $data = null)
    {
        $history = new PostHistory();
        $history->post_id = $id;
        $history->domain_id = $domainId;
        $history->status = $statusId ;
        $history->created_at = Carbon::now() ;
        $history->created_by = Auth::user()->id;
        if ($statusId==10) {
            $history->duedate_to = Carbon::Parse($data['due_dated_at']) ;
        }

        if ($statusId==7||$statusId==8||$statusId==9) {
            $history->post_comment_id = $data['comment_id'];
        }

        if ($statusId==20||$statusId==21) {
            $history->post_attach_id = $data['attach_id'] ;
        }
        // if($statusId==22||$statusId==23){
        //     $history->task_category_id = $data['category_id'] ;
        // }
        if ($statusId==3||$statusId==4) {
            $history->assign_to_user_id = $data['user_id'] ;
        }
        // if($statusId==25||$statusId==26||$statusId==31){
        //     $history->checklist_id = $data['checklist_id'] ;
        // }

        $history->save();
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
                    $name = $file['name'];
                } else {
                    $fileData = $file->data ;
                    $fileName = time().'_'.$file->name;
                    $name = $file->name;
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
                $result['name'][$key] = $name;
            }
        } catch (\Exception $e) {
            $result = ['result'=>false,'error'=>$e->getMessage()] ;
        }
        return $result ;
    }
}
