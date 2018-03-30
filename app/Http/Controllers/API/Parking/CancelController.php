<?php

namespace App\Http\Controllers\API\Parking;

use App\Http\Controllers\ApiController;
use App\Models\Parking\ParkingCheckIn;
use App\Models\Parking\ParkingDebt;
use App\Models\Parking\ParkingHistory;
use App\Models\Parking\ParkingPackage;
use App\Models\Parking\ParkingUse;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class CancelController extends ApiController
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
    }

    
    public function index(Request $request, $domainId)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $whereQuery = " ";
        if (isset($startDate)&&isset($endDate)) {
            $whereQuery = " AND (DATE_FORMAT(used_at ,'%Y-%m') BETWEEN DATE_FORMAT($startDate ,'%Y-%m') AND DATE_FORMAT($endDate ,'%Y-%m') ) ";
        }
        $sql = "SELECT pu.*,u.first_name,u.last_name
               
                ,pv.PROVINCE_NAME as province_name
                ,CONCAT( IFNULL(r.name_prefix,''),IFNULL(r.name,''),IFNULL(r.name_surfix,'') ) as room_name
                FROM parking_checkin as pu 
                LEFT JOIN rooms as r 
                ON r.id = pu.room_id
                LEFT JOIN users as u 
                ON u.id = pu.created_by 
                JOIN provinces as pv 
                ON pv.PROVINCE_ID = pu.province_id 
                WHERE  
                 pu.domain_id = $domainId
                AND pu.used_at is not null
                $whereQuery 
                ORDER BY pu.used_at DESC ";
        $data['parking_checkout_list']  = DB::select(DB::raw($sql));
       
        $sql = "SELECT pc.*,u.first_name,u.last_name
                ,pv.PROVINCE_NAME as province_name
                ,CONCAT( IFNULL(r.name_prefix,''),IFNULL(r.name,''),IFNULL(r.name_surfix,'') ) as room_name
                FROM parking_history as ph 
                JOIN parking_checkin as pc
                ON pc.id = ph.parking_checkin_id
                LEFT JOIN rooms as r 
                ON r.id = pc.room_id
                LEFT JOIN users as u 
                ON u.id = ph.created_by 
                JOIN provinces as pv 
                ON pv.PROVINCE_ID = pc.province_id 
                WHERE  ph.status=2
                AND pc.domain_id = $domainId
               ";
        $data['parking_cancel_history']  = DB::select(DB::raw($sql));

        return $this->respondWithItem($data);
    }
  

    public function store(Request $request, $domainId)
    {
    }

    public function edit($domainId, $id)
    {
    }

    public function update(Request $request, $domainId, $Id)
    {
    }
    public function destroy(Request $request, $domainId, $id)
    {
        $post = $request->except('api_token', '_method');

         DB::beginTransaction();

        // $parkingHistory = ParkingHistory::where('parking_use_id',$id)->where('status',1)->orderBy('id','desc')->first();
        $query = ParkingCheckIn::find($id) ;
        if (empty($query)) {
             DB::rollBack();
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูลนี้', 'not found this user'));
        }

        $carbonNow = Carbon::now();
        $userId = Auth()->user()->id;

        ParkingDebt::where('parking_checkin_id', $query->id)->whereNull('deleted_at')->update(['deleted_at'=>$carbonNow,'deleted_by'=>$userId]) ;


        // $query = ParkingUse::where('parking_checkin_id',$query->id)->update(['deleted_at'=>$carbonNow,'deleted_by'=>$userId]) ;

        ParkingCheckIn::find($id)->update(['outed_at'=>null,'used_at'=>null,'hour_use'=>0]) ;
        $history['parking_checkin_id'] = $id ;
        $history['parking_use_id'] = 0 ;
        $history['domain_id'] = $domainId ;
        $history['created_at'] = $carbonNow;
        $history['created_by'] = $userId;
        $history['status'] = 2;

        ParkingHistory::insert($history);
        DB::commit();
        return $this->respondWithItem(['text'=>'success']);
    }
}
