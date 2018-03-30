<?php

namespace App\Http\Controllers\API\Officer\Purchase;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Images;
use App\Models\Notification;
use App\Models\Quotation\Quotation;
use App\Models\Quotation\QuotationComment;
use App\Models\Quotation\QuotationCompany;
use App\Models\Quotation\QuotationCompanyAttach;
use App\Models\Quotation\QuotationHistory;
use App\Models\Quotation\QuotationItem;
use App\Models\Quotation\QuotationItemCompany;
use App\Models\Quotation\QuotationSetting;
use App\Models\Quotation\QuotationVote;
use App\Models\Quotation\QuotationVoteInstead;
use App\Models\Quotation\QuotationVoteSetting;
use App\Models\Room;
use App\Models\StatusHistory;
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

class QuotationController extends ApiController
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

    public function search(Request $request)
    {
    }

    public function index($domainId)
    {


        $query = QuotationVoteSetting::where('domain_id', $domainId)->where('is_auto', 0)->first();
        if (empty($query)) {
            $sql = "SELECT count(ru.id_card) as CNT
                FROM role_user ru 
                JOIN users u
                ON u.id_card = ru.id_card
                WHERE ru.role_id = 3 AND ru.domain_id = $domainId
                GROUP BY ru.role_id
                ORDER BY u.id ASC" ;
            $vote   =  DB::select(DB::raw($sql));
            $totalCanVote = $vote[0]->CNT ;
        } else {
            $totalCanVote = $query->board_count ;
        }

        


        $hasHeaduser = Auth()->user()->hasRole('head.user');

        $sql = "SELECT q.* 
                ,IFNULL(t2.cnt,0) as total_vote
                ,$totalCanVote as total_can_vote";

        if ($hasHeaduser) {
            $sql .= " , CASE WHEN  t3.user_id is not null THEN 1 ELSE 0 END as user_has_vote  ";
        }
        $sql .= " FROM quotations as q
                LEFT JOIN ( 
                    SELECT quotation_id,count(quotation_id) as cnt
                    FROM quotation_vote WHERE domain_id=$domainId 
                    GROUP BY quotation_id
                ) t2
                ON t2.quotation_id = q.id " ;
        if ($hasHeaduser) {
            $sql .= " LEFT JOIN (
                    SELECT *
                    FROM quotation_vote 
                    WHERE domain_id=$domainId AND user_id=".Auth()->user()->id."
                ) t3
                ON t3.quotation_id = q.id";
        }
        $sql .= " WHERE q.domain_id = $domainId
                ORDER BY CASE WHEN voting_at IS NULL THEN 1 ELSE 0 END ASC,voting_at ASC , id ASC";

        $data['quotations']  =  DB::select(DB::raw($sql));
        $data['status_history'] = StatusHistory::where('status', 1)->get();
        return $this->respondWithItem($data);
    }
    public function data($domainId, $quotationId)
    {

        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }
    public function destroy($domainId, $quotationId)
    {

        $quotation = Quotation::where('id', $quotationId)->first();
        if ($quotation->created_by!= Auth()->user()->id && !Auth()->user()->hasRole('admin')) {
            return $this->respondWithError($this->langMessage('คุณไม่มีสิทธิ์ลบใบเสนอราคานี้ค่ะ', 'not permission'));
        }


        $data = QuotationComment::where('quotation_id', $quotationId)->delete();
        $data = QuotationCompany::where('quotation_id', $quotationId)->delete();
        $data = QuotationItemCompany::where('quotation_id', $quotationId)->delete();
        $data = QuotationCompanyAttach::where('quotation_id', $quotationId)->delete();
        $data = QuotationItem::where('quotation_id', $quotationId)->delete();
        $data = QuotationHistory::where('quotation_id', $quotationId)->delete();
        $data = QuotationVote::where('quotation_id', $quotationId)->delete();
        $data = Quotation::where('id', $quotationId)->delete();
        return $this->respondWithItem(['text'=>'success']);
    }

    public function store(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $quotation = new Quotation();
        $quotation->title = $post['title'] ;
        $quotation->domain_id = $domainId ;
        $quotation->status = 1 ;
        $quotation->created_by = Auth()->user()->id ;
        $quotation->save();

        $history = new QuotationHistory();
        $history->quotation_id = $quotation->id;
        $history->domain_id = $domainId;
        $history->status = StatusHistory::getStatus('created') ;
        $history->created_at = Carbon::now() ;
        $history->created_by = Auth::user()->id;
        $history->save();

        return $this->respondWithItem(['quotation_id'=>$quotation->id]);
    }
    public function update(Request $request, $domainId, $quotationId)
    {
        $post = $request->except('api_token', '_method');
        $quotation = Quotation::find($quotationId)->update($post);
        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }
    public function status(Request $request, $domainId, $quotationId)
    {
        $post = $request->except('api_token', '_method');


        $post['status'] = ( gettype($post['status'])=="string" ) ? intval($post['status']) : $post['status'] ;

        if ($post['status']!=1&&$post['status']!=4) {
            $qi = QuotationItem::where('quotation_id', $quotationId)->first();
            if (empty($qi)) {
                return $this->respondWithError('Please insert item');
            }
        }

        if ($post['status']==7) {
            $post['doned_at'] = Carbon::now();
        } else {
            $post['doned_at'] = null;
        }
        if ($post['status']==2) {
            $post['voting_at'] = Carbon::now();
        }
        $quotation = Quotation::find($quotationId)->update($post);

        if ($post['status']==4||$post['status']==1) {
             QuotationVote::where('quotation_id', $quotationId)
            ->where('domain_id', $domainId)
            ->delete();

              QuotationVoteInstead::where('quotation_id', $quotationId)
            ->where('domain_id', $domainId)
            ->delete();

            $quotation = Quotation::where('id', $quotationId)
            ->where('domain_id', $domainId)
            ->first();
            $quotation->vote_winner =null;
            $quotation->voting_at =null;
            $quotation->save();
        }

        $notiRole = "2" ;

        $lang = getLang() ;

        switch ($post['status']) {
            case 1:
                $statusId = 19 ;
                $statusTxt =    ($lang=='en')  ? "Re submit" :  "รายการใหม่"  ;
                break;
            case 2:
                $statusTxt = ($lang=='en')  ? "Voting" :  "กำลังโหวต"  ;
                $notiRole = "2,3" ;
                break;
            case 3:
                $statusTxt = ($lang=='en')  ? "Voted" :  "โหวตเสร็จแล้ว"  ;
                break;
            case 4:
                $statusId = 15 ;
                $statusTxt = ($lang=='en')  ? "Cancel" :  "ยกเลิก"  ;
                break;
            case 5:
                $statusId = 16 ;
                $statusTxt = ($lang=='en')  ? "In progress" :  "กำลังดำเนินการ"  ;
                break;
            case 6:
                $statusId = 17 ;
                $statusTxt = ($lang=='en')  ? "Pending" :  "รอดำเนินการ"  ;
                break;
            case 7:
                $statusId = 18 ;
                $statusTxt = ($lang=='en')  ? "Done" :  "เสร็จแล้ว"  ;
                break;
        }


 
        if (isset($statusId)) {
            $this->setHistory($domainId, $quotationId, $statusId) ;
        }
        


         //--- send to viewer except myself
        $sql = "select distinct(id_card),noti_player_id,noti_player_id_mobile from (
                    select ru.id_card,ud.noti_player_id,ud.noti_player_id_mobile  from 
                    role_user ru
                    inner join user_domains as ud 
                    on ud.id_card = ru.id_card 
                    and ud.domain_id = ru.domain_id 
                    and ud.approve = 1
                    where ru.role_id in ($notiRole) and ru.domain_id = $domainId
                    and ru.id_card != '".Auth()->user()->id_card."'
                ) x";
        $query = DB::select(DB::raw($sql));
        if (!empty($query)) {
            $quotation = Quotation::find($quotationId);


            if ($lang=='en') {
                $message = "quotation \"".cutStrlen($quotation->title, SUB_STR_MESSAGE)."\" status ".$statusTxt ;
            } else {
                $message = "เสนอราคา \"".cutStrlen($quotation->title, SUB_STR_MESSAGE)."\" สถานะ  ".$statusTxt ;
            }

            Notification::addNotificationMulti($query, $domainId, $message, 4, 4, $quotationId);
        }


        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }
    
    public function itemStore(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $data = $post['item'] ;
        try {
            QuotationItem::upSert($data);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
        $data = QuotationItem::getItemData($domainId, $post['quotation_id']);
        return $this->respondWithItem($data);
    }
    public function itemDelelete(Request $request, $domainId, $quotationId, $itemId)
    {
        $post = $request->except('api_token', '_method');
        try {
            $model = QuotationItemCompany::withTrashed()
            ->where('domain_id', $domainId)
            ->where('quotation_id', $quotationId)
            ->where('quotation_item_id', $itemId)
            ->first();
            if (!empty($model)) {
                if ($model->trashed()) {
                    $model->forceDelete();
                }
                $model->delete();
            }
            $model = QuotationItem::where('domain_id', $domainId)
            ->where('quotation_id', $quotationId)
            ->where('id', $itemId)
            ->first();
            if (!empty($model)) {
                if ($model->trashed()) {
                    $model->forceDelete();
                }
                $model->delete();
            }
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }

    public function companyData($domainId, $quotationId, $companyId)
    {
        $data = QuotationItem::getItemCompanyData($domainId, $quotationId, $companyId);
        return $this->respondWithItem($data);
    }
    public function voting($domainId, $quotationId, $companyId)
    {

        if (QuotationVote::checkVoted($quotationId, $domainId)!=="false") {
            return $this->respondWithError('คุณเคยทำการโหวตไปแล้วค่ะ');
        }
        $vote = new QuotationVote() ;
        $vote->quotation_id = $quotationId;
        $vote->domain_id = $domainId;
        $vote->company_id = $companyId;
        $vote->user_id = Auth()->user()->id ;
        $vote->created_at = Carbon::now() ;
        $vote->save();
        $this->setHistory($domainId, $quotationId, 27) ;
        $winnerId = QuotationVote::calculateVoted($quotationId, $domainId);
        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }



    public function novote($domainId, $quotationId)
    {

        if (QuotationVote::checkVoted($quotationId, $domainId)!=="false") {
            return $this->respondWithError('คุณเคยทำการโหวตไปแล้วค่ะ');
        }
        $vote = new QuotationVote() ;
        $vote->quotation_id = $quotationId;
        $vote->domain_id = $domainId;
        $vote->company_id = 0 ;
        $vote->user_id = Auth()->user()->id ;
        $vote->created_at = Carbon::now() ;
        $vote->save();
        $this->setHistory($domainId, $quotationId, 30) ;
        $winnerId = QuotationVote::calculateVoted($quotationId, $domainId);
        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }

    public function voteWinner($domainId, $quotationId, $companyId)
    {
        $quotation = Quotation::where('id', $quotationId)
        ->where('domain_id', $domainId)
        ->where('status', 3)
        ->whereNull('vote_winner')
        ->first();
        if (empty($quotation)) {
            return $this->respondWithError('ไม่สามารถเปลี่ยนผลโหวตได้ค่ะ');
        }
        $quotation->vote_winner = $companyId;
        $quotation->save();

        $this->setHistory($domainId, $quotationId, 29) ;

        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }

    public function changeVoted($domainId, $quotationId)
    {
        $userId = auth()->user()->id;
        $vote = QuotationVote::where('quotation_id', $quotationId)
        ->where('domain_id', $domainId)
        ->where('user_id', $userId)
        ->delete();
        $this->setHistory($domainId, $quotationId, 28) ;
        //--- ถ้าอยู่ในสถานะ Voted แล้วมีการเปลี่ยนใจผลโหวต ต้องคำนวนค่าใหม่
        $quotation = Quotation::where('id', $quotationId)
        ->where('domain_id', $domainId)
        ->where('status', 3)->first();
        if (!empty($quotation)) {
            $quotation->vote_winner = null ;
            $quotation->status = 2 ;
            $quotation->save();

            $sql = "select distinct(id_card),noti_player_id from (
                        select ru.id_card,ud.noti_player_id  from 
                        role_user ru
                        inner join user_domains as ud 
                        on ud.id_card = ru.id_card 
                        and ud.domain_id = ru.domain_id 
                        and ud.approve = 1
                        where ru.role_id in (2) and ru.domain_id = $domainId
                        and ru.id_card != '".Auth()->user()->id_card."'
                    ) x";
            $query = DB::select(DB::raw($sql));
            if (!empty($query)) {
                $quotation = Quotation::find($quotationId);
                Notification::addNotificationMulti($query, $domainId, $quotation->title.' status Voting', 4, 4, $quotationId);
            }
        }

        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }
    

    public function companyListInstead($domainId, $quotationId)
    {
        if (!QuotationVoteInstead::checkVoted($quotationId, $domainId)) {
            return $this->respondWithError($this->langMessage('โหวตครบแล้ว ไม่สามารถโหวตแทนที่ได้อีก', 'Vote Full'));
        }
        $data = QuotationCompany::companyList($domainId, $quotationId) ;

        return $this->respondWithItem($data);
    }

    public function votingInstead(Request $request, $domainId, $quotationId, $companyId)
    {
        $post = $request->except('api_token', '_method');
        if (!QuotationVoteInstead::checkVoted($quotationId, $domainId)) {
            return $this->respondWithError($this->langMessage('โหวตครบแล้ว ไม่สามารถโหวตแทนที่ได้อีก', 'Vote Full'));
        }
        $vote = new QuotationVoteInstead() ;
        $vote->quotation_id = $quotationId;
        $vote->domain_id = $domainId;
        $vote->company_id = $companyId;
        $vote->first_name = $post['first_name'] ;
        $vote->last_name = $post['last_name'] ;
        $vote->created_at = Carbon::now() ;
        $vote->created_by = Auth()->user()->id ;
        $vote->save();
        $this->setHistory($domainId, $quotationId, 27) ;
        $winnerId = QuotationVote::calculateVoted($quotationId, $domainId);
        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }
    public function votingInsteadDestroy($domainId, $quotationId, $id)
    {
        $userId = auth()->user()->id;
        $vote = QuotationVoteInstead::where('id', $id)
        ->delete();
        $this->setHistory($domainId, $quotationId, 28) ;
        //--- ถ้าอยู่ในสถานะ Voted แล้วมีการเปลี่ยนใจผลโหวต ต้องคำนวนค่าใหม่
        $quotation = Quotation::where('id', $quotationId)
        ->where('domain_id', $domainId)
        ->where('status', 3)->first();
        if (!empty($quotation)) {
            $quotation->vote_winner = null ;
            $quotation->status = 2 ;
            $quotation->save();

            $sql = "select distinct(id_card),noti_player_id from (
                        select ru.id_card,ud.noti_player_id  from 
                        role_user ru
                        inner join user_domains as ud 
                        on ud.id_card = ru.id_card 
                        and ud.domain_id = ru.domain_id 
                        and ud.approve = 1
                        where ru.role_id in (2) and ru.domain_id = $domainId
                        and ru.id_card != '".Auth()->user()->id_card."'
                    ) x";
            $query = DB::select(DB::raw($sql));
            if (!empty($query)) {
                $quotation = Quotation::find($quotationId);
                Notification::addNotificationMulti($query, $domainId, $quotation->title.' status Voting', 4, 4, $quotationId);
            }
        }

        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }


    public function companyList($domainId, $quotationId)
    {
        if (QuotationVote::checkVoted($quotationId, $domainId)!=="false") {
            return $this->respondWithError('คุณเคยทำการโหวตไปแล้วค่ะ');
        }
        $data = QuotationCompany::companyList($domainId, $quotationId) ;

        return $this->respondWithItem($data);
    }
    public function companyAttachment($domainId, $quotationId, $companyId)
    {
         $data['attachment'] =  QuotationCompanyAttach::getAttachment($domainId, $companyId, $quotationId) ;
        return $this->respondWithItem($data);
    }
    public function companyAttachmentAll($domainId, $quotationId)
    {
         $data['attachment'] =  QuotationCompanyAttach::getAttachment($domainId, null, $quotationId) ;
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
    public function companyAttachmentDelete($domainId, $quotationId, $fileCode)
    {
        $file = QuotationCompanyAttach::where('file_code', $fileCode)->first();
        if (isset($file->file_code)) {
            Images::deleteRealImage($fileCode) ;
        }

        $source = public_path('upload/'.$file->path."/".$file->image);
        if (file_exists($source)) {
            unlink($source);
        }
        QuotationCompanyAttach::where('file_code', $fileCode)->delete();

        return $this->respondWithItem(['text'=>'success']);
    }

    public function companyStore(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
 
        $attachments = (gettype($post['file_upload'])=="string") ?  (array)json_decode($post['file_upload']) : $post['file_upload'] ;
        $items = (gettype($post['item'])=="string") ?  (array)json_decode($post['item']) : $post['item'] ;
        $summary = (gettype($post['summary'])=="string") ?  (array)json_decode($post['summary']) : $post['summary'] ;
        $quotationId = $post['quotation_id'] ;
        $companyData = (gettype($post['company'])=="string") ?  (array)json_decode($post['company']) : $post['company'] ;
       
        try {
            if (empty($post['company_id'])) {   //-- new company
                $companyData['created_by'] = Auth()->user()->id ;
                $company = Company::where('name', $companyData['name'])->first();
                if (empty($company)) {
                    $company = Company::create($companyData);
                }
                $companyId = $company->id ;
            } else {
                $company = Company::where("id", $post['company_id'])->update($companyData);
                $companyId = $post['company_id'] ;
            }



            foreach ($items as $key => $item) {
                $items[$key] = (array)$items[$key];
                $items[$key]['company_id'] = $companyId ;
            }
            $quotationCompany = QuotationCompany::where("quotation_id", $quotationId)->where("company_id", $companyId)->first();
            if (empty($quotationCompany)) {
                $quotationCompany = new QuotationCompany();
            }

           
            $quotationCompany->quotation_id = $quotationId;
            $quotationCompany->company_id = $companyId ;
            $quotationCompany->price_b4_vat = $summary['price_b4_vat'] ;
            $quotationCompany->vat = $summary['vat'] ;
            $quotationCompany->price_total = $summary['price_total'] ;
            $quotationCompany->discount = $summary['discount'] ;
            $quotationCompany->price_net = $summary['price_net'] ;
            $quotationCompany->remark =  $summary['remark'] ;
            $quotationCompany->payment_term =  $summary['payment_term'] ;
            $quotationCompany->guarantee =  $summary['guarantee'] ;
            $quotationCompany->domain_id = $domainId ;
            $quotationCompany->save();



            QuotationItemCompany::upSert($items);

           


            if (count($attachments)>0) {
                QuotationCompanyAttach::where('quotation_id', $quotationId)
                ->where('domain_id', $domainId)
                ->where('company_id', $companyId)
                ->delete();

                $companyId = $post['company_id'] ;

                // $files = Images::uploadImage($request,$domainId);

                $uploadImg = Images::uploadImage($request, $domainId);
                if (!$uploadImg['result']) {
                    return $this->respondWithError($uploadImg['error']);
                }
                if (isset($uploadImg)&&isset($uploadImg['file'])) {
                    if (is_array($uploadImg['file'])) {
                        foreach ($uploadImg['file'] as $key => $v) {
                            $filesData[$key]['quotation_id'] = $quotationId ;
                            $filesData[$key]['domain_id'] = $domainId ;
                            $filesData[$key]['company_id'] = $companyId ;
                            $filesData[$key]['path'] = $v['filePath'];
                            $filesData[$key]['image'] = $v['fileName'];
                            $filesData[$key]['file_name'] = $v['fileDisplayName'];
                            $filesData[$key]['file_code'] = $v['fileID'];
                            $filesData[$key]['file_extension'] = $v['fileExtension'];
                            $filesData[$key]['file_size'] = $v['fileSize'];
                        }
                        QuotationCompanyAttach::insert($filesData);
                    }
                }




                // var_dump($files);die;
                // // $files = $this->saveImage($domainId,$attachments) ;
                // if(!$files['result']){
                //     return $this->respondWithError($files['error']);
                // }
                // foreach ($files['path'] as $key => $i) {
                //     $filesData[$key]['quotation_id'] = $quotationId ;
                //     $filesData[$key]['domain_id'] = $domainId ;
                //     $filesData[$key]['company_id'] = $companyId ;
                //     $filesData[$key]['path'] = $files['path'][$key] ;
                //     $filesData[$key]['filename'] =  $files['filename'][$key] ;
                // }
            }
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }


        

        $data = QuotationItem::getItemData($domainId, $quotationId);
        return $this->respondWithItem($data);
    }



    public function commentStore(Request $request, $domainId, $quotationId)
    {
        $post = $request->except('api_token', '_method');
        if ($post['description']==""||$post['description']==" ") {
            return $this->respondWithError('please insert comment');
        }

        $comment = new QuotationComment();
        $comment->quotation_id = $quotationId ;
        $comment->domain_id = $domainId ;
        $comment->description = $post['description'] ;
        $comment->created_at = Carbon::now();
        $comment->created_by = Auth()->user()->id ;
        $comment->save();
        $data['comment_id'] = $comment->id ;

        $history = new QuotationHistory();
        $history->quotation_id = $quotationId ;
        $history->domain_id = $domainId ;
        $history->status = StatusHistory::getStatus('added comment') ;
        $history->created_at = Carbon::now();
        $history->created_by = Auth()->user()->id ;
        $history->quotation_comment_id = $comment->id ;
        $history->save();
        $data['quotation_historys'] = QuotationItem::getQuotationHistory($domainId, $quotationId);
        $data['quotation_comments'] = QuotationItem::getQuotationComment($domainId, $quotationId);
        $data['quotation_id'] = $quotationId;
        

        $quotation = Quotation::find($quotationId) ;

        $txt = $post['description'] ;

        $notiText = Auth()->user()->first_name." ".Auth()->user()->last_name." : ".$txt." @ ".$quotation->title ;

        $sql = "select distinct(id_card),noti_player_id from (
                        select ru.id_card,ud.noti_player_id  from 
                        role_user ru
                        inner join user_domains as ud 
                        on ud.id_card = ru.id_card 
                        and ud.domain_id = ru.domain_id 
                        and ud.approve = 1
                        where ru.role_id in (2) and ru.domain_id = $domainId
                        and ru.id_card != '".Auth()->user()->id_card."'
                    ) x";
        $query = DB::select(DB::raw($sql));
        if (!empty($query)) {
            Notification::addNotificationMulti(
                $query,
                $domainId,
                $notiText,
                4,
                4,
                $quotationId
            );
        }

        return $this->respondWithItem($data);
    }

    public function commentUpdate(Request $request, $domainId, $quotationId, $commentId)
    {
        $post = $request->except('api_token', '_method');
        $comment = QuotationComment::find($commentId);
        if ($comment->created_by!=Auth()->user()->id) {
            return $this->respondWithError('ไม่สามารถแก้ไขข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $comment->update($post) ;
        // $comment->quotation_id = $quotationId ;
        // $comment->domain_id = $domainId ;
        // $comment->description = $post['description'] ;
        // $comment->created_at = Carbon::now();
        // $comment->created_by = Auth()->user()->id ;
        // $comment->save();
        // $data['comment_id'] = $comment->id ;

        $history = new QuotationHistory();
        $history->quotation_id = $quotationId ;
        $history->domain_id = $domainId ;
        $history->status = StatusHistory::getStatus('edit comment') ;
        $history->created_at = Carbon::now();
        $history->created_by = Auth()->user()->id ;
        $history->quotation_comment_id = $commentId ;
        $history->save();
        $data['comment_id'] = $commentId ;
        $data['quotation_historys'] = QuotationItem::getQuotationHistory($domainId, $quotationId);
        $data['quotation_comments'] = QuotationItem::getQuotationComment($domainId, $quotationId);
        $data['quotation_id'] = $quotationId;
        return $this->respondWithItem($data);
    }
    public function commentDelete(Request $request, $domainId, $quotationId, $commentId)
    {
        $comment = QuotationComment::find($commentId) ;
       
        if ($comment->created_by!=Auth()->user()->id) {
            return $this->respondWithError('ไม่สามารถลบข้อมูลคอมเมนท์ของผู้อื่นได้');
        }
        $comment->delete();

        $history = new QuotationHistory();
        $history->quotation_id = $quotationId ;
        $history->domain_id = $domainId ;
        $history->status = StatusHistory::getStatus('deleted comment') ;
        $history->created_at = Carbon::now();
        $history->created_by = Auth()->user()->id ;
        $history->quotation_comment_id = $commentId ;
        $history->save();
        $data['comment_id'] = $commentId ;
        $data['quotation_historys'] = QuotationItem::getQuotationHistory($domainId, $quotationId);
        $data['quotation_comments'] = QuotationItem::getQuotationComment($domainId, $quotationId);
        $data['quotation_id'] = $quotationId;
        return $this->respondWithItem($data);
    }

    public function settingGet($domainId)
    {
        $setting = QuotationSetting::where('domain_id', $domainId)->first();
        if (empty($setting)) {
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูล', 'not found data'));
        }
        $lang = GetLang();
        $data['header'] = ($lang=='en') ?  $setting->header_en : $setting->header_th ;
        $data['subject'] = ($lang=='en') ?  $setting->subject_en : $setting->subject_th ;
        $data['inform'] = ($lang=='en') ?  $setting->inform_en : $setting->inform_th ;
        $data['remark'] = ($lang=='en') ?  $setting->remark_en : $setting->remark_th ;
        $data['sign_1'] = ($lang=='en') ?  $setting->sign_1_en : $setting->sign_1_th ;
        $data['sign_2'] = ($lang=='en') ?  $setting->sign_2_en : $setting->sign_2_th ;
        $data['logo_left'] = $setting->logo_left  ;
        $data['logo_right'] = $setting->logo_right  ;
        return $this->respondWithItem($data);
    }
    public function settingEdit($domainId)
    {
        $setting = QuotationSetting::where('domain_id', $domainId)->first();
        if (empty($setting)) {
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูล', 'not found data'));
        }
        $data = $setting;
        return $this->respondWithItem($data);
    }
    public function settingUpdate(Request $request, $domainId)
    {
        $post = $request->all() ;
        unset($post["_method"]);
        unset($post["api_token"]);
        unset($post["logo_left"]);
        unset($post["logo_right"]);
        if (isset($post['hidden_logo_left'])) {
            $img = Images::upload($post['hidden_logo_left']);
            if (!$img['result']) {
                return $this->respondWithError($img['error']);
            }
            if (isset($img)&&isset($img['file'])) {
                if (is_array($img['file'])) {
                    foreach ($img['file'] as $key => $v) {
                        $post['logo_left'] =  url('public/upload/'.$v['filePath'].'/'.$v['fileName']);
                    }
                }
            }
        }
        if (isset($post['hidden_logo_right'])) {
            $img2 = Images::upload($post['hidden_logo_right']);
            if (!$img2['result']) {
                return $this->respondWithError($img2['error']);
            }
            if (isset($img2)&&isset($img2['file'])) {
                if (is_array($img2['file'])) {
                    foreach ($img2['file'] as $key => $v) {
                        $post['logo_right'] =  url('public/upload/'.$v['filePath'].'/'.$v['fileName']);
                    }
                }
            }
        }
       
        unset($post['hidden_logo_left']);
        unset($post['hidden_logo_right']);
        QuotationSetting::where('domain_id', $domainId)->update($post);
        return $this->respondWithItem(['text'=>'success']);
    }

    public function voteSettingGet($domainId)
    {
        $data = QuotationVoteSetting::where('domain_id', $domainId)->first();
        if (empty($data)) {
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูล', 'not found data'));
        }
        return $this->respondWithItem($data);
    }
   
    public function voteSettingUpdate(Request $request, $domainId)
    {
        $post = $request->all() ;

        
        if (!isset($post['is_auto'])) {
            $post['is_auto']=0;
        }


        unset($post["_method"]);
        unset($post["api_token"]);
        QuotationVoteSetting::where('domain_id', $domainId)->update($post);
        return $this->respondWithItem(['text'=>'success']);
    }

    private function setHistory($domainId, $quotationId, $statusId, $data = null)
    {
        $history = new QuotationHistory();
        $history->quotation_id = $quotationId;
        $history->domain_id = $domainId;
        $history->status = $statusId ;
        $history->created_at = Carbon::now() ;
        $history->created_by = Auth::user()->id;
        

        $history->save();
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
            'item' => 'required|string',
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
                list($mime, $data)   = explode(';', $file->data);
                list(, $data)       = explode(',', $data);
                $data = base64_decode($data);
                $fileName = time().'_'.$file->name;
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
