<?php

namespace App\Models\Quotation;

use App\Models\Company;
use App\Models\Notification;
use App\Models\Quotation\QuotationVoteSetting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class QuotationVote extends Model
{

    

    protected $table = 'quotation_vote';
    public $timestamps = false;

    protected $fillable = ['quotation_id','domain_id', 'user_id','company_id','created_at'];
    
    public static function upSert($data)
    {
        $sql = "INSERT INTO quotation_item (id,quotation_id,domain_id, name,amount) VALUES " ;

        foreach ($data as $key => $d) {
            $sql .= "(" ;
            $sql .= ($d['id']=="0"||$d['id']==0) ? "NULL" : $d['id'] ;
            $sql .= ",".$d['quotation_id'].
                    ",".$d['domain_id'].
                    ",'".$d['name']."'".
                    ",".$d['amount'].")," ;
        }
        $sql = substr($sql, 0, -1) ;
        $sql .= " ON DUPLICATE KEY UPDATE
				id = VALUES(id)
				,quotation_id = VALUES(quotation_id)
				,domain_id = VALUES(domain_id)
				,name = VALUES(name)
				,amount = VALUES(amount)";
        $query = DB::insert(DB::raw($sql));
    }

    public static function checkVoted($quotationId, $domainId)
    {
        $vote =  QuotationVote::where('quotation_id', $quotationId)
            ->where('domain_id', $domainId)
            ->where('user_id', Auth()->user()->id)
            ->first();
        return (!empty($vote)) ? $vote->company_id : false ;
    }

    public static function totalVoter($domainId)
    {
        $totalVoter = 0 ;
        $query = QuotationVoteSetting::where('domain_id', $domainId)->where('is_auto', 0)->first();

        if (empty($query)) {
            $sql   = "SELECT COUNT(*) as cnt_total 
						FROM role_user ru
						JOIN users u
						ON u.id_card = ru.id_card
						WHERE ru.role_id = 3 
						AND ru.domain_id = $domainId";
            $query = collect(DB::select(DB::raw($sql)))->first();
            $totalVoter = $query->cnt_total ;
        } else {
            $totalVoter = $query->board_count ;
        }
        return $totalVoter ;
    }

    public static function calculateVoted($quotationId, $domainId)
    {
        //var_dump("expression");die;
        // ดึงข้อมูลผลโหวต
        // echo "quotationId: $quotationId <BR>";
        // echo "domainId: $domainId <BR>";
        //  DB::enableQueryLog();
 
        $sql   =  "SELECT a.company_id,COUNT(a.company_id) as vote_count 
				   FROM 
					(
						SELECT company_id FROM quotation_vote 
					    WHERE quotation_id = $quotationId
						AND domain_id = $domainId
						AND user_id in (SELECT u.id
						FROM role_user ru
						JOIN users u
						ON u.id_card = ru.id_card
						WHERE ru.role_id = 3 
						AND ru.domain_id = $domainId)
					    UNION ALL 
					    SELECT company_id FROM quotation_vote_instead 
					    WHERE quotation_id = $quotationId
						AND domain_id = $domainId
					) a
					GROUP BY a.company_id " ;
        $vote = DB::select(DB::raw($sql)) ;
        // var_dump($vote);die;
        // $vote =  QuotationVote::where('quotation_id',$quotationId)
     //        ->where('domain_id',$domainId)
     //        ->select('company_id', DB::raw('count(*) as vote_count'))
     //        ->groupBy('company_id')
     //        ->get();

        //     var_dump(DB::getQueryLog());die;

        // var_dump($vote->toArray());
        // ดึงข้อมูลกรรมการว่ามีกี่คน
        $totalVoter = self::totalVoter($domainId);

        // var_dump($totalVoter);

        $maxPercent = 0 ;
        $currentVotedCompanyId = 0;
        $summaryVoter = 0 ;
        $checkDraw = true ;
        $novoteCnt = 0;
        $voterVoted = $totalVoter;
        foreach ($vote as $key => $v) {
            // echo "summaryVoter : $summaryVoter<BR>";

            $summaryVoter+= $v->vote_count ;
            if ($v->company_id==0) {
                $novoteCnt = $v->vote_count ;
                $voterVoted -=  $novoteCnt;
                continue ;
            }

            //---  ถ้า vote % มากกว่าค่าที่เก็บไว้ แสดงว่า มากสุด
            $currentPercent = round((($v->vote_count*100)/$voterVoted), 2) ;

            // $array[$key]['percent'] = $currentPercent ;
            // $array[$key]['company_id'] = $v->company_id ;

            // echo "vote_count : ".$v->vote_count."<BR>";
            // echo "voterVoted : $voterVoted<BR>";
            // echo "currentPercent : $currentPercent<BR>";
            if ($currentPercent > $maxPercent) {
                $checkDraw = false ;
                $currentVotedCompanyId = $v->company_id ;
                $maxPercent = $currentPercent ;
            } elseif ($currentPercent == $maxPercent) {
                $checkDraw = true ;
                $minPercent = $currentPercent ;
            }
        }
        
        $winnerId = 0 ;
        // echo "totalVoter : $totalVoter<BR>";
        // echo "summaryVoter : $summaryVoter<BR>";
        // echo "maxPercent : $maxPercent<BR>";
        // echo "currentVotedCompanyId : $currentVotedCompanyId<BR>";
        // die;
        //--- ถ้าโหวตครบทุกคน   หรือ ผลโหวตมากกว่า 50 %  กดหนดผลโหวตที่ชนะ
        $sendNoti = false;

         
        //-- ถ้าโหวตไม่ครบ แต่เสียงชนะขาด เป็น โหวตเสร็จสิ้น
        if (($summaryVoter*100)/$totalVoter > 50 && $maxPercent > 50) {
            $quotation = Quotation::where('id', $quotationId)->first();
            $quotation->vote_winner = $currentVotedCompanyId ;
            $quotation->status = 3 ;
            $quotation->voting_at = Carbon::now();
            $quotation->save();
            $winnerId = $currentVotedCompanyId ;
            $sendNoti = true;
        } elseif ((($summaryVoter==$totalVoter) && !$checkDraw )) {
            //--- ถ้าโหวตครบ เอาเสียงที่ มากสุด
            $quotation = Quotation::where('id', $quotationId)->first();
            $quotation->vote_winner = $currentVotedCompanyId ;
            $quotation->status = 3 ;
            $quotation->voting_at = Carbon::now();
            $quotation->save();
            $winnerId = $currentVotedCompanyId ;
            $sendNoti = true;
        } elseif (($summaryVoter==$totalVoter)) {
            //--- โหวตครบแต่ตัดสินค่าไม่ได้
            $quotation = Quotation::where('id', $quotationId)->first();
            $quotation->vote_winner = null ;
            $quotation->status = 3 ;
            $quotation->save();
            $sendNoti = true;
        }

        if ($sendNoti) {
            //--- send to viewer
            $sql = "select distinct(id_card),noti_player_id,noti_player_id_mobile from (
                        select ru.id_card,ud.noti_player_id,ud.noti_player_id_mobile  from 
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
                Notification::addNotificationMulti($query, $domainId, $quotation->title.' status Voted', 4, 4, $quotationId);
            }
        }

        return $winnerId ;
    }
}
