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

class ManualInController extends ApiController
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
        $data['parking_manual_in'] = ParkingCheckIn::from('parking_checkin as pc')
                ->leftJoin('rooms as r', 'r.id', '=', 'pc.room_id')
                ->join('provinces as pv', 'pv.PROVINCE_ID', '=', 'pc.province_id')
                ->where(DB::raw("(DATE_FORMAT(now(),'%Y-%m'))"), '>=', DB::raw("DATE_FORMAT(pc.created_at,'%Y-%m')"))
                ->where('pc.domain_id', $domainId)
                ->whereNull('pc.used_at')
                ->where('pc.manual_in', 1)
                ->select(DB::raw("pc.* 
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                ,pv.PROVINCE_NAME as province_name"))
                ->orderBy('pc.created_at', 'desc')
                ->get();
        return $this->respondWithItem($data);
    }
  
    public function store(Request $request, $domainId)
    {
        $post = $request->except(['api_token']);

        $validator = $this->validator($post);
        if ($validator->fails()) {
            $request->except('api_token', '_method');
        }

        if (!isset($post['no_room'])) {
            $post['no_room'] = 0 ;
        }
        if ($post['no_room']==1) {
            $post['room_id'] = 0;
        }
        $parking = new ParkingCheckIn();
        $parking->license_plate = $post['license_plate'];
        $parking->license_plate_category = $post['license_plate_category'];
        $parking->province_id = $post['province_id'];
        $parking->room_id = $post['room_id'];
        $parking->domain_id = $domainId ;
        $parking->is_no_room = $post['no_room'];
        $parking->created_at = Carbon::now();
        $parking->created_by = Auth()->user()->id;
        $parking->manual_in = 1;
        $parking->save();
        return $this->respondWithItem(['text'=>'success']);
    }

    public function edit($domainId, $id)
    {
    }

    public function update(Request $request, $domainId, $Id)
    {
    }
    public function destroy(Request $request, $domainId, $Id)
    {
    }
    
    private function validator($data)
    {
        return Validator::make($data, [
            'license_plate' => 'required|string|max:4',
            'license_plate_category' => 'required|string|max:3',
            'province_id' => 'required|numeric',
        ]);
    }
}
