<?php

namespace App\Http\Controllers\API\Parking;

use App\Http\Controllers\ApiController;
use App\Models\Parking\ParkingBuy;
use App\Models\Parking\ParkingPackage;
use App\Models\Parking\ParkingUse;
use App\Models\Parking\ParkingUseHistory;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class BuyController extends ApiController
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
       
        $sql = "SELECT pb.id
                ,pb.room_id
                ,pb.package_id
                ,pp.name as package_name
                ,pp.price as package_price
                ,pb.created_at
                ,pb.expired_at
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                ,pb.period_at
                ,pb.deleted_at
                ,pb.user_buy_name 
                ,pb.id_card_buyer 
                ,pb.buyer_tel
                ,CASE WHEN t2.parking_buy_id is not null THEN 1 ELSE 0 END as coupon_used
                ,u.first_name,u.last_name
                ,ud.first_name as delete_first_name,ud.last_name as delete_last_name
                ,pb.is_offline
                FROM parking_buy as pb
                JOIN parking_package as pp 
                ON pp.id = pb.package_id
                JOIN rooms as r 
                ON r.id=pb.room_id
                LEFT JOIN users u ON u.id=pb.created_by
                LEFT JOIN users ud ON ud.id=pb.deleted_by
                LEFT JOIN (
                    SELECT parking_buy_id FROM parking_use WHERE domain_id=$domainId
                    GROUP BY parking_buy_id
                ) t2 
                ON t2.parking_buy_id = pb.id
                WHERE pb.domain_id = $domainId
                AND pp.deleted_at is null
                ORDER BY pb.created_at DESC
                " ;
        $data['parking_buys']  =  DB::select(DB::raw($sql));
        // $data['parking_buys']  = ParkingBuy::from('parking_buy as pb')
        //                         ->join('parking_package as pp','pp.id','=','pb.package_id')
        //                         ->join('rooms as r','r.id','=','pb.room_id')
        //                         ->where('pb.domain_id',$domainId)
        //                         ->where('pb.expired_at','>',Carbon::now()->toDateTimeString())
        //                         ->select(DB::raw("pb.id,pb.room_id,pb.package_id,pp.name as package_name,pp.price as package_price,pb.created_at,pb.expired_at,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name,pb.period_at,pb.user_buy_name"))
        //                         ->get();
        return $this->respondWithItem($data);
    }
   

    public function store(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        unset($post['api_token']);
        


        $periodAt = Carbon::parse($post['period_at'])->toDateTimeString() ;
        $expire = Carbon::parse($post['period_at'])->endOfMonth() ;
        $expire->setTime(23, 59, 59);

        if ($post['is_offline']!=1) {
            $remainLimit = ParkingBuy::getRemain($domainId, $post['package_id'], $post['room_id'], $periodAt);
            if ($remainLimit<=0) {
                 return $this->respondWithError($this->langMessage('ไม่สามารถซื้อแพ็คเกจนี้ได้เนื่องจากซื้อเต็มจำนวนสิทธิ์แล้ว', 'Cannot sell,this room max limit'));
            }
        } else {
            $post['package_id'] = 0 ;   //--- คูปองกระดาษ
        }

        unset($post['period_at']);

        $query = new ParkingBuy();
        $query->domain_id = $domainId ;
        $query->created_at = Carbon::now() ;
        $query->created_by = Auth()->user()->id ;
        $query->period_at = $periodAt ;
        $query->expired_at = $expire ;
        $query->fill($post)->save();
        $buyId = $query->id ;
        if ($post['is_offline']==1) {
            // $package = ParkingPackage::find($post['package_id']);
            $query = new ParkingUse();
            $query->room_id = $post['room_id'];
            $query->domain_id = $domainId;
            $query->start_date = Carbon::now() ;
            $query->end_date = Carbon::now()  ;
            $query->license_plate =  "" ;
            $query->license_plate_category =  "" ;
            $query->created_at = Carbon::now();
            $query->created_by = Auth()->user()->id ;
            $query->province_id = 0 ;
            $query->parking_buy_id = $buyId ;
            $query->parking_checkin_id = 0;
            $query->hour_use = 0 ;
            $query->is_until_out = 0 ;
            $query->used_date = Carbon::now() ;
            $query->save();
        }
        return $this->respondWithItem(['parking_buy_id'=>$buyId]);
    }

    public function edit(Request $request, $domainId, $id)
    {
        $data['parking_buys'] = ParkingBuy::find($id) ;
        return $this->respondWithItem($data);
    }

    public function update(Request $request, $domainId, $Id)
    {
        $post = $request->except('api_token', '_method');

        $query = ParkingBuy::find($Id) ;

        if (ParkingUse::checkUsed($domainId, $query->room_id, $query->id)) {
            return $this->respondWithError($this->langMessage('มีการใช้คูปปองแล้วไม่สามารถทำการแก้ไขได้', 'Cannot edit,because this coupon used'));
        }

       
        $periodAt = Carbon::parse($post['period_at'])->toDateTimeString() ;
        $expire = Carbon::parse($post['period_at'])->endOfMonth() ;
        $expire->setTime(23, 59, 59);

        $remainLimit = ParkingBuy::getRemain($domainId, $post['package_id'], $post['room_id'], $periodAt, $Id);
        if ($remainLimit<=0) {
             return $this->respondWithError($this->langMessage('ไม่สามารถซื้อแพ็คเกจได้เนื่องจากซื้อเต็มจำนวนสิทธิ์แล้ว', 'Cannot sell,this room max limit'));
        }

       
        $query->period_at = $periodAt ;
        $query->expired_at = $expire ;
        unset($post['period_at']);
        $query->fill($post)->save();
        return $this->respondWithItem(['parking_buy_id'=>$Id]);
    }
    public function destroy(Request $request, $domainId, $Id)
    {
        $post = $request->except('api_token', '_method');
        $query = ParkingBuy::join('parking_package as pp','pp.id','=','parking_buy.package_id')
        ->select(DB::raw('parking_buy.*,pp.hour as total_hour'))
        ->where('parking_buy.id',$Id)
        ->first();

        if(empty($query)){
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูล', 'not found this data'));
        }

        $roomId = $query->room_id ;
        $remain = ParkingUse::getRemainHourByDate($roomId, date('Y-m-d', strtotime($query->created_at)));
        if($remain<$query->total_hour){
            return $this->respondWithError($this->langMessage('มีการใช้คูปปองแล้วไม่สามารถทำการลบได้', 'Cannot delete,because this coupon used'));
        }

        ParkingBuy::where('id', $Id)->update(['deleted_by'=>Auth()->user()->id]);
        ParkingBuy::find($Id)->delete();
      
        return $this->respondWithItem(['parking_buy_id'=>$Id]);
    }
    

    private function validator($data)
    {
        return Validator::make($data, [
            'room_id' => 'required|numeric',
            'package_id' => 'required|numeric',
        ]);
    }
}
