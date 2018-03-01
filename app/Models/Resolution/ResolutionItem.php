<?php

namespace App\Models\Resolution;



use App\Facades\Permission;
use App\Models\Company;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ResolutionItem extends Model
{

	use SoftDeletes;

    protected $table = 'resolution_item';
    public $timestamps = false;

    protected $fillable = ['resolution_id','domain_id', 'name','item_id'];
   	protected $dates = ['deleted_at'];

   	public static function getResolutionHistory($domainId,$Id){
   		$sql = "SELECT u.id as created_by_id,u.first_name,u.last_name ,h.name as history_status
				,qh.status
				, UNIX_TIMESTAMP(qh.created_at) as history_created_at
				,qc.description as comment_description
				,qc.id as comment_id
				FROM resolution_historys qh 
				LEFT JOIN master_status_history h
				ON h.id = qh.status
				LEFT JOIN users u 
				ON u.id = qh.created_by
				LEFT JOIN resolution_comments qc 
				ON qc.id = qh.resolution_comment_id
				WHERE qh.resolution_id = $Id
				AND qh.domain_id = $domainId
				ORDER BY  qh.created_at DESC" ;
		return DB::select(DB::raw($sql));
   	}

   	public static function getResolutionComment($domainId,$Id){

		$sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
               ,u.first_name
               ,u.last_name
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,tc.id as comment_id
                ,tc.description as comment_description
                ,UNIX_TIMESTAMP(tc.created_at) as ts_created_at
                FROM users u
                JOIN resolution_comments tc 
                ON tc.created_by = u.id 
                WHERE tc.domain_id =$domainId AND tc.resolution_id=$Id ORDER BY tc.created_at DESC";
        $query = DB::select(DB::raw($sql));
		foreach ($query as $key => $q) {
			$query[$key]->img = getBase64Img($q->img);
		}
        return  $query;
	}

	public static function getResolutionVote($domainId,$Id){
		$sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,qv.item_id
                FROM resolution_vote qv
                JOIN users u
                ON qv.user_id = u.id 
                WHERE qv.domain_id =$domainId AND qv.resolution_id=$Id";
        $query = DB::select(DB::raw($sql));
		foreach ($query as $key => $q) {
			$query[$key]->img = getBase64Img($q->img);
		}
        return  $query;
	}
	public static function getUserCanVote($domainId,$Id){

		$sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
               ,u.first_name
               ,u.last_name
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,CASE WHEN qv.user_id is not null THEN 1 
                ELSE 0 END as voted
                ,ri.name as vote_name
                ,qv.created_at
				FROM role_user ru 
				JOIN users u
				ON u.id_card = ru.id_card
				LEFT JOIN resolution_vote qv
				ON qv.user_id = u.id 
				AND qv.resolution_id = $Id
				LEFT JOIN  resolution_item  ri 
				ON ri.id = qv.item_id
				WHERE ru.role_id = 3 AND ru.domain_id = $domainId
				ORDER BY u.id ASC";
         $query = DB::select(DB::raw($sql));
		foreach ($query as $key => $q) {
			$query[$key]->img = getBase64Img($q->img);
		}
        return  $query;
	}
	public static function getResolutionItem($domainId,$Id){

		$sql = "SELECT ri.*, IFNULL(t2.cnt,0) as amount FROM 
				resolution_item ri
				LEFT JOIN (
					SELECT item_id,count(item_id) as cnt FROM resolution_vote   
					WHERE resolution_id = $Id
					GROUP BY item_id
				) t2
				ON t2.item_id = ri.id
				WHERE ri.resolution_id = $Id
				AND ri.domain_id=$domainId
				AND ri.deleted_at is null
				ORDER BY ri.id ASC";
        $query = DB::select(DB::raw($sql));
        return  $query;
	}

	private static function status($data){
		$appLocal = App::getLocale() ;
		switch ($data['status']) {
			case 1:
				$txt = ($appLocal=="th") ? "รายการใหม่" : "New" ;
				break;
			case 2:
				$txt = ($appLocal=="th") ? "กำลังโหวต" : "Voting" ;
				break;
			case 3: 
				$txt = ($appLocal=="th") ? "โหวตเสร็จแล้ว" : "Voted" ;
				break;
			case 4:
				$txt = ($appLocal=="th") ? "ยกเลิก" : "Cancel" ;
				break;
			case 5:
				$txt = ($appLocal=="th") ? "กำลังดำเนินการ" : "In Progress" ;
				break;
			case 6:
				$txt = ($appLocal=="th") ? "รอดำเนินการ" : "Pending" ;
				break;
			case 7:
				$txt = (($appLocal=="th") ? "เสร็จ ณ " : "Done at ").date('d M Y',strtotime($data['doned_at'])) ;
				break;
		}
		return $txt ;
	}

	public static function color($id){
        $status = self::ResolutionStatus();
        $text = '';
        foreach ($status as $s ){
            if($s['id']==$id){
                $text = $s['color'] ;
            }
        }
        return  $text ;
    }

	public static function ResolutionStatus(){
		 return [ ['id'=>1,'text'=>'New','color'=>'#d2d6de']
			,['id'=>2,'text'=>'Voting','color'=>'#e08b52']
			,['id'=>3,'text'=>'Voted','color'=>'#a7e052']
			,['id'=>4,'text'=>'Cancel','color'=>'#f56954']
			,['id'=>5,'text'=>'In Progress','color'=>'#f39c12']
			,['id'=>6,'text'=>'Pending','color'=>'#605ca8']
			,['id'=>7,'text'=>'Done','color'=>'#00a65a']
		 ] ;
	}

    public static function getItemData($domainId,$Id){
    	$data['resolution_id'] =  $Id;
    	$data['resolution'] = Resolution::where('domain_id',$domainId)->where('id',$Id)->first() ;

    	$data['resolution_items'] = self::getResolutionItem($domainId,$Id);

		$data['resolution_historys'] = self::getResolutionHistory($domainId,$Id);


		$data['resolution_comments'] = self::getResolutionComment($domainId,$Id);
		$data['resolution_votes'] = self::getResolutionVote($domainId,$Id);

		$data['resolution_user_can_vote'] = self::getUserCanVote($domainId,$Id);

		

		$hasOfficer = Auth()->user()->hasRole('officer') ;
		$hasHeaduser = Auth()->user()->hasRole('head.user') ;
		$cardStatus = $data['resolution']['status'] ;


		$data['resolution']['status_txt'] = self::status($data['resolution']);
		$data['resolution']['status_color'] = self::color($cardStatus);

		$userVote = count($data['resolution_votes']);
		$userCanVote = count($data['resolution_user_can_vote']);

		$data['status'] = [];

		$data['voted_item_id'] = ResolutionVote::checkVoted($Id,$domainId);

		

		$voted = ($data['voted_item_id']==="false") ? false : true ;
		
	
		$voting = false ;
		if($hasHeaduser&&!$voted&&($cardStatus ==2)){
			$voting = true ;
		}

		$add = false ;
		if($hasOfficer){
			$add = true ;
		}



		$resubmit = false;
		if(($cardStatus==2||$cardStatus==3||$cardStatus==4||$cardStatus==5) && $hasOfficer){
			$resubmit =true ;
		}
		
		$data['status']['btn_set_company_winner'] = $hasOfficer ? true : false ;
		$data['status']['btn_manual_voted'] = ((($userVote/$userCanVote)*100>50)&&$cardStatus ==2) ? true : false ;
		$data['status']['btn_resubmit'] = $resubmit ;
		$data['status']['voting'] = $voting ;
		$data['status']['voted'] = $voted ;
		$data['status']['change_voted'] = ($voted&&($cardStatus ==2||$cardStatus ==3)) ? true :false ;
		$data['status']['add_item'] = ($cardStatus ==1 && $add) ? true : false ;

		if($cardStatus==7){
			$data['status']['add_item'] = false;
		}


		$data['status']['cancel_vote'] =  (is_null($data['resolution']['vote_winner'])&&$add&&$cardStatus !=4) ? true : false ;
		$data['status']['winner'] =  (is_null($data['resolution']['vote_winner'])) ? false : true ;



		$data['status']['in_progress'] = ( ($cardStatus ==3||$cardStatus ==6||$cardStatus ==7 ) &&(!is_null($data['resolution']['vote_winner'])) && $hasOfficer) ? true : false ;
		$data['status']['pending'] = (($cardStatus ==3||$cardStatus ==5 )&&(!is_null($data['resolution']['vote_winner'])) && $hasOfficer) ? true : false ;
		$data['status']['done'] = (($cardStatus ==3)&&(!is_null($data['resolution']['vote_winner'])) && $hasOfficer) ? true : false ;
	
    	return $data ;


    }

   

    public static function upSert($data){
		$sql = "INSERT INTO resolution_item (id,resolution_id,domain_id, name) VALUES " ;

		foreach ($data as $key => $d) {
			$sql .= "(" ;
			$sql .= ($d['id']=="0"||$d['id']==0) ? "NULL" : $d['id'] ;
			$sql .= ",".$d['resolution_id'].
					",".$d['domain_id'].
					",'".$d['name']."')," ;
		}
		$sql = substr($sql, 0,-1) ;
		$sql .= " ON DUPLICATE KEY UPDATE
				id = VALUES(id)
				,resolution_id = VALUES(resolution_id)
				,domain_id = VALUES(domain_id)
				,name = VALUES(name)";
		$query = DB::insert(DB::raw($sql));
    }
}	
