<?php

namespace App\Http\Controllers\API\Parking;

use App\Http\Controllers\ApiController;
use App\Models\Parking\ParkingBuy;
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

 
    public function index($domainId){
        $date = Date('Y-m'."-01");
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

                FROM parking_buy as pb
                JOIN parking_package as pp 
                ON pp.id = pb.package_id
                JOIN rooms as r 
                ON r.id=pb.room_id
                LEFT JOIN users u ON u.id=pb.created_by
                LEFT JOIN users ud ON u.id=pb.deleted_by
               LEFT JOIN (
                    SELECT parking_buy_id FROM parking_use WHERE domain_id=$domainId
                    GROUP BY parking_buy_id
                ) t2 
                ON t2.parking_buy_id = pb.id
                WHERE pb.domain_id = $domainId
                AND pb.expired_at > '$date'
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
   

    public function store(Request $request,$domainId){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        unset($post['api_token']);

       
        $periodAt = Carbon::parse($post['period_at'])->toDateTimeString() ;
        $expire = Carbon::parse($post['period_at'])->endOfMonth() ;
        $expire->setTime(23, 59, 59);

        $remainLimit = ParkingBuy::getRemain($domainId,$post['package_id'],$post['room_id'],$periodAt);
        if($remainLimit<=0){
             return $this->respondWithError($this->langMessage('ไม่สามารถซื้อแพ็คเกจนี้ได้เนื่องจากซื้อเต็มจำนวนสิทธิ์แล้ว','Cannot sell,this room max limit'));
        }


        unset($post['period_at']);

        $query = new ParkingBuy();
        $query->domain_id = $domainId ;
        $query->created_at = Carbon::now() ;
        $query->created_by = Auth()->user()->id ;
        $query->period_at = $periodAt ;
        $query->expired_at = $expire ;
      
        $query->fill($post)->save();
        return $this->respondWithItem(['parking_buy_id'=>$query->id]);
    }  

    public function edit(Request $request,$domainId,$id){
        $data['parking_buys'] = ParkingBuy::find($id) ;
        return $this->respondWithItem($data);
    }

    public function update(Request $request,$domainId,$Id){
        $post = $request->all();

        $query = ParkingBuy::find($Id) ;

        if(ParkingUse::checkUsed($domainId,$query->room_id,$query->id)){
            return $this->respondWithError($this->langMessage('มีการใช้คูปปองแล้วไม่สามารถทำการแก้ไขได้','Cannot edit,because this coupon used'));
        
        }

       
        $periodAt = Carbon::parse($post['period_at'])->toDateTimeString() ;
        $expire = Carbon::parse($post['period_at'])->endOfMonth() ;
        $expire->setTime(23, 59, 59);

        $remainLimit = ParkingBuy::getRemain($domainId,$post['package_id'],$post['room_id'],$periodAt,$Id);
        if($remainLimit<=0){
             return $this->respondWithError($this->langMessage('ไม่สามารถซื้อแพ็คเกจได้เนื่องจากซื้อเต็มจำนวนสิทธิ์แล้ว','Cannot sell,this room max limit'));
        }

       
        $query->period_at = $periodAt ;
        $query->expired_at = $expire ;
        unset($post['period_at']);
        $query->fill($post)->save();
        return $this->respondWithItem(['parking_buy_id'=>$Id]);
    } 
    public function destroy(Request $request,$domainId,$Id){
        $post = $request->all();
        $query = ParkingBuy::find($Id);
        if(ParkingUse::checkUsed($domainId,$query->room_id,$query->id)){
            return $this->respondWithError($this->langMessage('มีการใช้คูปปองแล้วไม่สามารถทำการลบได้','Cannot delete,because this coupon used'));
        
        }

       

        ParkingBuy::where('id',$id)->update(['deleted_by'=>Auth()->user()->id]);
        $query->delete();
      
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
