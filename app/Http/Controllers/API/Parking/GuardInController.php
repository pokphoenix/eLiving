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

class GuardInController extends ApiController
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
       
         $sql = "SELECT pu.* 
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                ,pv.PROVINCE_NAME as province_name
                FROM parking_use  pu
                JOIN rooms as r 
                ON r.id=pu.room_id
                 JOIN provinces as pv 
                ON pv.PROVINCE_ID = pu.province_id 
                WHERE  DATE_FORMAT(now() ,'%Y-%m-%d') = DATE_FORMAT(pu.start_date ,'%Y-%m-%d')
                AND pu.domain_id = $domainId
                AND pu.used_date is null AND pu.deleted_at is null";
        $data['parking_guard_search']  = DB::select(DB::raw($sql));
       
        return $this->respondWithItem($data);
    }
  
    
    public function store(Request $request, $domainId)
    {
        $post = $request->except(['api_token']);
       
        if (!isset($post['no_room'])) {
            $post['no_room'] = 0 ;
        }

        if ($post['no_room']==1) {
            $post['room_id'] = 0;
        }


        $parkingDebt = new ParkingCheckIn();
        $parkingDebt->license_plate = $post['license_plate'];
        $parkingDebt->license_plate_category = $post['license_plate_category'];
        $parkingDebt->province_id = $post['province_id'];
        $parkingDebt->room_id = $post['room_id'];
        $parkingDebt->domain_id = $domainId ;
        $parkingDebt->is_no_room = $post['no_room'];
        $parkingDebt->created_at = Carbon::now();
        $parkingDebt->created_by = Auth()->user()->id;
        $parkingDebt->save();
        return $this->respondWithItem(['text'=>'success']);
    }

    public function edit($domainId, $id)
    {
    }

    public function update(Request $request, $domainId, $Id)
    {
        $post = $request->except('api_token', '_method');

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
    
    public function search(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $sqlLike = "";
        if (isset($post['license_plate_category'])&& !empty($post['license_plate_category'])) {
            $sqlLike .= " pu.license_plate_category like '%".$post['license_plate_category']."%'" ;
        }
        if (isset($post['license_plate'])&& !empty($post['license_plate'])) {
            if (!empty($sqlLike)) {
                $sqlLike .= " OR " ;
            }
            $sqlLike .= " pu.license_plate like '%".$post['license_plate']."%'" ;
        }

        if (!empty($sqlLike)) {
            $sqlLike = "  AND (
                    $sqlLike
                ) ";
        }

      

        // $sqlLike = " license_plate_category like '%".$post['license_plate_category']."%'  OR license_plate like '%".$post['license_plate']."%'" ;

        $sql = "SELECT pu.* 
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                ,pv.PROVINCE_NAME as province_name
                FROM parking_use  pu
                JOIN rooms as r 
                ON r.id=pu.room_id
                JOIN provinces as pv 
                ON pv.PROVINCE_ID = pu.province_id 
                WHERE DATE_FORMAT(now() ,'%Y-%m') = DATE_FORMAT(pu.start_date ,'%Y-%m')
                 $sqlLike
                AND pu.domain_id = $domainId
                AND pu.used_date is null 
                AND pu.deleted_at is null";
        $query = DB::select(DB::raw($sql));
        return $this->respondWithItem(['parking_guard_search'=>$query]);
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
