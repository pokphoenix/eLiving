<?php

namespace App\Models\Suggest;

use App\Facades\Permission;
use App\Models\Master\SuggestCategory;
use App\Models\Room;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Suggest extends Model
{
    protected $table = 'suggests';

    protected $fillable = ['title','description', 'status','domain_id','due_dated_at','due_dated_complete','room_id','start_suggest_at','due_date_complete_at','category_id','created_by','type','doned_at'];
    protected $dates = ['created_at', 'updated_at','due_date_complete_at','doned_at'];

    public function setDueDatedAtAttribute($value)
    {
        $this->attributes['due_dated_at'] = Carbon::parse($value);
    }
    public function setDueDateCompleteAtAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['due_date_complete_at'] = Carbon::parse($value);
        } else {
            $this->attributes['due_date_complete_at'] = null ;
        }
    }

    public static function status($data, $getId = false)
    {

        $appLocal = App::getLocale() ;
        switch ($data['status']) {
            case 1:
                $txt = ($appLocal=="th") ? "รายการใหม่" : "New" ;
                break;
            case 2:
                $txt = ($appLocal=="th") ? "เริ่มทำในสัปดาห์นี้" : "To do";
                break;
            case 3:
                $txt = ($appLocal=="th") ? "ตอบรับ" : "Accept";
                break;
            case 4:
                $txt = ($appLocal=="th") ? "ยกเลิก" : "Cancel";
                break;
            case 5:
                $txt = ($appLocal=="th") ? "กำลังดำเนินการ" : "In Progress";
                break;
            case 6:
                $txt = ($appLocal=="th") ? "รอดำเนินการ" : "Pending";
                break;
            case 7:
                $txt = (($appLocal=="th") ? "เสร็จ ณ " : "Done at")." ".date('d/m/Y', strtotime($data['doned_at'])) ;
                break;
        }

        if ($getId) {
            $txt = $data['status'] ;
        }

        // if(isset($data['type'])){
        //  if($data['status']==1&&$data['type']==1&&(strtotime('monday this week')<=strtotime($data['start_suggest_at'])&&
  //                                   strtotime($data['start_suggest_at'])<=strtotime('sunday this week')
  //                                   )){
        //      $txt = ($appLocal=="th") ? "เริ่มทำในสัปดาห์นี้" : "To do";
  //               if($getId){
  //                   $txt = 2 ;
  //               }
        //  }

           
        // }
        
        return $txt ;
    }

    public static function getSuggestHistory($domainId, $id)
    {

        

        $sql = "SELECT u.id as created_by_id,u.first_name,u.last_name ,h.name as history_status
				,qh.status
				, UNIX_TIMESTAMP(qh.created_at) as history_created_at

				,qc.description as comment_description
				,qc.id as comment_id
				,UNIX_TIMESTAMP(qh.duedate_to) as history_duedate_to
				,ta.filename as file_name
				,mtc.id as category_id
				,mtc.name_en as category_name
				,CONCAT( au.first_name,' ',au.last_name) as assign_name
				FROM suggest_historys qh 
				LEFT JOIN master_status_history h
				ON h.id = qh.status
				LEFT JOIN users u 
				ON u.id = qh.created_by
				LEFT JOIN suggest_comments qc 
				ON qc.id = qh.suggest_comment_id
				LEFT JOIN suggest_attachments ta 
				ON ta.id = qh.suggest_attach_id
					
				LEFT JOIN users au 
				ON au.id = qh.assign_to_user_id	

				LEFT JOIN master_suggest_category mtc 
				ON mtc.id = qh.suggest_category_id

				WHERE qh.suggest_id = $id
				AND qh.domain_id = $domainId
				ORDER BY qh.created_at DESC" ;
        return DB::select(DB::raw($sql));
    }

    public static function getSuggestMember($domainId, $id)
    {

        $sql = "SELECT u.id,CONCAT( u.first_name,' ',u.last_name) as text
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                FROM users u
                JOIN suggest_members tm 
                ON tm.user_id = u.id 
                WHERE tm.domain_id =$domainId AND tm.suggest_id=$id ";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query ;
    }
    public static function getSuggestAttach($domainId, $id)
    {

        $sql = "SELECT ta.*,CONCAT( '".url('')."/public/storage/',ta.path,'/',ta.filename) as file_path
					,u.first_name,u.last_name
					, UNIX_TIMESTAMP(ta.created_at) as created_at
					FROM suggest_attachments ta
					LEFT JOIN users u
					ON u.id = ta.created_by
					WHERE ta.suggest_id=$id 
					AND ta.domain_id=$domainId
					ORDER BY ta.id DESC";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->file_path = getBase64Img($q->file_path);
        }
        return  $query;
    }




    public static function getSuggestComment($domainId, $id)
    {
        $sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,tc.id as comment_id
                ,tc.description as comment_description
                ,UNIX_TIMESTAMP(tc.created_at) as ts_created_at
                FROM users u
                JOIN suggest_comments tc 
                ON tc.created_by = u.id 
                WHERE tc.domain_id =$domainId AND tc.suggest_id=$id ORDER BY tc.created_at DESC";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query;
    }

    public static function getSuggestChecklist($domainId, $id)
    {
        $sql = "SELECT tc.*
				,tci.title as item_title 
				,tci.id as item_id
				,tci.status as item_status
                FROM suggest_checklists tc
                LEFT JOIN suggest_checklist_items tci
                ON tc.id = tci.checklist_id
                AND tc.suggest_id =tci.suggest_id
                AND tc.domain_id = tci.domain_id
                WHERE tc.domain_id =$domainId AND tc.suggest_id=$id ";
        $checklists = DB::select(DB::raw($sql));
        if (empty($checklists)) {
            return $checklists;
        }
        $data = [];
        foreach ($checklists as $key => $checklist) {
            $data[$checklist->id]['id'] = $checklist->id;
            $data[$checklist->id]['title'] = $checklist->title;
            $data[$checklist->id]['created_by'] = $checklist->created_by;
            if (isset($checklist->item_id)) {
                $item['id'] = $checklist->item_id;
                $item['title'] = $checklist->item_title ;
                $item['status'] = $checklist->item_status ;
                $data[$checklist->id]['item'][] = $item;
            } else {
                $data[$checklist->id]['item'] = [];
            }
        }


        return array_values($data);
    }

    public static function getSuggestData($domainId, $id, $type = null)
    {

        if (is_null($type)) {
            $type = 1 ;
        }

        $userId = Auth()->user()->id;
        $hasAdmin = Auth()->user()->hasRole('admin') ;
        $hasOfficer = Auth()->user()->hasRole('officer') ;
        $hasHeaduser = Auth()->user()->hasRole('head.user') ;
        $hasUser = Auth()->user()->hasRole('user') ;
        $data['suggest'] = Suggest::where('domain_id', $domainId)
                        ->where('id', $id)
                
                        ->first() ;
        $data['suggest_id'] = $id;
        $data['suggest_category'] = SuggestCategory::find($data['suggest']->category_id) ;
        $data['suggest']['status_txt'] = self::status($data['suggest']);
        $data['suggest']['status'] = self::status($data['suggest'], true);

        $room = Room::where('id', $data['suggest']->room_id)->where('domain_id', $domainId)->first() ;

        $userCreate = User::where('id', $data['suggest']->created_by)->first();

        $data['suggest']['room_number'] = (!empty($room)) ?   $room->name_prefix.$room->name.$room->name_surfix : null;
        $data['suggest']['room_name'] = $userCreate->first_name." ".$userCreate->last_name ;
        $data['suggest']['room_tel'] = $userCreate->tel ;
        $data['suggest']['room_email'] = $userCreate->email ;

        $data['suggest']['status_color'] = self::statusColor($data['suggest']->status);
        

        // $member = suggestMember::where('domain_id',$domainId)
        // ->where('suggest_id',$id)
        // ->where('user_id',$userId)
        // ->first();
        $member = [];
        $hasMember = (!empty($member)) ? true : false ;

        $isOwner = ($data['suggest']->created_by==$userId) ? true : false;

  //       $data['suggest_members'] = self::getsuggestMember($domainId,$id);
        // $data['suggest_historys'] = self::getsuggestHistory($domainId,$id);
        
        // $data['suggest_checklists'] = self::getsuggestChecklist($domainId,$id);

        $data['suggest_comments'] = self::getSuggestComment($domainId, $id);

        $data['suggest_attachs'] =  self::getSuggestAttach($domainId, $id);
        


        $data['suggest']['cover_img'] = (!empty($data['suggest_attachs'])) ? $data['suggest_attachs'][0]->file_path : null ;


        $data['status'] = [];

        $add = false ;
        if ($hasOfficer) {
            $add = true ;
        }

        $data['status']['edit_description'] = ($isOwner) ? true : false ;

        $data['status']['add_item'] = $add ;
        
        $data['status']['history'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? false : true ;
        
        $data['status']['duedate'] = (!is_null($data['suggest']['due_dated_at'])) ? true :false ;


        $data['status']['menu_flow'] = (!$hasOfficer&&!$hasAdmin) ? false : true ;




        $data['status']['menu_add'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? false : true ;
        $data['status']['menu_action'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? false : true ;
        $data['status']['menu_delete'] = (($isOwner||$hasAdmin)&& ($data['suggest']['status']==1||$data['suggest']['status']==4) ) ? true : false ;
        $data['status']['attachment'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? true : false ;

        $data['status']['btn_attachment'] =  ($type==2&&$hasUser) ? true : false ;

        $data['status']['start_suggest'] = ($data['suggest']['status'] ==1&& strtotime($data['suggest']['start_suggest_at']) < time() && ($hasOfficer||$hasHeaduser))  ? true : false ;

        $data['status']['accept'] = ($data['suggest']['status'] ==1&&($hasOfficer))  ? true : false ;
            $data['status']['todo'] =false;

        

        $data['status']['re_submit'] = ( $data['suggest']['status'] ==4&& ($hasOfficer||$isOwner))  ? true : false ;

        $data['status']['cancel_task'] =  (($data['suggest']['status'] !=7&&$data['suggest']['status'] !=4)&&$add) ? true : false ;

        //-- check new suggest this week
        // ( (($data['suggest']['status'] ==2||$data['suggest']['status'] ==7 ||$data['suggest']['status'] ==3||$data['suggest']['status'] ==6||( $data['suggest']['status'] ==1&&$data['suggest']['type']==1 && strtotime($data['suggest']['start_suggest_at']) >= time() ) ) && $hasOfficer && $hasMember))

        $data['status']['in_progress'] = ( (($data['suggest']['status'] ==2||$data['suggest']['status'] ==7 ||$data['suggest']['status'] ==3||$data['suggest']['status'] ==6 ) && $hasOfficer && $hasMember)) ? true : false ;


        $data['status']['pending'] = ( (($data['suggest']['status'] ==2||$data['suggest']['status'] ==3||$data['suggest']['status'] ==5 ) && $hasOfficer && $hasMember)) ? true : false ;


        $data['status']['done'] = (($data['suggest']['status'] ==5) && ($hasOfficer)  && $hasMember ) ? true : false ;
        $data['status']['is_member'] = $hasMember;
            
        if ($data['status']['re_submit']) {
            $data['status']['menu_flow'] = true ;
        }

        return $data ;
    }

    public static function getSuggestListData($domainId, $type = 1, $searchQuery = null)
    {
        $lang = getLang();

        $sql = "select t.*,tc.name_$lang as category_name 
                ,tc.color as category_color 
               
               
                ,t4.file_path
                from suggests as t 
              

                left join (SELECT ta.*,CONCAT( '".url('')."/public/storage/',ta.path,'/',ta.filename) as file_path
                    FROM suggest_attachments ta
                    WHERE ta.domain_id=$domainId
                    ORDER BY ta.id DESC LIMIT 1) t4
                ON t4.suggest_id = t.id
                left join master_suggest_category as tc 
                on t.category_id = tc.id 
              
                WHERE t.domain_id = $domainId
                AND t.type=$type
                $searchQuery
                ORDER BY t.created_at ASC";
        $suggests   =  DB::select(DB::raw($sql));
         $data['suggests'] = [];
        foreach ($suggests as $key => $suggest) {
            $data['suggests'][$suggest->id]['id'] = $suggest->id ;
            $data['suggests'][$suggest->id]['title'] = $suggest->title ;
            $data['suggests'][$suggest->id]['created_at'] = strtotime($suggest->created_at) ;
            $data['suggests'][$suggest->id]['status'] = $suggest->status ;
            $data['suggests'][$suggest->id]['status_text'] = Suggest::statusText($suggest->status) ;
            $data['suggests'][$suggest->id]['status_color'] = Suggest::statusColor($suggest->status) ;
            $data['suggests'][$suggest->id]['domain_id'] = $suggest->domain_id ;
            $data['suggests'][$suggest->id]['category_id'] = $suggest->category_id;
            $data['suggests'][$suggest->id]['category_name'] = $suggest->category_name ;
            $data['suggests'][$suggest->id]['category_color'] = $suggest->category_color ;
            $data['suggests'][$suggest->id]['file_path'] = getBase64Img($suggest->file_path) ;
            $data['suggests'][$suggest->id]['doned_at'] = $suggest->doned_at ;
        }



        return array_values($data['suggests']) ;
    }


    public static function statusText($id)
    {
        $status = Suggest::suggestStatus();
        $text = '';
        foreach ($status as $s) {
            if ($s['id']==$id) {
                $text = $s['text'] ;
            }
        }
        return  $text ;
    }
    public static function statusColor($id)
    {
        $status = Suggest::suggestStatus();
        $text = '';
        foreach ($status as $s) {
            if ($s['id']==$id) {
                $text = $s['color'] ;
            }
        }
        return  $text ;
    }

    public static function suggestStatus()
    {
            $lang = getLang();
         return [ ['id'=>1,'text'=> ($lang=='th' ? 'รายการใหม่' : 'New' ),'color'=>'#8a8a8a']
            ,['id'=>2,'text'=> ($lang=='th' ? 'เริ่มทำในสัปดาห์นี้' : 'To Do' ),'color'=>'#52a7e0']
            ,['id'=>3,'text'=> ($lang=='th' ? 'ตอบรับ' : 'Accept' ),'color'=>'#00c0ef']
            ,['id'=>4,'text'=> ($lang=='th' ? 'ยกเลิก' : 'Cancel' ),'color'=>'#f56954']
            ,['id'=>5,'text'=> ($lang=='th' ? 'กำลังดำเนินการ' : 'In Progress' ),'color'=>'#f39c12']
            ,['id'=>6,'text'=> ($lang=='th' ? 'รอดำเนินการ' : 'Pending' ),'color'=>'#605ca8']
            ,['id'=>7,'text'=> ($lang=='th' ? 'เสร็จแล้ว' : 'Done' ),'color'=>'#00a65a']
         ] ;
    }
}
