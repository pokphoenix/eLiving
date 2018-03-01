<?php

namespace App\Models\Quotation;



use App\Models\Company;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class QuotationVoteInstead extends Model
{

	

    protected $table = 'quotation_vote_instead';
    public $timestamps = false;

    protected $fillable = ['quotation_id','domain_id', 'first_name','last_name','company_id','created_at','created_by'];
   	


    public static function checkVoted($quotationId,$domainId){
    	$query = QuotationVoteSetting::where('domain_id',$domainId)->where('is_auto',0)->first();
    	if(empty($query)){
    		return false;
    	}
    	$totalCanVote = $query->board_count ;


    	$voteInsteadCnt = 0 ;
    	$voteInstead =  QuotationVoteInstead::where('quotation_id',$quotationId)
            ->where('domain_id',$domainId)
            ->select(DB::raw('count(quotation_id) as vote_cnt'))
            ->first();
        if(!empty($voteInstead))
        	$voteInsteadCnt = $voteInstead->vote_cnt;


        $userCanVote = QuotationItem::getUserCanVote($domainId,$quotationId);
        $totalUserCanVote = count($userCanVote);

        return (($totalCanVote-$totalUserCanVote) <= $voteInsteadCnt)  ? false : true ;

        // $voteCnt = 0 ;
        // $vote =  QuotationVote::where('quotation_id',$quotationId)
        //     ->where('domain_id',$domainId)
        //     ->select(DB::raw('count(quotation_id) as vote_cnt'))
        //     ->first();
        //  if(!empty($vote))
        // 	$voteCnt = $vote->vote_cnt;
        // echo "  "

        // return (($totalCanVote-$voteInsteadCnt-$voteCnt) <=0 ) ? false : true ;
    }

   
}	
