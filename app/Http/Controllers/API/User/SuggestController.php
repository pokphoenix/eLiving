<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Master\SuggestCategory;
use App\Models\Room;
use App\Models\Search;
use App\Models\StatusHistory;
use App\Models\Suggest\Suggest;
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
        // $this->middleware('auth:api');
    }

  

    public function index($domainId){
        $url = url('');
            $sqlLang =  (App::isLocale('en')) ? 'tc.name_en' : 'tc.name_th' ;
        $sql = "select t.*,$sqlLang as category_name 
                ,tc.color as category_color 
              
              
            
                from suggests as t 
               
                

             

                left join master_suggest_category as tc 
                on t.category_id = tc.id 
              
                WHERE t.created_by = ".Auth::user()->id."
                AND t.domain_id = $domainId
               
                ORDER BY t.created_at DESC" ;
        $query   =  DB::select(DB::raw($sql));
        $data['suggests'] = [];
        if (!empty($query )){
            foreach ($query as $key => $suggest) {
                $data['suggests'][$suggest->id]['id'] = $suggest->id ;
                $data['suggests'][$suggest->id]['title'] = $suggest->title ;
                $data['suggests'][$suggest->id]['created_at'] = strtotime($suggest->created_at) ;
                $data['suggests'][$suggest->id]['status'] = $suggest->status ;
                $data['suggests'][$suggest->id]['status_text'] = Suggest::statusText($suggest->status) ;
                $data['suggests'][$suggest->id]['status_color'] = Suggest::statusColor($suggest->status) ;
               
                $data['suggests'][$suggest->id]['domain_id'] = $suggest->domain_id ;
                $data['suggests'][$suggest->id]['start_task_at'] = $suggest->start_task_at ;
             
                $data['suggests'][$suggest->id]['category_id'] = $suggest->category_id;
                $data['suggests'][$suggest->id]['category_name'] = $suggest->category_name ;
                $data['suggests'][$suggest->id]['category_color'] = $suggest->category_color ;
              
              
            }
        }
      
        $data['suggests'] = array_values($data['suggests']);
        $data['master_status_history'] = StatusHistory::where('status',1)->get();
        $data['master_suggest_category'] = SuggestCategory::getCategory() ;
       
        return $this->respondWithItem($data);
    } 
   
    public function show($domainId,$suggestId){
        $data = Suggest::getSuggestData($domainId,$suggestId,2);
        return $this->respondWithItem($data);
    }

    public function store(Request $request,$domainId){
        $userId = Auth::user()->id ;
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $suggest = new Suggest();
        $suggest->title = $post['title'] ;
        $suggest->category_id = $post['category_id'] ;
        $suggest->domain_id = $domainId ;
        $suggest->status = 1 ;
        $suggest->created_by = $userId ;

        $suggest->save();

     
        $data = Suggest::getSuggestData($domainId,$suggest->id,2);
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
