<?php

namespace App\Models\Task;

use App\Facades\Permission;
use App\Models\Room;
use App\Models\Task\TaskCategory;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = ['title','description', 'status','domain_id','pioritized','is_issues','due_dated_at','due_dated_complete','room_id','start_task_at','due_date_complete_at','category_id','created_by','type','doned_at'];
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
        //  if($data['status']==1&&$data['type']==1&&(strtotime('monday this week')<=strtotime($data['start_task_at'])&&
  //                                   strtotime($data['start_task_at'])<=strtotime('sunday this week')
  //                                   )){
        //      $txt = ($appLocal=="th") ? "เริ่มทำในสัปดาห์นี้" : "To do";
  //               if($getId){
  //                   $txt = 2 ;
  //               }
        //  }

           
        // }
        
        return $txt ;
    }

    public static function getTaskHistory($domainId, $taskId)
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
				FROM task_historys qh 
				LEFT JOIN master_status_history h
				ON h.id = qh.status
				LEFT JOIN users u 
				ON u.id = qh.created_by
				LEFT JOIN task_comments qc 
				ON qc.id = qh.task_comment_id
				LEFT JOIN task_attachments ta 
				ON ta.id = qh.task_attach_id
					
				LEFT JOIN users au 
				ON au.id = qh.assign_to_user_id	

				LEFT JOIN master_task_category mtc 
				ON mtc.id = qh.task_category_id

				WHERE qh.task_id = $taskId
				AND qh.domain_id = $domainId
				ORDER BY qh.created_at DESC" ;
        return DB::select(DB::raw($sql));
    }

    public static function getTaskMember($domainId, $taskId)
    {

        $sql = "SELECT u.id,CONCAT( u.first_name,' ',u.last_name) as text
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                FROM users u
                JOIN task_members tm 
                ON tm.user_id = u.id 
                WHERE tm.domain_id =$domainId AND tm.task_id=$taskId ";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query ;
    }
    public static function getTaskAttach($domainId, $taskId)
    {

        $sql = "SELECT ta.*,CONCAT( '".url('')."/public/storage/',ta.path,'/',ta.filename) as file_path
					,u.first_name,u.last_name
					, UNIX_TIMESTAMP(ta.created_at) as created_at
					FROM task_attachments ta
					LEFT JOIN users u
					ON u.id = ta.created_by
					WHERE ta.task_id=$taskId 
					AND ta.domain_id=$domainId
					ORDER BY ta.id DESC";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->file_path = getBase64Img($q->file_path);
        }
        return  $query;
    }




    public static function getTaskComment($domainId, $taskId)
    {
        $sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,tc.id as comment_id
                ,tc.description as comment_description
                ,UNIX_TIMESTAMP(tc.created_at) as ts_created_at
                FROM users u
                JOIN task_comments tc 
                ON tc.created_by = u.id 
                WHERE tc.domain_id =$domainId AND tc.task_id=$taskId ORDER BY tc.created_at DESC";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query;
    }

    public static function getTaskChecklist($domainId, $taskId)
    {
        $sql = "SELECT tc.*
				,tci.title as item_title 
				,tci.id as item_id
				,tci.status as item_status
                FROM task_checklists tc
                LEFT JOIN task_checklist_items tci
                ON tc.id = tci.checklist_id
                AND tc.task_id =tci.task_id
                AND tc.domain_id = tci.domain_id
                WHERE tc.domain_id =$domainId AND tc.task_id=$taskId ";
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

    public static function getTaskData($domainId, $taskId, $type = null)
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
        $data['task'] = Task::where('domain_id', $domainId)
                        ->where('id', $taskId)
                        ->first() ;
        $data['task_id'] = $taskId;
        $data['task_category'] = TaskCategory::where('id', $data['task']->category_id)->select(DB::raw("*,name_$lang as name"))->first() ;
        $data['task']['status_txt'] = self::status($data['task']);
        $data['task']['status'] = self::status($data['task'], true);

        $room = Room::where('id', $data['task']->room_id)->where('domain_id', $domainId)->first() ;

        $userCreate = User::where('id', $data['task']->created_by)->first();

        $data['task']['room_number'] = (!empty($room)) ?   $room->name_prefix.$room->name.$room->name_surfix : null;
        $data['task']['room_name'] = $userCreate->first_name." ".$userCreate->last_name ;
        $data['task']['room_tel'] = $userCreate->tel ;
        $data['task']['room_email'] = $userCreate->email ;

        $data['task']['status_color'] = self::statusColor($data['task']->status);
        $viewer = TaskViewer::where('domain_id', $domainId)
        ->where('task_id', $taskId)
        ->where('user_id', $userId)
        ->first();

        $member = TaskMember::where('domain_id', $domainId)
        ->where('task_id', $taskId)
        ->where('user_id', $userId)
        ->first();
        $hasMember = (!empty($member)) ? true : false ;

        $isOwner = ($data['task']->created_by==$userId) ? true : false;

        $data['task_members'] = self::getTaskMember($domainId, $taskId);
        $data['task_historys'] = self::getTaskHistory($domainId, $taskId);
        
        $data['task_checklists'] = self::getTaskChecklist($domainId, $taskId);

        $data['task_comments'] = self::getTaskComment($domainId, $taskId);

        $data['task_attachs'] =  self::getTaskAttach($domainId, $taskId);
        


        $data['task']['cover_img'] = (!empty($data['task_attachs'])) ? $data['task_attachs'][0]->file_path : null ;


        $data['status'] = [];

        $add = false ;
        if ($hasOfficer) {
            $add = true ;
        }

        $data['status']['edit_description'] = ($isOwner) ? true : false ;

        $data['status']['add_item'] = $add ;
        
        $data['status']['history'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? false : true ;
        $data['status']['viewer'] = (empty($viewer)) ? false : true ;
        $data['status']['duedate'] = (!is_null($data['task']['due_dated_at'])) ? true :false ;


        $data['status']['menu_flow'] = (!$hasOfficer&&!$hasAdmin) ? false : true ;




        $data['status']['menu_add'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? false : true ;
        $data['status']['menu_action'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? false : true ;
        $data['status']['menu_delete'] = (($isOwner||$hasAdmin)&& ($data['task']['status']==1||$data['task']['status']==4) ) ? true : false ;
        $data['status']['attachment'] = (!$hasOfficer&&!$hasHeaduser&&!$hasAdmin) ? true : false ;

        $data['status']['btn_attachment'] =  ($type==2&&$hasUser) ? true : false ;

        $data['status']['start_task'] = ($data['task']['status'] ==1&& strtotime($data['task']['start_task_at']) < time() && ($hasOfficer||$hasHeaduser))  ? true : false ;

        if ($data['task']['type']==1) {
            $data['status']['todo'] = ( $data['task']['status'] ==1&&($hasOfficer)&&strtotime($data['task']['start_task_at']) < time() )  ? true : false ;
            $data['status']['accept'] = false;
        } else {
            $data['status']['accept'] = ($data['task']['status'] ==1&&($hasOfficer))  ? true : false ;
            $data['status']['todo'] =false;
        }


        

        $data['status']['re_submit'] = ( $data['task']['status'] ==4&& ($hasOfficer||$isOwner))  ? true : false ;

        $data['status']['cancel_task'] =  (($data['task']['status'] !=7&&$data['task']['status'] !=4)&&$add) ? true : false ;

        //-- check new task this week
        // ( (($data['task']['status'] ==2||$data['task']['status'] ==7 ||$data['task']['status'] ==3||$data['task']['status'] ==6||( $data['task']['status'] ==1&&$data['task']['type']==1 && strtotime($data['task']['start_task_at']) >= time() ) ) && $hasOfficer && $hasMember))

        $data['status']['in_progress'] = ( (($data['task']['status'] ==2||$data['task']['status'] ==7 ||$data['task']['status'] ==3||$data['task']['status'] ==6 ) && $hasOfficer && $hasMember)) ? true : false ;


        $data['status']['pending'] = ( (($data['task']['status'] ==2||$data['task']['status'] ==3||$data['task']['status'] ==5 ) && $hasOfficer && $hasMember)) ? true : false ;


        $data['status']['done'] = (($data['task']['status'] ==5) && ($hasOfficer)  && $hasMember ) ? true : false ;
        $data['status']['is_member'] = $hasMember;
            
        if ($data['status']['re_submit']) {
            $data['status']['menu_flow'] = true ;
        }

        return $data ;
    }

    public static function getTaskListData($domainId, $type = 1, $searchQuery = null)
    {
        $lang = getLang();

        $sql = "select t.*,tc.name_$lang as category_name 
                ,tc.color as category_color 
                ,u.id as member_id
                ,CONCAT( u.first_name,' ',u.last_name) as member_name 
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as member_img 
                ,IFNULL(t2.cnt,0) as success_checklist
                ,IFNULL(t3.cnt,0) as total_checklist
                ,t4.file_path
                from tasks as t 
                left join ( 
                    SELECT task_id,count(task_id) as cnt
                    FROM task_checklist_items WHERE domain_id=$domainId AND status=1
                    GROUP BY task_id
                ) t2
                ON t2.task_id = t.id
                left join ( 
                    SELECT task_id,count(task_id) as cnt
                    FROM task_checklist_items WHERE domain_id=$domainId
                    GROUP BY task_id
                ) t3 
                ON t3.task_id = t.id

                left join (SELECT ta.*,CONCAT( '".url('')."/public/storage/',ta.path,'/',ta.filename) as file_path
                    FROM task_attachments ta
                    WHERE ta.domain_id=$domainId
                    ORDER BY ta.id DESC LIMIT 1) t4
                ON t4.task_id = t.id
                left join master_task_category as tc 
                on t.category_id = tc.id 
                left join task_members as tm 
                on tm.task_id = t.id 
                and tm.domain_id = $domainId 
                left join users as u 
                on tm.user_id = u.id
                WHERE t.domain_id = $domainId
                AND t.type=$type
                $searchQuery
                ORDER BY t.created_at ASC";
        $tasks   =  DB::select(DB::raw($sql));
         $data['tasks'] = [];
        foreach ($tasks as $key => $task) {
            $data['tasks'][$task->id]['id'] = $task->id ;
            $data['tasks'][$task->id]['title'] = $task->title ;
            $data['tasks'][$task->id]['created_at'] = strtotime($task->created_at) ;
            $data['tasks'][$task->id]['status'] = $task->status ;
            $data['tasks'][$task->id]['status_text'] = Task::statusText($task->status) ;
            $data['tasks'][$task->id]['status_color'] = Task::statusColor($task->status) ;
                
            $data['tasks'][$task->id]['pioritized'] = $task->pioritized ;
            $data['tasks'][$task->id]['is_issues'] = $task->is_issues ;
            $data['tasks'][$task->id]['due_dated_at'] = $task->due_dated_at ;
            $data['tasks'][$task->id]['due_dated_complete'] = $task->due_dated_complete ;
            $data['tasks'][$task->id]['domain_id'] = $task->domain_id ;
            $data['tasks'][$task->id]['start_task_at'] = $task->start_task_at ;
            $data['tasks'][$task->id]['due_date_complete_at'] = $task->due_date_complete_at ;
            $data['tasks'][$task->id]['category_id'] = $task->category_id;
            $data['tasks'][$task->id]['category_name'] = $task->category_name ;
            $data['tasks'][$task->id]['category_color'] = $task->category_color ;
            $data['tasks'][$task->id]['checklist_success'] = $task->success_checklist ;
            $data['tasks'][$task->id]['checklist_total'] = $task->total_checklist ;
            $data['tasks'][$task->id]['file_path'] = getBase64Img($task->file_path) ;
            $data['tasks'][$task->id]['doned_at'] = $task->doned_at ;
            if (isset($task->member_id)) {
                $member['member_id'] = $task->member_id;
                $member['member_name'] = $task->member_name;
                $member['member_img'] = getBase64Img($task->member_img);
                $data['tasks'][$task->id]['members'][] =  $member ;
            } else {
                $data['tasks'][$task->id]['members'] = [];
            }
        }



        return array_values($data['tasks']) ;
    }


    public static function statusText($id)
    {
        $status = task::taskStatus();
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
        $status = task::taskStatus();
        $text = '';
        foreach ($status as $s) {
            if ($s['id']==$id) {
                $text = $s['color'] ;
            }
        }
        return  $text ;
    }

    public static function taskStatus()
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
