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


class UseController extends ApiController
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

    public function index($domainId,$roomId){
        // DB::enableQueryLog();
       
        // $data['parking_use'] = ParkingUse::from('parking_use as pu')
        // ->join('users as u','u.id','pu.created_by')
        // ->leftjoin('users as ud','ud.id','pu.deleted_by')
        // ->where('pu.domain_id',$domainId)
        // ->where('pu.room_id',$roomId)
        // ->select(DB::raw('pu.*,u.first_name,u.last_name,ud.first_name as deleted_first_name,ud.last_name as deleted_last_name'))
        // ->get();

        $sql = "SELECT pu.*,u.first_name,u.last_name
                ,ud.first_name as deleted_first_name
                ,ud.last_name as deleted_last_name
                ,pv.PROVINCE_NAME as province_name
                FROM parking_use as pu  
                JOIN users as u 
                ON u.id = pu.created_by 
                LEFT JOIN users as ud 
                ON ud.id = pu.deleted_by 
                JOIN provinces as pv 
                ON pv.PROVINCE_ID = pu.province_id 
                WHERE pu.room_id=$roomId 
                AND pu.domain_id=$domainId " ;
        // echo "$sql";die;
      
        $data['parking_use']  =  DB::select(DB::raw($sql));

        // var_dump(DB::getQueryLog());die;
        // $data['remain_hour'] = ParkingUse::getRemainHour($roomId,date('Y-m').'-01');

         $sql = "SELECT t1.id,t1.total_hour,t1.period_at,IFNULL(t2.used_hour,0) as used_hour , (t1.total_hour-IFNULL(t2.used_hour,0)) as remain_hour  FROM (
                SELECT pb.id,SUM(pp.hour) as total_hour 
                ,pb.period_at
                FROM parking_buy pb  
                JOIN parking_package pp 
                ON pb.package_id = pp.id 
                WHERE pb.room_id=$roomId AND pb.domain_id=$domainId
                AND DATE(pb.expired_at) > '".date('Y-m').'-01'."'
                AND pb.deleted_at is null 
                GROUP BY pb.id,pb.period_at ) t1
                LEFT JOIN (
                    SELECT parking_buy_id, sum(hour_use) as used_hour FROM parking_use
                    WHERE room_id=$roomId AND domain_id= $domainId AND is_until_out=0
                    GROUP BY parking_buy_id    
                    UNION ALL
                    SELECT t1.parking_buy_id , sum(TIMESTAMPDIFF(HOUR, t1.start_date, CASE WHEN DATE_FORMAT(now(), '%i') > 1 THEN DATE_FORMAT(  DATE_ADD(NOW(), INTERVAL 1 HOUR), '%Y-%m-%d %H:00:00') ELSE DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')  END )) as used_hour
                    FROM (
                    SELECT parking_buy_id ,start_date
                     , CASE WHEN  start_date < now() THEN 1
                       ELSE 0 END as used_hour
                    FROM parking_use
                    WHERE room_id=$roomId AND domain_id= $domainId AND is_until_out=1 AND end_date is null
                    ) t1
                    WHERE t1.used_hour > 0
                    GROUP BY t1.parking_buy_id 
                    UNION ALL 
                    SELECT parking_buy_id,SUM(TIMESTAMPDIFF(HOUR, start_date, end_date)) as used_hour 
                    FROM parking_use
                    WHERE room_id=$roomId AND domain_id= $domainId AND is_until_out=1  AND end_date is not null
                    GROUP BY parking_buy_id 

                ) t2 
                ON t2.parking_buy_id = t1.id
                 WHERE (t1.total_hour-IFNULL(t2.used_hour,0)) > 0
                ORDER BY t1.id ASC" ;
        
      
        $data['parking_buy_from_room']  =  DB::select(DB::raw($sql));


        return $this->respondWithItem($data);
    } 

    public function store(Request $request,$domainId,$roomId){
        $post = $request->all();
       
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        unset($post['api_token']);

        $post['is_until_out'] = (!isset($post['is_until_out']) || $post['is_until_out']=="false" )  ? 0 : 1 ;



       
        // if(date('i',strtotime($post['end_date'])) > 0 ){
        //     $post['end_date'] = date("Y-m-d H", strtotime($post['end_date'].'+1 hours')).":00";
        // }

        $diffInHour = $post['hour_use'] ;

        $post['end_date'] = date("Y-m-d H:i", strtotime($post['start_date'].'+'.$diffInHour.' hours'));

        if($post['is_until_out']){
            $post['end_date'] = null ;

         
       
            $sql = "SELECT pu.id
                    ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                    FROM parking_use pu 
                    LEFT JOIN rooms r ON r.id=pu.room_id
                    WHERE license_plate=".$post['license_plate']."
                    AND license_plate_category='".$post['license_plate_category']."' 
                    AND province_id=".$post['province_id']."
                    AND used_date is null 
                    AND end_date is null";
            $block =  collect(DB::select(DB::raw($sql)))->first();

            if(!empty($block)){
                return $this->respondWithError($this->langMessage('ไม่สามาถใช้อี-คูปอง จนกว่าจะออก สำหรับ รถทะเบียนคันนี้ได้ เนื่องจากมีการใช้ อี-คูปอง จนกว่าจะออก ของห้อง '.$block->room_name.' อยู่','Wrong license plate used by room '.$block->room_name));
            }
        }
       
        // $diff = strtotime($post['end_date'])-strtotime($post['start_date']);
        // $diffInHour = ceil($diff/3600) ;
        if($diffInHour<=0){
            return $this->respondWithError($this->langMessage('ระบุเวลาให้ถูกต้อง','Wrong date time'));
        }

        // $remainHour = ParkingUse::getRemainHour($roomId,date('Y-m',strtotime($post['start_date'])).'-01');
        $remainHour = ParkingUse::getPackageRemainHour($roomId,$post['package_id']);
        if($remainHour < $diffInHour){
            return $this->respondWithError($this->langMessage('ชั่วโมงคงเหลือไม่เพียงพอ','Remain hour not enough'));
        }

        $query = new ParkingUse();
        $query->room_id = $roomId;
        $query->domain_id = $domainId;
        $query->start_date = Carbon::Parse($post['start_date']) ;
        $query->end_date = isset($post['end_date']) ?  Carbon::Parse($post['end_date']) : null ;
        $query->license_plate =  intval($post['license_plate']) ;
        $query->license_plate_category =  $post['license_plate_category'] ;
        $query->created_at = Carbon::now();
        $query->created_by = Auth()->user()->id ;
        $query->province_id = $post['province_id'] ;
        $query->parking_buy_id = $post['package_id'] ;
        $query->hour_use = $diffInHour ;
        $query->is_until_out = $post['is_until_out'] ;
    

        $query->save();

        // $packageUseId =  $query->id ;

        // $sql = "SELECT t1.id,t1.total_hour,IFNULL(t2.used_hour,0) as used_hour , (t1.total_hour-IFNULL(t2.used_hour,0)) as remain_hour  FROM (
        //         SELECT pb.id,SUM(pp.hour) as total_hour 
        //         FROM parking_buy pb  
        //         JOIN parking_package pp 
        //         ON pb.package_id = pp.id 
        //         WHERE pb.room_id=$roomId AND pb.domain_id= $domainId
        //         AND DATE(pb.period_at) = '".date('Y-m',strtotime($post['start_date'])).'-01'."'
        //         GROUP BY pb.id ) t1
        //         LEFT JOIN (
        //              SELECT parking_buy_id, sum(hour_use) as used_hour FROM parking_use_history
        //         WHERE room_id=$roomId  AND domain_id= $domainId
        //         GROUP BY parking_buy_id
        //         ) t2 
        //         ON t2.parking_buy_id = t1.id
        //          WHERE (t1.total_hour-IFNULL(t2.used_hour,0)) > 0
        //         ORDER BY t1.id DESC";
   
        // $check =  DB::select(DB::raw($sql));
        // if(empty($check)){
        //      return $this->respondWithError($this->langMessage('ชั่วโมงคงเหลือไม่เพียงพอ','Remain hour not enough'));
        // }

        // $setHour = $diffInHour ;

        // foreach ($check as $key => $c) {
        //     if($c->remain_hour >=  $setHour ){
        //         $history[$key]['hour_use'] =  $setHour;
        //         $history[$key]['parking_buy_id'] =  $c->id;
        //         $setHour -= $diffInHour ;
        //     }elseif($c->remain_hour <  $setHour ){
        //         $history[$key]['hour_use'] =  $c->remain_hour ;
        //         $history[$key]['parking_buy_id'] =  $c->id;
        //         $setHour -= $c->remain_hour ;
        //     }

        //     $history[$key]['parking_use_id'] =  $packageUseId;
        //     $history[$key]['created_at'] =  Carbon::now();
        //     $history[$key]['created_by'] =  Auth()->User()->id;
        //     $history[$key]['room_id'] =  $roomId;
        //     $history[$key]['domain_id'] =  $domainId;


        //     if($setHour==0){
        //         break;
        //     }

        // }

        // ParkingUseHistory::insert($history);

     



        return $this->respondWithItem(['parking_use_id'=>$query->id]);
    }  
    public function update(Request $request,$domainId,$Id){
        // $post = $request->all();
        // $query = ParkingUse::find($Id) ;
        // if(empty($query)){
        //     $query = new ParkingUse();
        // }
        // $query->fill($post)->save();
        // return $this->respondWithItem(['parking_use_id'=>$Id]);
    } 
    public function destroy(Request $request,$domainId,$roomId,$id){
        // ParkingUseHistory::where('parking_use_id',$id)->delete();
        ParkingUse::where('id',$id)->update(['deleted_by'=>Auth()->user()->id]);
        $query = ParkingUse::where('id',$id)->delete();
      
        return $this->respondWithItem(['parking_use_id'=>$id]);
    } 
    
    public function getPackage(Request $request,$domainId,$roomId){
        $post = $request->all();
        $query = ParkingBuy::from('parking_buy as pb')
                ->join('parking_package as pp','pp.id','=','pb.package_id')
                ->where('pb.domain_id',$domainId)
                ->where('pb.room_id',$roomId)
                // ->where('pb.expired_at','>',Carbon::now())
                ->select(DB::raw("pb.id,pb.room_id,pb.package_id,pp.name as package_name,pp.price as package_price,pb.created_at,pb.expired_at"))
                ->get();
        $data['parking_buy_user'] = $query;
        return $this->respondWithItem($data);
    } 
    public function getRemainHour(Request $request,$domainId,$roomId){
        $post = $request->all();
         
        $data['remain_hour'] = ParkingUse::getRemainHour($roomId,$post['month_year'].'-01');

        return $this->respondWithItem($data);
    } 
    

    private function validator($data)
    {
        return Validator::make($data, [
            'license_plate' => 'required|max:4',
            'license_plate_category' => 'required|max:3',
            'province_id' => 'required',
            'package_id' => 'required',
            'start_date' => 'required',
            'hour_use' => 'required',
        ]);
    }
    
}
