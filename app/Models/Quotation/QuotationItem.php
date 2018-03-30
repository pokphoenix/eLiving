<?php

namespace App\Models\Quotation;

use App\Facades\Permission;
use App\Models\Company;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class QuotationItem extends Model
{

    use SoftDeletes;

    protected $table = 'quotation_item';
    public $timestamps = false;

    protected $fillable = ['quotation_id','domain_id', 'name','amount'];
    protected $dates = ['deleted_at'];

    public static function getQuotationHistory($domainId, $quotationId)
    {
        $sql = "SELECT u.id as created_by_id,u.first_name,u.last_name ,h.name as history_status
				,qh.status
				, UNIX_TIMESTAMP(qh.created_at) as history_created_at
				,qc.description as comment_description
				,qc.id as comment_id
				FROM quotation_historys qh 
				LEFT JOIN master_status_history h
				ON h.id = qh.status
				LEFT JOIN users u 
				ON u.id = qh.created_by
				LEFT JOIN quotation_comments qc 
				ON qc.id = qh.quotation_comment_id
				WHERE qh.quotation_id = $quotationId
				AND qh.domain_id = $domainId
				ORDER BY  qh.created_at DESC" ;
        return DB::select(DB::raw($sql));
    }

    public static function getQuotationComment($domainId, $quotationId)
    {

        $sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,tc.id as comment_id
                ,tc.description as comment_description
                ,UNIX_TIMESTAMP(tc.created_at) as ts_created_at
                FROM users u
                JOIN quotation_comments tc 
                ON tc.created_by = u.id 
                WHERE tc.domain_id =$domainId AND tc.quotation_id=$quotationId ORDER BY tc.created_at DESC";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query;
    }

    public static function getQuotationVote($domainId, $quotationId)
    {
        $sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,qv.company_id
                FROM quotation_vote qv
                JOIN users u
                ON qv.user_id = u.id 
                WHERE qv.domain_id =$domainId AND qv.quotation_id=$quotationId
                AND qv.user_id in ( SELECT u.id
						FROM role_user ru
						JOIN users u
						ON u.id_card = ru.id_card
						WHERE ru.role_id = 3 
						AND ru.domain_id = $domainId )
                ";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query;
    }

    public static function getQuotationVoteInstead($domainId, $quotationId)
    {
        $sql = "SELECT qvi.id, 0 as user_id,CONCAT( qvi.first_name,' ',qvi.last_name) as user_name
               ,CONCAT( '".url('')."/public/img/profile/0.png') as img 
                ,qvi.company_id
                ,c.name as company_name
                ,CONCAT( u.first_name,' ',u.last_name) as created_by_name
                FROM quotation_vote_instead qvi
                LEFT JOIN  companies  c 
				ON c.id = qvi.company_id
				AND c.domain_id = $domainId
				LEFT JOIN users u ON u.id=qvi.created_by
                WHERE qvi.domain_id =$domainId AND qvi.quotation_id=$quotationId
                ";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query;
    }



    public static function getUserCanVote($domainId, $quotationId)
    {

        $sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
               ,u.first_name
               ,u.last_name
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,CASE WHEN qv.user_id is not null THEN 1 
                ELSE 0 END as voted
                ,c.name as company_name
                ,qv.created_at
				FROM role_user ru 
				JOIN users u
				ON u.id_card = ru.id_card
				LEFT JOIN quotation_vote qv
				ON qv.user_id = u.id 
				AND qv.quotation_id = $quotationId
				LEFT JOIN  companies  c 
				ON c.id = qv.company_id
				AND c.domain_id = $domainId
				WHERE ru.role_id = 3 AND ru.domain_id = $domainId
				ORDER BY u.id ASC";
         $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query;
    }

    private static function status($data)
    {
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
                $txt = (($appLocal=="th") ? "เสร็จ ณ " : "Done at ").date('d M Y', strtotime($data['doned_at'])) ;
                break;
        }
        return $txt ;
    }

    public static function color($id)
    {
        $status = self::QuotationStatus();
        $text = '';
        foreach ($status as $s) {
            if ($s['id']==$id) {
                $text = $s['color'] ;
            }
        }
        return  $text ;
    }

    public static function QuotationStatus()
    {
         return [ ['id'=>1,'text'=>'New','color'=>'#8a8a8a']
            ,['id'=>2,'text'=>'Voting','color'=>'#c37949']
            ,['id'=>3,'text'=>'Voted','color'=>'#a0c349']
            ,['id'=>4,'text'=>'Cancel','color'=>'#d2505b']
            ,['id'=>5,'text'=>'In Progress','color'=>'#c5b156']
            ,['id'=>6,'text'=>'Pending','color'=>'#9f60b9']
            ,['id'=>7,'text'=>'Done','color'=>'#389986']
         ] ;
    }

    public static function getItemData($domainId, $quotationId)
    {
        $data['quotation_id'] =  $quotationId;
        $data['quotation'] = Quotation::where('domain_id', $domainId)->where('id', $quotationId)->first() ;

        $data['quotation_items'] = QuotationItem::where('domain_id', $domainId)->where('quotation_id', $quotationId)->get() ;

        $data['quotation_company_items'] = QuotationItemCompany::where('domain_id', $domainId)->where('quotation_id', $quotationId)->get() ;

        $data['quotation_historys'] = self::getQuotationHistory($domainId, $quotationId);


        $data['quotation_comments'] = self::getQuotationComment($domainId, $quotationId);
        $data['quotation_votes'] = self::getQuotationVote($domainId, $quotationId);
        $data['quotation_votes_instead'] = self::getQuotationVoteInstead($domainId, $quotationId);

        $data['quotation_user_can_vote'] = self::getUserCanVote($domainId, $quotationId);

        $data['quotation_total_user_can_vote'] =  QuotationVote::totalVoter($domainId);
        $data['quotation_total_user_instead_vote'] =  $data['quotation_total_user_can_vote']-count($data['quotation_user_can_vote']);


        $hasAdmin = Auth()->user()->hasRole('admin') ;
        $hasOfficer = Auth()->user()->hasRole('officer') ;
        $hasHeaduser = Auth()->user()->hasRole('head.user') ;

        $isOwner = false;
        if ($data['quotation']['created_by']==Auth()->user()->id) {
            $isOwner = true;
        }

        

        $cardStatus = $data['quotation']['status'] ;


        $data['quotation']['status_txt'] = self::status($data['quotation']);
        $data['quotation']['status_color'] = self::color($cardStatus);

        $userVote = count($data['quotation_votes']);
        $userCanVote = count($data['quotation_user_can_vote']);





  //    $sql = "SELECT qic.*,c.name,c.id as company_id ,qica.path,qica.filename
  //            FROM quotation_company qic
        //      JOIN companies c ON c.id = qic.company_id
        //      AND c.domain_id = qic.domain_id
        //      LEFT JOIN quotation_item_company_attachment qica
        //      ON qica.domain_id = qic.domain_id
        //      AND qica.company_id = qic.company_id
        //      AND qica.quotation_id = qic.quotation_id
        //      WHERE qic.quotation_id = $quotationId
        //      AND qic.domain_id = $domainId" ;
        // $data['quotation_companys'] = DB::select(DB::raw($sql));
        $sql = "SELECT qic.*,c.name,c.id as company_id ,IFNULL(qica.cnt, 0) as has_attachment, IFNULL(qv.cnt, 0) as vote_count
				FROM quotation_company qic 
				JOIN companies c ON c.id = qic.company_id
				AND c.domain_id = qic.domain_id
				LEFT JOIN 
					( SELECT company_id,COUNT(*) as cnt FROM quotation_item_company_attachment  
					WHERE quotation_id = $quotationId
					AND domain_id = $domainId
					GROUP BY company_id ) as qica
				ON qica.company_id = qic.company_id
				
                LEFT JOIN 
					( SELECT a.company_id,COUNT(a.company_id) as cnt 
					FROM 
					(
						SELECT company_id FROM quotation_vote 
					    WHERE quotation_id = $quotationId
						AND domain_id = $domainId
						AND user_id in ( SELECT u.id
							FROM role_user ru
							JOIN users u
							ON u.id_card = ru.id_card
							WHERE ru.role_id = 3 
							AND ru.domain_id = $domainId )
					    UNION ALL 
					    SELECT company_id FROM quotation_vote_instead 
					    WHERE quotation_id = $quotationId
						AND domain_id = $domainId
					) a
					GROUP BY a.company_id ) as qv
				ON qv.company_id = qic.company_id
                
				WHERE qic.quotation_id = $quotationId
				AND qic.domain_id = $domainId
				ORDER BY qic.id ASC";
        $data['quotation_companys'] = DB::select(DB::raw($sql));
    
        $data['status'] = [];

        $data['voted_company_id'] = QuotationVote::checkVoted($quotationId, $domainId);

        

        $voted = ($data['voted_company_id']==="false") ? false : true ;
        
    
        $voting = false ;
        if ($hasHeaduser&&!$voted&&($cardStatus ==2)) {
            $voting = true ;
        }

        $add = false ;
        if ($hasOfficer) {
            $add = true ;
        }



        $resubmit = false;
        if (($cardStatus==2||$cardStatus==3||$cardStatus==4||$cardStatus==5) && $hasOfficer) {
            $resubmit =true ;
        }
        
        $data['status']['btn_set_company_winner'] = $hasOfficer ? true : false ;
        $data['status']['btn_manual_voted'] = ((($userVote/$userCanVote)*100>50)&&$cardStatus ==2) ? true : false ;
        $data['status']['btn_resubmit'] = $resubmit ;
        $data['status']['voting'] = $voting ;
        $data['status']['voted'] = $voted ;
        $data['status']['change_voted'] = ($voted&&($cardStatus ==2||$cardStatus ==3)) ? true :false ;
        $data['status']['add_item'] = ($cardStatus ==1 && $add) ? true : false ;

        if ($cardStatus==7) {
            $data['status']['add_item'] = false;
        }

        $data['status']['btn_delete'] = ($isOwner||$hasAdmin) ? true : false ;


        $data['status']['cancel_vote'] =  (is_null($data['quotation']['vote_winner'])&&$add&&$cardStatus !=4) ? true : false ;
        $data['status']['winner'] =  (is_null($data['quotation']['vote_winner'])) ? false : true ;



        $data['status']['in_progress'] = ( ($cardStatus ==3||$cardStatus ==6||$cardStatus ==7 ) &&(!is_null($data['quotation']['vote_winner'])) && $hasOfficer) ? true : false ;
        $data['status']['pending'] = (($cardStatus ==3||$cardStatus ==5 )&&(!is_null($data['quotation']['vote_winner'])) && $hasOfficer) ? true : false ;
        $data['status']['done'] = (($cardStatus ==5)&&(!is_null($data['quotation']['vote_winner'])) && $hasOfficer) ? true : false ;
        // $sql = "SELECT qic.* FROM quotation_item_company qic
                
        //      WHERE qic.quotation_id = $quotationId
        //      AND qic.domain_id = $domainId" ;
        // $data['quotation_company_items'] = DB::select(DB::raw($sql));

        return $data ;


    //  $sql = "SELECT qi.*,t2.id as quotation_item_company_id,t2.price_per_unit
    //          ,t2.price,t2.name as company_name  FROM  quotation_item qi
                // LEFT JOIN (
                //  SELECT qic.*,c.name FROM quotation_item_company qic
                //  JOIN companies c ON c.id = qic.company_id
                //  AND c.domain_id = qic.domain_id
                // )t2
                // ON qi.id = t2.quotation_item_id
                // AND qi.quotation_id = t2.quotation_id
                // AND qi.domain_id = t2.domain_id
                // WHERE qi.quotation_id =$quotationId AND qi.domain_id =$domainId" ;
    //     $query = DB::select(DB::raw($sql));
    }

    public static function getItemCompanyData($domainId, $quotationId, $companyId)
    {
        $data['quotation_items'] = QuotationItem::where('domain_id', $domainId)->where('quotation_id', $quotationId)->get() ;
        
        $data['quotation_company_items'] = QuotationItemCompany::where('domain_id', $domainId)->where('quotation_id', $quotationId)->where('company_id', $companyId)->get() ;
        $data['quotation_company_attach'] = QuotationCompanyAttach::getAttachment($domainId, $companyId, $quotationId) ;


  //    $sql = "SELECT * FROM quotation_company qic
        //      JOIN companies c
  //               ON c.id = qic.company_id
        //      AND c.domain_id = qic.domain_id
        //      WHERE qic.quotation_id = $quotationId
        //      AND qic.domain_id = $domainId
  //               AND qic.company_id = $companyId" ;

        // $data['quotation_companys'] = collect(DB::select(DB::raw($sql)) )->first() ;
         $data['quotation_companys'] = QuotationCompany::where('quotation_id', $quotationId)
            ->where('domain_id', $domainId)
            ->where('company_id', $companyId)
            ->first();

        $data['company'] = Company::where('domain_id', $domainId)->where('id', $companyId)->first();

        return $data ;
    }

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
}
