<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Notification;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationController extends ApiController
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
        // $this->middleware('auth:api');
    }

  

    public function index()
    {
        $data = auth()->user()->getNotification() ;
        return $this->respondWithItem($data);
    }
   
    public function show()
    {
    }

    public function store(Request $request)
    {
        $post = $request->except('api_token', '_method');

        $sqlUpdate = "";
        if (isset($post['noti_player_id'])) {
            $sqlUpdate .= " noti_player_id ='".$post['noti_player_id']."'" ;

            $sql = "UPDATE user_domains SET noti_player_id = null 
                WHERE domain_id =".Auth()->user()->recent_domain." 
                AND noti_player_id='".$post['noti_player_id']."'" ;
            $query = DB::update(DB::raw($sql));
        }
        if (isset($post['noti_player_id_mobile'])) {
            $sql = "UPDATE user_domains SET noti_player_id_mobile = null 
                WHERE domain_id =".Auth()->user()->recent_domain." 
                AND noti_player_id_mobile='".$post['noti_player_id_mobile']."'" ;
            $query = DB::update(DB::raw($sql));

            if (!empty($sqlUpdate)) {
                $sqlUpdate .= " , noti_player_id_mobile ='".$post['noti_player_id_mobile']."'" ;
            } else {
                $sqlUpdate .= "  noti_player_id_mobile ='".$post['noti_player_id_mobile']."'" ;
            }
        }


        
       

        $sql = "UPDATE user_domains SET $sqlUpdate
                WHERE id_card ='".Auth()->user()->id_card."'
                AND domain_id =".Auth()->user()->recent_domain ;
        $query = DB::update(DB::raw($sql));
       
        $data['users'] =  auth()->user();
        return $this->respondWithItem($data);
    }

    public function edit(Request $request, $addressId)
    {
    }
    
    
    public function update(Request $request, $addressId)
    {
    }
  
    public function destroy()
    {
        Notification::where('id_card', Auth()->user()->id_card)
        ->where('domain_id', Auth()->user()->recent_domain)
        ->delete();
         $data['notification'] = [] ;
        return $this->respondWithItem($data);
    }

    public function seen()
    {
        Notification::where('id_card', Auth()->user()->id_card)
        ->where('domain_id', Auth()->user()->recent_domain)
        ->update(['seen'=>1]);
      

        if (Auth()->user()->hasRole('admin')) {
             Notification::where('type', 0)
            ->where('domain_id', Auth()->user()->recent_domain)
            ->update(['seen'=>1]);
        }

        $data = Auth()->user()->getNotification() ;

        return $this->respondWithItem($data);
    }
}
