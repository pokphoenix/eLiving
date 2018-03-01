<?php

namespace App\Http\Controllers\API\Officer;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Images;
use App\Models\Notification;
use App\Models\Resolution\Resolution;
use App\Models\Resolution\ResolutionComment;
use App\Models\Resolution\ResolutionCompany;
use App\Models\Resolution\ResolutionCompanyAttach;
use App\Models\Resolution\ResolutionHistory;
use App\Models\Resolution\ResolutionItem;
use App\Models\Resolution\ResolutionItemCompany;
use App\Models\Resolution\ResolutionVote;
use App\Models\Room;
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

class ResolutionController extends ApiController
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
        $this->middleware('auth');
    }

    public function search(Request $request){
      
    }

    public function index($domainId){
        $sql = "SELECT count(ru.id_card) as CNT
                FROM role_user ru 
                JOIN users u
                ON u.id_card = ru.id_card
                WHERE ru.role_id = 3 AND ru.domain_id = $domainId
                GROUP BY ru.role_id
                ORDER BY u.id ASC" ;
        $vote   =  DB::select(DB::raw($sql));
        $totalCanVote = $vote[0]->CNT ;


        $hasHeaduser = Auth()->user()->hasRole('head.user');

        $sql = "SELECT q.* 
                ,IFNULL(t2.cnt,0) as total_vote
                ,$totalCanVote as total_can_vote";

        if($hasHeaduser){
            $sql .= " , CASE WHEN  t3.user_id is not null THEN 1 ELSE 0 END as user_has_vote  ";
        }
        $sql .= " FROM resolutions as q
                LEFT JOIN ( 
                    SELECT resolution_id,count(resolution_id) as cnt
                    FROM resolution_vote WHERE domain_id=$domainId 
                    GROUP BY resolution_id
                ) t2
                ON t2.resolution_id = q.id " ;
        if($hasHeaduser){
            $sql .= " LEFT JOIN (
                    SELECT *
                    FROM resolution_vote 
                    WHERE domain_id=1 AND user_id=".Auth()->user()->id."
                ) t3
                ON t3.resolution_id = q.id";
        }
        $sql .= " WHERE q.domain_id = $domainId
                ORDER BY CASE WHEN voting_at IS NULL THEN 1 ELSE 0 END ASC,voting_at ASC , id ASC";
        $data['resolutions']  =  DB::select(DB::raw($sql));
        $data['status_history'] = StatusHistory::where('status',1)->get();
        return $this->respondWithItem($data);
    } 
    public function data($domainId,$Id){

        $data = ResolutionItem::getItemData($domainId,$Id);
        return $this->respondWithItem($data);
    }

    public function store(Request $request,$domainId){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $query = new Resolution();
        $query->title = $post['title'] ;
        $query->domain_id = $domainId ;
        $query->status = 1 ;
        $query->save();

        $history = new ResolutionHistory();
        $history->resolution_id = $query->id;
        $history->domain_id = $domainId;
        $history->status = StatusHistory::getStatus('created') ;
        $history->created_at = Carbon::now() ;
        $history->created_by = Auth::user()->id;
        $history->save();

        return $this->respondWithItem(['resolution_id'=>$query->id]);
    }  
    public function update(Request $request,$domainId,$Id){
        $post = $request->all();
        $query = Resolution::find($Id)->update($post);
        $data = ResolutionItem::getItemData($domainId,$Id);
        return $this->respondWithItem($data);
    } 
    public function status(Request $request,$domainId,$Id){
        $post = $request->all();
        if($post['status']!=1&&$post['status']!=4){
            $qi = ResolutionItem::where('resolution_id',$Id)->first();
            if(empty($qi)){
                return $this->respondWithError('Please insert item');
            }
        }

        if($post['status']==7){
            $post['doned_at'] = Carbon::now();
        }else{
            $post['doned_at'] = null;
        }
        if($post['status']==2){
            $post['voting_at'] = Carbon::now();

        }
        $query = Resolution::find($Id)->update($post);

        if($post['status']==4||$post['status']==1){
             ResolutionVote::where('resolution_id',$Id)
            ->where('domain_id',$domainId)
            ->delete();

            $query = Resolution::where('id',$Id)
            ->where('domain_id',$domainId)
            ->first();
            $query->vote_winner =null;
            $query->voting_at =null;
            $query->save();
        }

        $notiRole = "2" ;
        switch ($post['status']) {
            case 1:
               $statusId = 19 ;
               $statusTxt = "Re submit" ;
               break;
            case 2:
               $statusTxt = "Voting" ;
               $notiRole = "2,3" ;
               break;
            case 3:
               $statusTxt = "Voted" ;
               break;
            case 4:
               $statusId = 15 ;
               $statusTxt = "Cancel" ;
               break;
            case 5:
               $statusId = 16 ;
               $statusTxt = "In progress" ;
               break;
            case 6:
               $statusId = 17 ;
               $statusTxt = "Pending" ;
               break;
            case 7:
               $statusId = 18 ;
               $statusTxt = "Done" ;
               break;
           
        }


 
        if(isset($statusId)){
            $this->setHistory($domainId,$Id,$statusId) ;
        }
        


         //--- send to viewer except myself
        $sql = "select distinct(id_card),noti_player_id from (
                    select ru.id_card,ud.noti_player_id  from 
                    role_user ru
                    inner join user_domains as ud 
                    on ud.id_card = ru.id_card 
                    and ud.domain_id = ru.domain_id 
                    and ud.approve = 1
                    where ru.role_id in ($notiRole) and ru.domain_id = $domainId
                    and ru.id_card != ".Auth()->user()->id_card."
                ) x";
        $query = DB::select(DB::raw($sql));
        if(!empty($query)){
            $resolution = Resolution::find($Id);
            Notification::addNotificationMulti($query,$domainId,$resolution->title.' status '.$statusTxt ,4,5,$Id);
        }


        $data = ResolutionItem::getItemData($domainId,$Id);
        return $this->respondWithItem($data);
    }  
    
    public function itemStore(Request $request,$domainId){
        $post = $request->all();
        $data = $post['item'] ;
        try{
            ResolutionItem::upSert($data);
        }catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
        $data = ResolutionItem::getItemData($domainId,$post['resolution_id']);
        return $this->respondWithItem($data);
    } 
    public function itemDelelete(Request $request,$domainId,$Id,$itemId){
        $post = $request->all();
        try{
            
           
            $model = ResolutionItem::where('domain_id',$domainId) 
            ->where('resolution_id',$Id)
            ->where('id',$itemId)
            ->first();
            if(!empty($model)){
                if ($model->trashed()) {
                    $model->forceDelete();
                }
                $model->delete();
            }
           

        }catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
        $data = ResolutionItem::getItemData($domainId,$Id);
        return $this->respondWithItem($data);
    } 

  
    public function voting($domainId,$Id,$itemId){

        if (ResolutionVote::checkVoted($Id,$domainId)!=="false"){
            return $this->respondWithError('คุณเคยทำการโหวตไปแล้วค่ะ');
        }
        $vote = new ResolutionVote() ;
        $vote->resolution_id = $Id;
        $vote->domain_id = $domainId;
        $vote->item_id = $itemId;
        $vote->user_id = Auth()->user()->id ;
        $vote->created_at = Carbon::now() ;
        $vote->save();
        $this->setHistory($domainId,$Id,27) ;
        $winnerId = ResolutionVote::calculateVoted($Id,$domainId);
        $data = ResolutionItem::getItemData($domainId,$Id);
        return $this->respondWithItem($data);
    }  

    public function novote($domainId,$Id){

        if (ResolutionVote::checkVoted($Id,$domainId)!=="false"){
            return $this->respondWithError('คุณเคยทำการโหวตไปแล้วค่ะ');
        }
        $vote = new ResolutionVote() ;
        $vote->resolution_id = $Id;
        $vote->domain_id = $domainId;
        $vote->item_id = 0 ;
        $vote->user_id = Auth()->user()->id ;
        $vote->created_at = Carbon::now() ;
        $vote->save();
        $this->setHistory($domainId,$Id,30) ;
        $winnerId = ResolutionVote::calculateVoted($Id,$domainId);
        $data = ResolutionItem::getItemData($domainId,$Id);
        return $this->respondWithItem($data);
    } 

    public function voteWinner($domainId,$Id,$itemId){
        $query = Resolution::where('id',$Id)
        ->where('domain_id',$domainId)
        ->where('status',3)
        ->whereNull('vote_winner')
        ->first();
        if(empty($query)){
            return $this->respondWithError('ไม่สามารถเปลี่ยนผลโหวตได้ค่ะ');
        }
        $query->vote_winner = $itemId;
        $query->save();

        $this->setHistory($domainId,$Id,29) ;

        $data = ResolutionItem::getItemData($domainId,$Id);
        return $this->respondWithItem($data);
    }

    public function changeVoted($domainId,$Id){
        $userId = auth()->user()->id;
        $vote = ResolutionVote::where('resolution_id',$Id)
        ->where('domain_id',$domainId)
        ->where('user_id',$userId)
        ->delete();
        $this->setHistory($domainId,$Id,28) ;
        //--- ถ้าอยู่ในสถานะ Voted แล้วมีการเปลี่ยนใจผลโหวต ต้องคำนวนค่าใหม่
        $query = Resolution::where('id',$Id)
        ->where('domain_id',$domainId)
        ->where('status',3)->first();
        if(!empty($query)){
            $query->vote_winner = null ;
            $query->status = 2 ;   
            $query->save();

            $sql = "select distinct(id_card),noti_player_id from (
                        select ru.id_card,ud.noti_player_id  from 
                        role_user ru
                        inner join user_domains as ud 
                        on ud.id_card = ru.id_card 
                        and ud.domain_id = ru.domain_id 
                        and ud.approve = 1
                        where ru.role_id in (2) and ru.domain_id = $domainId
                        and ru.id_card != ".Auth()->user()->id_card."
                    ) x";
            $query = DB::select(DB::raw($sql));
            if(!empty($query)){
                $resolution = Resolution::find($Id);
                Notification::addNotificationMulti($query,$domainId,$resolution->title.' status Voting',4,5,$Id);
            }
        }

        $data = ResolutionItem::getItemData($domainId,$Id);
        return $this->respondWithItem($data);
    } 



  
   

    public function commentStore(Request $request,$domainId,$Id){
        $post = $request->all();
        if($post['description']==""||$post['description']==" "){
            return $this->respondWithError('please insert comment');
        }

        $comment = new ResolutionComment();
        $comment->resolution_id = $Id ;
        $comment->domain_id = $domainId ;
        $comment->description = $post['description'] ;
        $comment->created_at = Carbon::now();
        $comment->created_by = Auth()->user()->id ;
        $comment->save();
        $data['comment_id'] = $comment->id ;

        $history = new ResolutionHistory();
        $history->resolution_id = $Id ;
        $history->domain_id = $domainId ;
        $history->status = StatusHistory::getStatus('added comment') ;
        $history->created_at = Carbon::now();
        $history->created_by = Auth()->user()->id ;
        $history->resolution_comment_id = $comment->id ;
        $history->save();
        $data['resolution_historys'] = ResolutionItem::getResolutionHistory($domainId,$Id);
        $data['resolution_comments'] = ResolutionItem::getResolutionComment($domainId,$Id);
        $data['resolution_id'] = $Id;
        

        $query = Resolution::find($Id) ;

        $txt = $post['description'] ; 

        $notiText = Auth()->user()->first_name." ".Auth()->user()->last_name." : ".$txt." @ ".$query->title ;

        $sql = "select distinct(id_card),noti_player_id from (
                        select ru.id_card,ud.noti_player_id  from 
                        role_user ru
                        inner join user_domains as ud 
                        on ud.id_card = ru.id_card 
                        and ud.domain_id = ru.domain_id 
                        and ud.approve = 1
                        where ru.role_id in (2) and ru.domain_id = $domainId
                        and ru.id_card != ".Auth()->user()->id_card."
                    ) x";
        $query = DB::select(DB::raw($sql));
        if(!empty($query)){
            
            Notification::addNotificationMulti($query,$domainId,
                 $notiText,4,5,$Id);
        }

        return $this->respondWithItem($data);
    }

    public function commentUpdate(Request $request,$domainId,$Id,$commentId){
        $post = $request->all();
        $comment = ResolutionComment::find($commentId);
        if($comment->created_by!=Auth()->user()->id){
            return $this->respondWithError('ไม่สามารถแก้ไขข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $comment->update($post) ;
       

        $history = new ResolutionHistory();
        $history->resolution_id = $Id ;
        $history->domain_id = $domainId ;
        $history->status = StatusHistory::getStatus('edit comment') ;
        $history->created_at = Carbon::now();
        $history->created_by = Auth()->user()->id ;
        $history->resolution_comment_id = $commentId ;
        $history->save();
        $data['comment_id'] = $commentId ;
        $data['resolution_historys'] = ResolutionItem::getResolutionHistory($domainId,$Id);
        $data['resolution_comments'] = ResolutionItem::getResolutionComment($domainId,$Id);
        $data['resolution_id'] = $Id;
        return $this->respondWithItem($data);
    }
    public function commentDelete(Request $request,$domainId,$Id,$commentId){
        $comment = ResolutionComment::find($commentId) ;
       
        if($comment->created_by!=Auth()->user()->id){
            return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $comment->delete();

        $history = new ResolutionHistory();
        $history->resolution_id = $Id ;
        $history->domain_id = $domainId ;
        $history->status = StatusHistory::getStatus('deleted comment') ;
        $history->created_at = Carbon::now();
        $history->created_by = Auth()->user()->id ;
        $history->resolution_comment_id = $commentId ;
        $history->save();
        $data['comment_id'] = $commentId ;
        $data['resolution_historys'] = ResolutionItem::getResolutionHistory($domainId,$Id);
        $data['resolution_comments'] = ResolutionItem::getResolutionComment($domainId,$Id);
        $data['resolution_id'] = $Id;
        return $this->respondWithItem($data);
    }

     private function setHistory($domainId,$Id,$statusId,$data=null){
        $history = new ResolutionHistory();
        $history->resolution_id = $Id;
        $history->domain_id = $domainId;
        $history->status = $statusId ;
        $history->created_at = Carbon::now() ;
        $history->created_by = Auth::user()->id;
        

        $history->save();
    }

    private function validator($data)
    {
        return Validator::make($data, [
            'title' => 'required|string|max:255|unique:resolutions,title',
        ]);
    }
    private function validatorItem($data)
    {
        return Validator::make($data, [
            'item' => 'required|string',
        ]);
    }
    private function saveImage($domainId,$files)
    {
        try {
            $result = ['result'=>true,'error'=>''];

            foreach($files as $key=>$file){
                list($mime, $data)   = explode(';', $file->data);
                list(, $data)       = explode(',', $data);
                $data = base64_decode($data);
                $fileName = time().'_'.$file->name;
                $folderName = $domainId."/".date('Ym') ;
                if (!is_dir(public_path('storage/'.$folderName))) {
                    File::makeDirectory(public_path('storage/'.$folderName),0755,true);  
                }

                $savePath = public_path('storage/'.$folderName.'/').$fileName;
                file_put_contents($savePath, $data);
                $result['path'][$key] = $folderName;
                $result['filename'][$key] = $fileName;
            }
        }catch (\Exception $e) {
            $result = ['result'=>false,'error'=>$e->getMessage()] ;
        }
        
        return $result ;
    }
}
