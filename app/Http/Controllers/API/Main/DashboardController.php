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


    public function index($domainId){
        Auth::user()->update(['recent_domain'=>$domainId]) ;
        $user = Auth::user() ;
        $domain =  $user->domains();
        $data = Auth()->user()->getNotification() ;
        $data->user = $user;
        $data->quotation = [];

        if(Auth()->user()->hasRole('head.user')) {
            $sql = "SELECT q.*
                FROM quotations as q 
                LEFT JOIN quotation_vote qv
                ON qv.quotation_id = q.id 
                AND qv.user_id = ".$user->id."
                WHERE q.domain_id = ".$user->recent_domain." 
                AND q.status=2 
                AND qv.user_id is null" ;
            $query  = DB::select(DB::raw($sql));
            if(!empty($query)){
                $data->quotation = $query ;
            }
        }

       
        $data->domain = $domain;
        $data->pre_welcom = PreWelcome::find(1)->text;



        $likeRoleQuery = "";
        $searchQuery = "";
        $sql = "SELECT r.name as role_name 
                FROM role_user  ru
                JOIN roles r ON r.id=ru.role_id
                WHERE ru.id_card = '".Auth()->user()->id_card."' AND ru.domain_id=".$domainId ;
        $roles = DB::select(DB::raw($sql));
        if (!empty($roles)){
            $roleQuery = "";
            foreach ($roles as $key => $r) {
                $roleQuery .= " or p.public_role like '%".$r->role_name."%' ";
            }
            $roleQuery = substr($roleQuery,3);
            $likeRoleQuery .= " AND ( $roleQuery  ) ";
        }

        $searchQuery = " AND (now() BETWEEN  p.public_start_at  AND p.public_end_at  OR (now() >  p.public_start_at AND p.public_end_at is null) ) ".$likeRoleQuery ;
        $order = "  ORDER BY p.prioritize DESC,p.public_end_at ASC,p.public_start_at ASC";
        $data->posts = Post::getListData($domainId,3,$searchQuery,$order);

        return $this->respondWithItem($data);
    }

   
}
