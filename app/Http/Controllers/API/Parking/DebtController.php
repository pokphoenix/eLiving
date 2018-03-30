<?php

namespace App\Http\Controllers\API\Parking;

use App\Http\Controllers\ApiController;
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

class DebtController extends ApiController
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
        $startDate = $request->input('start_date', strtotime(date('Y-m-d 00:00')));
        $endDate = $request->input('end_date', strtotime(date('Y-m-d 23:59:59')));


        $sql = "SELECT pd.*,u.first_name,u.last_name
                ,ud.first_name as deleted_first_name
                ,ud.last_name as deleted_last_name
                ,pv.PROVINCE_NAME as province_name
                ,CONCAT( IFNULL(r.name_prefix,''),IFNULL(r.name,''),IFNULL(r.name_surfix,'') ) as room_name
                FROM parking_debt as pd  
                LEFT JOIN rooms as r 
                ON r.id = pd.room_id
                LEFT JOIN users as u 
                ON u.id = pd.created_by 
                LEFT JOIN users as ud 
                ON ud.id = pd.deleted_by 
                JOIN provinces as pv 
                ON pv.PROVINCE_ID = pd.province_id 
                WHERE  pd.domain_id = $domainId
                AND pd.deleted_at is null
                AND pd.start_date between from_unixtime($startDate) AND from_unixtime($endDate)";
        $data['parking_debt']  = DB::select(DB::raw($sql));
       
       
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

        // $parkingHistory = ParkingHistory::where('parking_use_id',$id)->where('status',1)->orderBy('id','desc')->first();
        $query = ParkingUse::find($id) ;

      

        ParkingDebt::where('parking_use_id', $query->id)->whereNull('deleted_at')->update(['deleted_at'=>Carbon::now(),'deleted_by'=>Auth()->user()->id]) ;


        $query = ParkingUse::find($id)->update(['used_date'=>null]);


        $history['parking_use_id'] = $id ;
        $history['domain_id'] = $domainId ;
        $history['created_at'] = Carbon::now();
        $history['created_by'] = Auth()->user()->id;
        $history['start_date'] = null;
        $history['end_date'] = null;
        $history['status'] = 2;

        ParkingHistory::insert($history);

        return $this->respondWithItem(['text'=>'success']);
    }
}
