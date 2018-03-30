<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Amphur;
use App\Models\District;
use App\Models\Domain;
use App\Models\Images;
use App\Models\Province;
use App\Models\Role;
use App\Models\Room;
use App\Models\UserTemp;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class RequestRoomController extends ApiController
{
    private $view = 'admin.request_room';
    private $title = 'โครงการ';
    private $route = 'request-room';
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

    //use AuthenticatesUsers;

   

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('auth');
    }


    public function index($domainId)
    {
        try {
            $sql = "SELECT  u.username,u.id_card,u.first_name,u.last_name
                    ,u.created_at,ur.approve
                    ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                    FROM domains d
                    INNER JOIN user_domains ud ON ud.domain_id = d.id
                    INNER JOIN users u on u.id_card = ud.id_card
                    INNER JOIN rooms r ON r.domain_id = d.id
                    INNER JOIN user_rooms ur on r.id = ur.room_id 
                    AND u.id_card = ur.id_card
                    WHERE d.id=$domainId AND ur.approve = 0 AND ud.approve = 1 AND  u.id_card not in ( SELECT id_card FROM role_user WHERE role_id = 7  )" ;
            $query = DB::select(DB::raw($sql));
            $user = [];
            if (!empty($query)) {
                foreach ($query as $q) {
                    $user[$q->id_card]['id_card'] = $q->id_card;
                    $user[$q->id_card]['username'] = $q->username;
                    $user[$q->id_card]['first_name'] = $q->first_name;
                    $user[$q->id_card]['last_name'] = $q->last_name;
                    $user[$q->id_card]['created_at'] = $q->created_at;
                    $user[$q->id_card]['room'][] = $q->room_name;
                }
            }
            $data['user'] = array_values($user);
            ;
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
       
        return $this->respondWithItem($data);
    }

   
    private function validator($data)
    {
        return Validator::make($data, [
           'id_card' => 'required|string|max:13',
           'first_name' => 'required|string|max:255',
           'last_name' => 'required|string|max:255',
           'email' => 'required|string|email|max:255',
            
        ]);
    }

    private function validatorUniq($data)
    {

        return Validator::make($data, [
            'id_card' => 'required|string|max:13|unique:user_temps',
        ]);
    }
    private function validatorRoom($data)
    {
        return Validator::make($data, [
            'room' => 'required|string|max:255'
        ]);
    }

    private function uploadImg($request, $domainId)
    {
        return  uploadfile($request, 'file_upload', $domainId) ;
    }
}
