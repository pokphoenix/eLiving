<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Address;
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

class AttachmentController extends ApiController
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

  

    public function index(){
        $idcard = auth()->user()->id_card ;
        $domainId = auth()->user()->recent_domain ;
        $data['docs'] = User::getImage($domainId,$idcard);
        return $this->respondWithItem($data);
    } 
   
   
    public function store(Request $request){
        $post = $request->all();

        $userId = auth()->user()->id;
        $idcard = auth()->user()->id_card;
        $domainId = auth()->user()->recent_domain;

        $post['file-type'] = (gettype($post['file-type'])=="string") ? (array)json_decode($post['file-type']) : $post['file-type'] ;

        $uploadImg = Images::uploadImage($request,$domainId);
        if(!$uploadImg['result']){
            return $this->respondWithError($uploadImg['error']);
        }
        if(isset($uploadImg)&&isset($uploadImg['file'])){
            if(is_array($uploadImg['file'])){
                foreach ($uploadImg['file'] as $key => $v) {
                    $img['id_card']  =  $idcard  ;
                    $img['domain_id']  =  $domainId ;
                    $img['path'] = $v['filePath'];
                    $img['image'] = $v['fileName'];
                    $img['file_name'] = $v['fileDisplayName'];
                    $img['file_code'] = $v['fileID'];
                    $img['file_extension'] = $v['fileExtension'];
                    $img['type'] = $post['file-type'][$key];
                    $img['created_at'] = Carbon::now();
                    Images::create($img);
                }
            }
        }

        $data['docs'] = User::getImage($domainId,$idcard);
        return $this->respondWithItem($data);
    }  

    

    public function destroy($id)
    {
        $file = Images::find($id) ;
        if(isset($file->file_code)){
            Images::deleteRealImage($file->file_code) ;
        }

        $source = public_path('upload/'.$file->path."/".$file->image);
        if(file_exists($source)){
            unlink($source);
        }
        $file->delete();
        $domainId = auth()->user()->recent_domain;
        $idcard = auth()->user()->id_card;
        $data['docs'] = User::getImage($domainId,$idcard);
        return $this->respondWithItem($data);
    }

    
    

   
    
}
