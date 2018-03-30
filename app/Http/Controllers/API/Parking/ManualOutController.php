<?php

namespace App\Http\Controllers\API\Parking;

use App\Http\Controllers\ApiController;
use App\Models\Parking\ParkingCheckIn;
use App\Models\Parking\ParkingDebt;
use App\Models\Parking\ParkingHistory;
use App\Models\Parking\ParkingPackage;
use App\Models\Parking\ParkingUse;
use App\Models\Setting;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class ManualOutController extends ApiController
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

    
    public function index($domainId)
    {
        if (!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError($this->langMessage('คุณไม่มีสิทธิ์เข้าใช้งานส่วนนี้ค่ะ', 'not permission'));
        }
        $data['parking_manual_in'] = ParkingCheckIn::from('parking_checkin as pc')
                ->leftJoin('rooms as r', 'r.id', '=', 'pc.room_id')
                ->join('provinces as pv', 'pv.PROVINCE_ID', '=', 'pc.province_id')
                ->where(DB::raw("(DATE_FORMAT(now(),'%Y-%m'))"), '>=', DB::raw("DATE_FORMAT(pc.created_at,'%Y-%m')"))
                ->where('pc.domain_id', $domainId)
                ->whereNull('pc.used_at')
                ->select(DB::raw("pc.* 
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                ,pv.PROVINCE_NAME as province_name"))
                ->orderBy('pc.created_at', 'desc')
                ->get();

        $data['parking_manual_out'] = ParkingCheckIn::from('parking_checkin as pc')
                ->leftJoin('rooms as r', 'r.id', '=', 'pc.room_id')
                ->join('provinces as pv', 'pv.PROVINCE_ID', '=', 'pc.province_id')
                ->where(DB::raw("(DATE_FORMAT(now(),'%Y-%m'))"), '>=', DB::raw("DATE_FORMAT(pc.created_at,'%Y-%m')"))
                ->where('pc.domain_id', $domainId)
                ->where('pc.manual_out', 1)
                ->select(DB::raw("pc.* 
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                ,pv.PROVINCE_NAME as province_name"))
                ->orderBy('pc.created_at', 'desc')
                ->get();
              
        return $this->respondWithItem($data);
    }
  
    public function store(Request $request, $domainId)
    {
        if (!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError($this->langMessage('คุณไม่มีสิทธิ์เข้าใช้งานส่วนนี้ค่ะ', 'not permission'));
        }
        $post = $request->except(['api_token']);
        $userId = Auth()->user()->id;


        $id = $post['parking_checkin_id'];
        $parking = ParkingCheckIn::find($id);
        if ($parking->manual_out==1) {
            return $this->respondWithError($this->langMessage('ข้อมูลนี้มีการบันทึกออกแล้ว', 'this data used'));
        }
        $startDate = $parking->created_at ;
        $endDate = $post['date'] ;
        $carbonNow = Carbon::now();
        $debtHour = ceil((strtotime($endDate)-strtotime($startDate))/(60*60)) ;
       
        if ($debtHour>0) {
            $parkingDebt = new ParkingDebt();
            $parkingDebt->license_plate = $parking->license_plate;
            $parkingDebt->license_plate_category = $parking->license_plate_category;
            $parkingDebt->debt_hour = $debtHour ;
            $parkingDebt->parking_use_id = 0 ;
            $parkingDebt->parking_checkin_id = $id ;
            $parkingDebt->room_id = $parking->room_id;
            $parkingDebt->domain_id = $domainId ;
            $parkingDebt->province_id = $parking->province_id;
            $parkingDebt->debt_type = $post['debt_type'] ;
            $parkingDebt->start_date = Carbon::parse($startDate);
            $parkingDebt->end_date = Carbon::parse($endDate);
            $parkingDebt->created_at = $carbonNow;
            $parkingDebt->created_by = $userId;
            $parkingDebt->save();
        }
        $parking->hour_use=$debtHour;
        $parking->used_at = $carbonNow;
        $parking->outed_at = Carbon::parse($endDate);
        $parking->manual_out = 1;
        $parking->save();
        return $this->respondWithItem(['text'=>'success']);
    }

    public function edit($domainId, $id)
    {
    }

    public function update(Request $request, $domainId, $Id)
    {
        if (!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError($this->langMessage('คุณไม่มีสิทธิ์เข้าใช้งานส่วนนี้ค่ะ', 'not permission'));
        }
        $post = $request->all();

        unset($post['_method']);
        unset($post['api_token']);

        $query = ParkingPackage::find($Id) ;
        if (empty($query)) {
            $query = new ParkingPackage();
        }
        $query->fill($post)->save();
        return $this->respondWithItem(['parking_package_id'=>$Id]);
    }
    public function destroy(Request $request, $domainId, $Id)
    {
    }
    
    private function validator($data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'hour' => 'required|numeric',
            'price' => 'required|numeric',
        ]);
    }
}
