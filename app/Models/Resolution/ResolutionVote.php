<?php

namespace App\Models\Resolution;



use App\Models\Company;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ResolutionVote extends Model
{

	

    protected $table = 'resolution_vote';
    public $timestamps = false;

    protected $fillable = ['resolution_id','domain_id', 'user_id','item_id','created_at'];
   	
    public static function upSert($data){
		$sql = "INSERT INTO resolution_item (id,resolution_id,domain_id, name,amount) VALUES " ;

		foreach ($data as $key => $d) {
			$sql .= "(" ;
			$sql .= ($d['id']=="0"||$d['id']==0) ? "NULL" : $d['id'] ;
			$sql .= ",".$d['resolution_id'].
					",".$d['domain_id'].
					",'".$d['name']."'".
					",".$d['amount'].")," ;
		}
		$sql = substr($sql, 0,-1) ;
		$sql .= " ON DUPLICATE KEY UPDATE
				id = VALUES(id)
				,resolution_id = VALUES(resolution_id)
				,domain_id = VALUES(domain_id)
				,name = VALUES(name)
				,amount = VALUES(amount)";
		$query = DB::insert(DB::raw($sql));
    }

    public static function checkVoted($Id,$domainId){
    	$vote =  ResolutionVote::where('resolution_id',$Id)
            ->where('domain_id',$domainId)
            ->where('user_id',Auth()->user()->id)
            ->first();
        return (!empty($vote)) ? $vote->item_id : "false" ;
    }

    public static function calculateVoted($Id,$domainId){
    	//var_dump("expression");die;
    	// ดึงข้อมูลผลโหวต
    	// echo "quotationId: $quotationId <BR>";
    	// echo "domainId: $domainId <BR>";
    	//  DB::enableQueryLog();
 
    	$vote =  ResolutionVote::where('resolution_id',$Id)
            ->where('domain_id',$domainId)
            ->select('item_id', DB::raw('count(*) as vote_count'))
            ->groupBy('item_id')
            ->get();

        //     var_dump(DB::getQueryLog());die;

        // var_dump($vote->toArray());
        // ดึงข้อมูลกรรมการว่ามีกี่คน
		$sql   = "SELECT COUNT(*) as cnt_total 
				FROM role_user ru
				JOIN users u
				ON u.id_card = ru.id_card
				WHERE ru.role_id = 3 
				AND ru.domain_id = $domainId";
		$query = collect(DB::select(DB::raw($sql)))->first();
		$totalVoter = $query->cnt_total ;

		$maxPercent = 0 ;
		$currentVotedItemId = 0;
		$summaryVoter = 0 ;
		$checkDraw = true ;
		foreach ($vote as $key => $v) {
			$summaryVoter++ ;
			if($v->item_id==0){
				continue ;
			}

			//---  ถ้า vote % มากกว่าค่าที่เก็บไว้ แสดงว่า มากสุด
			$currentPercent = round((($v->vote_count*100)/$totalVoter),2) ;

			if( $currentPercent > $maxPercent ){
				$currentVotedItemId = $v->item_id ; 
				$maxPercent = $currentPercent ;
			// }elseif( $currentPercent == $maxPercent ){
			// 	$checkDraw = true ;
			// 	$minPercent = $currentPercent ;
			}
			
		}
		$winnerId = 0 ;
		// echo "totalVoter : $totalVoter<BR>";
		// echo "summaryVoter : $summaryVoter<BR>";
		// echo "maxPercent : $maxPercent<BR>";
		// echo "currentVotedCompanyId : $currentVotedItemId<BR>";
		// die;
		//--- ถ้าโหวตครบทุกคน   หรือ ผลโหวตมากกว่า 50 %  กดหนดผลโหวตที่ชนะ
		$sendNoti = false;
		if($maxPercent > 50){
			$query = Resolution::where('id',$Id)->first();
			$query->vote_winner = $currentVotedItemId ;
			$query->status = 3 ;   
			$query->voting_at = Carbon::now();  
			$query->save();
			$winnerId = $currentVotedItemId ;
			$sendNoti = true;
		}elseif(($summaryVoter==$totalVoter) && ($maxPercent <= 50)){
			$query = Resolution::where('id',$Id)->first();
			$query->vote_winner = null ;
			$query->status = 3 ;   
			$query->save();
			$sendNoti = true;
		}

		if($sendNoti){
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
            if(!empty($query)){
            	$resolution = Resolution::find($Id);
                Notification::addNotificationMulti($query,$domainId,$resolution->title.' status Voted',4,5,$Id);
            }
		}

		return $winnerId ;
    }
}	
