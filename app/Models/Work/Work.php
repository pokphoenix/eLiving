<?php

namespace App\Models\Work;

use App\Facades\Permission;
use App\Models\Master\WorkSystemType;
use App\Models\Room;
use App\Models\work\workCategory;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Work extends Model
{
    protected $table = 'works';

    protected $fillable = ['title','description', 'status','domain_id','pioritized','room_id','category_id','created_by','type','job_type','area_type','tower','floor','tel','requested_by','requested_at','action_taken','incomplete_because','technician_by','technician_at','technician_name','checked_by','checked_at','recommendation','','doned_at','pioritized_desc','result'];
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
        switch ($data->status) {
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
                $txt = (($appLocal=="th") ? "เสร็จ ณ " : "Done at")." ".date('d/m/Y', strtotime($data->doned_at)) ;
                break;
        }

        if ($getId) {
            $txt = $data->status ;
        }


        // if(isset($data['type'])){
        //  if($data['status']==1&&$data['type']==1&&(strtotime('monday this week')<=strtotime($data['start_work_at'])&&
 
        return $txt ;
    }

    public static function getWorkHistory($domainId, $workId)
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
				FROM work_historys qh 
				LEFT JOIN master_status_history h
				ON h.id = qh.status
				LEFT JOIN users u 
				ON u.id = qh.created_by
				LEFT JOIN work_comments qc 
				ON qc.id = qh.work_comment_id
				LEFT JOIN work_attachments ta 
				ON ta.id = qh.work_attach_id
					
				LEFT JOIN users au 
				ON au.id = qh.assign_to_user_id	

				LEFT JOIN master_work_system_type mtc 
				ON mtc.id = qh.work_category_id

				WHERE qh.work_id = $workId
				AND qh.domain_id = $domainId
				ORDER BY qh.created_at DESC" ;
        return DB::select(DB::raw($sql));
    }

    public static function getWorkMember($domainId, $workId)
    {

        $sql = "SELECT u.id,CONCAT( u.first_name,' ',u.last_name) as text
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                FROM users u
                JOIN work_members tm 
                ON tm.user_id = u.id 
                WHERE tm.domain_id =$domainId AND tm.work_id=$workId ";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query ;
    }
    public static function getWorkAttach($domainId, $workId)
    {

        $sql = "SELECT ta.*,CONCAT( '".url('')."/public/storage/',ta.path,'/',ta.filename) as file_path
					,u.first_name,u.last_name
					, UNIX_TIMESTAMP(ta.created_at) as created_at
					FROM work_attachments ta
					LEFT JOIN users u
					ON u.id = ta.created_by
					WHERE ta.work_id=$workId 
					AND ta.domain_id=$domainId
					ORDER BY ta.id DESC";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->file_path = getBase64Img($q->file_path);
        }
        return  $query;
    }




    public static function getWorkComment($domainId, $workId)
    {
        $sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,tc.id as comment_id
                ,tc.description as comment_description
                ,UNIX_TIMESTAMP(tc.created_at) as ts_created_at
                FROM users u
                JOIN work_comments tc 
                ON tc.created_by = u.id 
                WHERE tc.domain_id =$domainId AND tc.work_id=$workId ORDER BY tc.created_at DESC";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query;
    }


    public static function getWork($domainId, $workId)
    {
        $lang = getLang();
        $sql = "SELECT w.*,mp.name_$lang as pioritized_name
                ,cu.id as creator_id,CONCAT( cu.first_name,' ',cu.last_name) as creator_name
                ,cu.tel as creator_tel
                ,ru.id as requestor_id,CONCAT( ru.first_name,' ',ru.last_name) as requestor_name
                ,tu.id as technician_id,CONCAT( tu.first_name,' ',tu.last_name) as technician_name
                ,cku.id as checked_id,CONCAT( cku.first_name,' ',cku.last_name) as checked_name
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                FROM works as w
                JOIN master_work_prioritize as mp 
                ON mp.id=w.pioritized
                JOIN users as cu 
                ON cu.id=w.created_by
                LEFT JOIN users as ru 
                ON ru.id=w.requested_by 
                LEFT JOIN users as tu 
                ON tu.id=w.technician_by
                LEFT JOIN users as cku 
                ON cku.id=w.checked_by
                LEFT JOIN rooms as r 
                ON r.id = w.room_id
                WHERE w.domain_id =$domainId AND w.id=$workId ORDER BY w.created_at DESC";
        $query = DB::select(DB::raw($sql));
        return  (!empty($query)) ? $query[0] : [] ;
    }



    public static function getData($domainId, $workId, $type = null)
    {
        $lang = getLang();


        if (is_null($type)) {
            $type = 1 ;
        }

        $userId = Auth()->user()->id;
        $hasAdmin = Auth()->user()->hasRole('admin') ;
        $hasOfficer = Auth()->user()->hasRole('officer') ;
        $hasHeaduser = Auth()->user()->hasRole('head.user') ;
        $hasUser = Auth()->user()->hasRole('user') ;


         $sql = "SELECT w.*
                ,mp.name_$lang as pioritized_name
                ,IFNULL(at.name_$lang,'-') as area_type_name
                ,IFNULL(jt.name_$lang,'-') as job_type_name
                ,cu.id as creator_id,CONCAT( cu.first_name,' ',cu.last_name) as creator_name
                ,CASE WHEN cu.profile_url is not null AND cu.avartar_id=0 THEN cu.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',cu.avartar_id,'.png') 
                END as creator_img 
                ,cu.tel as creator_tel
                ,ru.id as requestor_id,CONCAT( ru.first_name,' ',ru.last_name) as requestor_name
                 ,CASE WHEN ru.profile_url is not null AND ru.avartar_id=0 THEN ru.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',ru.avartar_id,'.png') 
                END as requestor_img 

                ,tu.id as technician_id,CONCAT( tu.first_name,' ',tu.last_name) as technician_name
                 ,CASE WHEN tu.profile_url is not null AND tu.avartar_id=0 THEN tu.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',tu.avartar_id,'.png') 
                END as technician_img 

                ,cku.id as checked_id,CONCAT( cku.first_name,' ',cku.last_name) as checked_name
                 ,CASE WHEN cku.profile_url is not null AND cku.avartar_id=0 THEN cku.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',cku.avartar_id,'.png') 
                END as checked_img 

                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                FROM works as w
                JOIN master_work_prioritize as mp 
                ON mp.id=w.pioritized
        
                LEFT JOIN master_work_area_type as at 
                ON at.id=w.area_type 

                LEFT JOIN master_work_job_type as jt 
                ON jt.id=w.job_type


                JOIN users as cu 
                ON cu.id=w.created_by
                LEFT JOIN users as ru 
                ON ru.id=w.requested_by 
                LEFT JOIN users as tu 
                ON tu.id=w.technician_by
                LEFT JOIN users as cku 
                ON cku.id=w.checked_by
                LEFT JOIN rooms as r 
                ON r.id = w.room_id
                WHERE w.domain_id =$domainId AND w.id=$workId ORDER BY w.created_at DESC";
        $query = DB::select(DB::raw($sql));
        if (empty($query)) {
            return [] ;
        }

        $data['work'] = $query[0];
        if (empty($data['work'])) {
            return [] ;
        }
        $data['work_id'] = $workId;


        $data['task_category'] = WorkSystemType::where('id', $data['work']->category_id)->select(DB::raw("*,name_$lang as name"))->first() ;
        $data['work']->status_txt = self::status($data['work']);

        $data['work']->status = self::status($data['work'], true);
        

    
        $data['work']->room_name = $data['work']->creator_name ;
        $data['work']->status_color = self::statusColor($data['work']->status);
        

        // $member = WorkMember::where('domain_id',$domainId)
        // ->where('work_id',$workId)
        // ->where('user_id',$userId)
        // ->first();
        $hasMember = (isset($data['work']->technician_by)) ? true : false ;

        $isOwner = ($data['work']->created_by==$userId) ? true : false;

        // $data['task_members'] = self::getWorkMember($domainId,$workId);
        $data['task_historys'] = self::getWorkHistory($domainId, $workId);
        
        

        $data['work_comments'] = self::getWorkComment($domainId, $workId);

        $data['work_attachs'] =  self::getWorkAttach($domainId, $workId);
        


        $data['work']->cover_img = (!empty($data['work_attachs'])) ? $data['work_attachs'][0]->file_path : null ;


        $data['status'] = [];

        $add = false ;
        if ($hasOfficer) {
            $add = true ;
        }

        $data['status']['edit_description'] = ($isOwner) ? true : false ;

        $data['status']['add_item'] = $add ;
        
        $data['status']['history'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? false : true ;
        $data['status']['viewer'] = (empty($viewer)) ? false : true ;
        
        $data['status']['menu_flow'] = (!$hasOfficer&&!$hasAdmin) ? false : true ;




        $data['status']['menu_add'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? false : true ;
        $data['status']['menu_action'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? false : true ;
        $data['status']['menu_delete'] = (($isOwner||$hasAdmin)&& ($data['work']->status==1||$data['work']->status==4) ) ? true : false ;
        $data['status']['attachment'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? true : false ;

        $data['status']['btn_attachment'] =  ($type==2&&$hasUser) ? true : false ;

        

        $data['status']['accept'] = ($data['work']->status ==1&&($hasOfficer))  ? true : false ;
            $data['status']['todo'] =false;

        

        $data['status']['re_submit'] = ( $data['work']->status ==4&& ($hasOfficer||$isOwner))  ? true : false ;

        $data['status']['cancel_task'] =  (($data['work']->status !=7&&$data['work']->status !=4)&&$add) ? true : false ;

        //-- check new work this week
        // ( (($data['work']->status ==2||$data['work']->status ==7 ||$data['work']->status ==3||$data['work']->status ==6||( $data['work']->status ==1&&$data['work']['type']==1 && strtotime($data['work']['start_work_at']) >= time() ) ) && $hasOfficer && $hasMember))

        $data['status']['in_progress'] = ( (($data['work']->status ==2||$data['work']->status ==7 ||$data['work']->status ==3||$data['work']->status ==6 ) && $hasOfficer )) ? true : false ;


        $data['status']['pending'] = ( (($data['work']->status ==2||$data['work']->status ==3||$data['work']->status ==5 ) && $hasOfficer)) ? true : false ;


        $data['status']['done'] = (($data['work']->status ==5) && ($hasOfficer)  && $hasMember ) ? true : false ;
        $data['status']['is_member'] = $hasMember;
            
        if ($data['status']['re_submit']) {
            $data['status']['menu_flow'] = true ;
        }

        return $data ;
    }



    public static function getWorkListData($domainId, $type = 1, $searchQuery = null)
    {
        
        $lang = getLang();
        $sql = "select t.*,tc.name_$lang as category_name 
                ,tc.color as category_color 
                ,u.id as member_id
                ,CONCAT( u.first_name,' ',u.last_name) as member_name 
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as member_img 
                ,t4.file_path
                ,mwp.name_$lang as pioritized_name
                from works as t 
                left join (SELECT ta.*,CONCAT( '".url('')."/public/storage/',ta.path,'/',ta.filename) as file_path
                    FROM work_attachments ta
                    WHERE ta.domain_id=$domainId
                    ORDER BY ta.id DESC LIMIT 1) t4
                ON t4.work_id = t.id
                left join master_work_system_type as tc 
                on t.category_id = tc.id 
                left join users as u 
                on t.technician_by = u.id
                left join master_work_prioritize as mwp 
                on t.pioritized = mwp.id 
                WHERE t.domain_id = $domainId
                AND t.type=$type
                $searchQuery
                ORDER BY t.created_at ASC";
        $works   =  DB::select(DB::raw($sql));
         $data['works'] = [];
        foreach ($works as $key => $work) {
            $data['works'][$work->id]['id'] = $work->id ;
            $data['works'][$work->id]['title'] = $work->title ;
            $data['works'][$work->id]['created_at'] = strtotime($work->created_at) ;
            $data['works'][$work->id]['status'] = $work->status ;
            $data['works'][$work->id]['status_text'] = Work::statusText($work->status) ;
            $data['works'][$work->id]['status_color'] = Work::statusColor($work->status) ;
                
            $data['works'][$work->id]['pioritized'] = $work->pioritized ;
            $data['works'][$work->id]['pioritized_name'] = $work->pioritized_name ;
            // $data['works'][$work->id]['job_type'] = $work->job_type ;
            // $data['works'][$work->id]['area_type'] = $work->area_type ;
            $data['works'][$work->id]['domain_id'] = $work->domain_id ;
            // $data['works'][$work->id]['floor'] = $work->floor ;
            // $data['works'][$work->id]['tower'] = $work->tower ;
            // $data['works'][$work->id]['tel'] = $work->tel ;
            // $data['works'][$work->id]['requested_by'] = $work->requested_by ;
            // $data['works'][$work->id]['requested_at'] = $work->requested_at ;
            // $data['works'][$work->id]['action_taken'] = $work->action_taken ;
            // $data['works'][$work->id]['incomplete_because'] = $work->incomplete_because ;
            // $data['works'][$work->id]['technician_by'] = $work->technician_by ;
            // $data['works'][$work->id]['technician_at'] = $work->technician_at ;
            // $data['works'][$work->id]['technician_name'] = $work->technician_name ;
            // $data['works'][$work->id]['checked_by'] = $work->checked_by ;
            // $data['works'][$work->id]['checked_at'] = $work->checked_at ;
            // $data['works'][$work->id]['recommendation'] = $work->recommendation ;
            $data['works'][$work->id]['category_id'] = $work->category_id;
            $data['works'][$work->id]['category_name'] = $work->category_name ;
            $data['works'][$work->id]['category_color'] = $work->category_color ;
          
            $data['works'][$work->id]['file_path'] = getBase64Img($work->file_path) ;
            $data['works'][$work->id]['doned_at'] = $work->doned_at ;
            $data['works'][$work->id]['technician_id'] = $work->member_id;
            $data['works'][$work->id]['technician_name'] = $work->member_name;
            $data['works'][$work->id]['technician_img'] = getBase64Img($work->member_img);
        }



        return array_values($data['works']) ;
    }


    public static function statusText($id)
    {
        $status = Work::WorkStatus();
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

        $status = Work::WorkStatus();
        $text = '';
        foreach ($status as $s) {
            if ($s['id']==$id) {
                $text = $s['color'] ;
            }
        }
        return  $text ;
    }

    public static function WorkStatus()
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
