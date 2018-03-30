<?php

namespace App\Http\Controllers\API\Main;

use App\Http\Controllers\ApiController;
use App\Models\Admin\PreWelcome;
use App\Models\Post\Post;
use App\User;
use Auth;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class DashboardController extends ApiController
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


    public function index($domainId)
    {
        Auth::user()->update(['recent_domain'=>$domainId]) ;
        $user = Auth::user() ;
        $idCard = $user->id_card ;
        $domain =  $user->domains();
        $data = Auth()->user()->getNotification() ;
        $data->user = $user;


        $lang = getLang();

        // $data->quotation = [];

        // if(Auth()->user()->hasRole('head.user')) {
        //     $sql = "SELECT q.*
        //         FROM quotations as q
        //         LEFT JOIN quotation_vote qv
        //         ON qv.quotation_id = q.id
        //         AND qv.user_id = ".$user->id."
        //         WHERE q.domain_id = ".$user->recent_domain."
        //         AND q.status=2
        //         AND qv.user_id is null" ;
        //     $query  = DB::select(DB::raw($sql));
        //     if(!empty($query)){
        //         $data->quotation = $query ;
        //     }
        // }

       
        $data->domain = $domain;
        $data->pre_welcom = PreWelcome::find(1)->text;



        $likeRoleQuery = "";
        $searchQuery = "";
        $sql = "SELECT r.name as role_name 
                FROM role_user  ru
                JOIN roles r ON r.id=ru.role_id
                WHERE ru.id_card = '$idCard' AND ru.domain_id=".$domainId ;
        $roles = DB::select(DB::raw($sql));
        if (!empty($roles)) {
            $roleQuery = "";
            foreach ($roles as $key => $r) {
                $roleQuery .= " or p.public_role like '%".$r->role_name."%' ";
            }
            $roleQuery = substr($roleQuery, 3);
            $likeRoleQuery .= " AND ( $roleQuery  ) ";
        }

        $searchQuery = " AND (now() BETWEEN  p.public_start_at  AND p.public_end_at  OR (now() >  p.public_start_at AND p.public_end_at is null) ) ".$likeRoleQuery ;
        $order = "  ORDER BY p.prioritize DESC,p.public_end_at ASC,p.public_start_at ASC";
        $data->posts = Post::getListData($domainId, 3, $searchQuery, $order);




        $sqlTask = "SELECT t.id,t.created_at,t.title,t.room_id FROM tasks  t
                    JOIN (
                        SELECT room_id FROM user_rooms WHERE id_card = '$idCard' AND approve = 1
                    ) t2 
                    ON t2.room_id = t.room_id
                    WHERE t.domain_id =$domainId  AND t.type=2  AND  (t.status !=7 OR (t.status=7 AND NOW() < DATE_ADD(t.doned_at, INTERVAL 7 DAY)  )  )";
        $data->tasks_user = DB::select(DB::raw($sqlTask));


        $sqlTaskOfficer = "SELECT id,created_at,title,type FROM tasks
                    WHERE domain_id =$domainId   AND  (status !=7 OR (status=7 AND NOW() < DATE_ADD(doned_at, INTERVAL 7 DAY)  )  )";
        $data->tasks_officer = DB::select(DB::raw($sqlTaskOfficer));


        $data->quotation = [];
    
        $sqlPost = "SELECT p.id,p.created_at,p.room_id
                    ,mpt.name_$lang as parcel_type_name 
                    FROM parcels  p
                    JOIN (
                        SELECT room_id FROM user_rooms WHERE id_card = '$idCard' AND approve = 1
                    ) t2 
                    ON t2.room_id = p.room_id
                    LEFT JOIN master_parcel_type mpt 
                    ON mpt.id = p.type 
                   
                    WHERE p.domain_id = $domainId AND p.receive_at is null";
        $data->parcels = DB::select(DB::raw($sqlPost));

        if (Auth()->user()->hasRole('head.user')) {
             $sqlPost = "SELECT q.id,q.title,q.created_at FROM quotations q 
                    LEFT JOIN 
                    (
                        SELECT * FROM quotation_vote WHERE user_id=".$user->id." AND domain_id=$domainId
                    )t2
                    ON t2.quotation_id = q.id
                    WHERE q.domain_id = $domainId AND t2.company_id is null AND (q.status !=1 AND q.status!=4)  ";
            $data->quotations = DB::select(DB::raw($sqlPost));
        } else {
            $data->quotations = [];
        }

       



        return $this->respondWithItem($data);
    }
}
