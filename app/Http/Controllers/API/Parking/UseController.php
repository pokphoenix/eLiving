<?php

namespace App\Http\Controllers\API\Parking;

use App\Http\Controllers\ApiController;
use App\Models\Parking\ParkingBuy;
use App\Models\Parking\ParkingCheckIn;
use App\Models\Parking\ParkingDebt;
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

    public function index($domainId, $roomId)
    {
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
                AND pu.domain_id=$domainId 
               " ;
        // echo "$sql";die;
      
        $data['parking_use']  =  DB::select(DB::raw($sql));

        // var_dump(DB::getQueryLog());die;
        // $data['remain_hour'] = ParkingUse::getRemainHour($roomId,date('Y-m').'-01');

        //  $sql = "SELECT t1.id,t1.total_hour,t1.period_at,t1.package_name,IFNULL(t2.used_hour,0) as used_hour , (t1.total_hour-IFNULL(t2.used_hour,0)) as remain_hour  FROM (
        //         SELECT pb.id,SUM(pp.hour) as total_hour
        //         ,pb.period_at
        //         ,pp.name as package_name
        //         FROM parking_buy pb
        //         JOIN parking_package pp
        //         ON pb.package_id = pp.id
        //         WHERE pb.room_id=$roomId AND pb.domain_id=$domainId
        //         AND DATE(pb.expired_at) > '".date('Y-m').'-01'."'
        //         AND pb.deleted_at is null
        //         GROUP BY pb.id,pp.name,pb.period_at ) t1
        //         LEFT JOIN (
        //             SELECT parking_buy_id, sum(hour_use) as used_hour FROM parking_use
        //             WHERE room_id=$roomId AND domain_id= $domainId AND is_until_out=0 AND deleted_at is null
        //             GROUP BY parking_buy_id
        //             UNION ALL
        //             SELECT t1.parking_buy_id , sum(TIMESTAMPDIFF(HOUR, t1.start_date, CASE WHEN DATE_FORMAT(now(), '%i') > 1 THEN DATE_FORMAT(  DATE_ADD(NOW(), INTERVAL 1 HOUR), '%Y-%m-%d %H:00:00') ELSE DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')  END )) as used_hour
        //             FROM (
        //             SELECT parking_buy_id ,start_date
        //              , CASE WHEN  start_date < now() THEN 1
        //                ELSE 0 END as used_hour
        //             FROM parking_use
        //             WHERE room_id=$roomId AND domain_id= $domainId AND is_until_out=1 AND end_date is null AND deleted_at is null
        //             ) t1
        //             WHERE t1.used_hour > 0
        //             GROUP BY t1.parking_buy_id
        //             UNION ALL
        //             SELECT parking_buy_id,SUM(TIMESTAMPDIFF(HOUR, start_date, end_date)) as used_hour
        //             FROM parking_use
        //             WHERE room_id=$roomId AND domain_id= $domainId AND is_until_out=1  AND end_date is not null AND deleted_at is null
        //             GROUP BY parking_buy_id

        //         ) t2
        //         ON t2.parking_buy_id = t1.id
        //          WHERE (t1.total_hour-IFNULL(t2.used_hour,0)) > 0
        //         ORDER BY t1.id ASC" ;
        
      
        // $data['parking_buy_from_room']  =  DB::select(DB::raw($sql));


        return $this->respondWithItem($data);
    }

    public function checkDebtHour(Request $request, $domainId, $roomId)
    {
        $post = $request->except('api_token');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        //---- หาวันเวลาที่เข้ามาก่อน
        $sql = "SELECT * , DATE(created_at) as date_start, DATE(now()) as date_now
                FROM parking_checkin
                WHERE  id=".$post['parking_checkin_id'] ;
        $query = collect(DB::select(DB::raw($sql)))->first();
        if (empty($query)) {
             return $this->respondWithError($this->langMessage('ไม่พบทะบียนรถที่ระบุ', 'Not found this car'));
        }
        $exceptId = (isset($post['except_id'])) ? $post['except_id'] : 0 ;
        $monthStartDate = date('Y-m', strtotime($query->date_start)) ;
        $startDate = $query->date_start ;
        //---  ถ้าคนละเดือน ต้องเช็ค คูปองของวันก่อนหน้า ว่ามีหรือไม่
        if ($query->date_start != $query->date_now && $monthStartDate != date('Y-m', strtotime($query->date_now))) {
            //--- ดึงคูปองของเดือนก่อนหน้า
            $beforMonthRemainHour = ParkingUse::getRemainHourByDate($roomId, date('Y-m-d', strtotime($query->date_start)), $exceptYourself);

            //--- หาวันสุดท้ายของเดือนก่อนหน้า
            $expire = date("Y-m-t 23:59:59", strtotime($monthStartDate));

            //--- หาวัน start ของเดือนถัดไป เพื่อไปใช้ ลอจิก เดือนปัจจุบัน
            $nextDateMonth = date('Y-m-d 00:00:00', strtotime($expire . "+1 days"));
            $startDate =  $nextDateMonth ;

            $diffhour = ceil((strtotime($expire)-strtotime($query->date_start))/(60*60)) ;
            if ($beforMonthRemainHour<=$diffhour) {
                $debtHour = $diffhour-$beforMonthRemainHour ;
                return $this->respondWithError($this->langMessage($monthStartDate." ชั่วโมงขาดไป $debtHour ชั่วโมง ยืนยันทำรายการ", "Remain coupon not enough $monthStartDate debt $debtHour hour confirm order"));
            }
        }

        return $this->respondWithItem(['parking_use_id'=>$query->id]);
    }

    public function store(Request $request, $domainId, $roomId)
    {
        $post = $request->except('api_token');
        $userId = Auth()->user()->id;
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $post['is_until_out'] = (!isset($post['is_until_out']) || $post['is_until_out']=="false" || $post['is_until_out']=="0" )  ? 0 : 1 ;
        $exceptId = (isset($post['except_id'])) ? $post['except_id'] : 0 ;
        $parkingCheckInId = $post['parking_checkin_id'];
      
        DB::beginTransaction();


        //---- หาวันเวลาที่เข้ามาก่อน
        $sql = "SELECT * , DATE(created_at) as date_start, DATE(now()) as date_now
                FROM parking_checkin
                WHERE  id=$parkingCheckInId "  ;
        $query = collect(DB::select(DB::raw($sql)))->first();
        if (empty($query)) {
             return $this->respondWithError($this->langMessage('ไม่พบทะบียนรถที่ระบุ', 'Not found this car'));
        }



        if ($query->set_used==1&&$exceptId==0) {
            return $this->respondWithError($this->langMessage('เลขทะเบียนนี้ ได้มีการบันทึกใช้ไปก่อนหน้านี้แล้ว หากต้องการบันทึกใหม่ ให้ลบข้อมูลบันทึกใช้เก่าออกก่อน', 'This car used coupon'));
        }

        $licensePlate  = $post['license_plate'];
        $licensePlateCategory  = $post['license_plate_category'];
        $provinceId  = $post['province_id'];
        $hourSetUse = $post['hour_use'] ;
        $isUntilOut = $post['is_until_out'] ;
        $monthStartDate = date('Y-m', strtotime($query->date_start)) ;
        $constStartDate = $query->created_at ;
        $startDate = $constStartDate ;
        
        $startTime = date('H:i:s', strtotime($startDate)) ;
    



        if ($isUntilOut) {
            $hourSetUse = 0 ;
            $post['end_date'] = null ;
            $post['end_date'] = null ;
            $sql = "SELECT pu.id
                    ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                    FROM parking_use pu 
                    LEFT JOIN rooms r ON r.id=pu.room_id
                    WHERE license_plate=".$post['license_plate']."
                    AND license_plate_category='".$post['license_plate_category']."' 
                    AND province_id=".$post['province_id']."
                    AND used_date is null 
                    AND end_date is null
                    AND deleted_at is null
                    AND is_until_out=1";
            $block =  collect(DB::select(DB::raw($sql)))->first();
            if (!empty($block)) {
                return $this->respondWithError($this->langMessage('ไม่สามาถใช้อี-คูปอง จนกว่าจะออก สำหรับ รถทะเบียนคันนี้ได้ เนื่องจากมีการใช้ อี-คูปอง จนกว่าจะออก ของห้อง '.$block->room_name.' อยู่', 'Wrong license plate used by room '.$block->room_name));
            }
        }

        //---  ถ้าคนละเดือน ต้องเช็ค คูปองของวันก่อนหน้า ว่ามีหรือไม่
        // if ($query->date_start != $query->date_now && $monthStartDate != date('Y-m',strtotime($query->date_now))) {
        //     //--- ดึงคูปองของเดือนก่อนหน้า
        //     $beforMonthRemainHour = ParkingUse::getRemainHourByDate($roomId,date('Y-m-d',strtotime($query->date_start)),$exceptId);


    
        //     //--- หาวันสุดท้ายของเดือนก่อนหน้า
        //     $expire = date("Y-m-t 23:59:59", strtotime($startDate));
        //     //--- หาจำนวนชั่วโมง ที่ต้องใช้ จนถึงวันสิ้นสุด
        //     $diffhour = ceil((strtotime($expire)-strtotime($query->date_start))/(60*60)) ;

          

        //     //--- ถ้าจำนวนชั่วโมงที่มีอยู่ น้อยกว่า ชั่วโมงที่ต้องใช้จนสิ้นเดือน
            
        //     if($beforMonthRemainHour<$diffhour){
        //         $debtHour = $diffhour-$beforMonthRemainHour ;
        //         //-- หาค่า ชั่วโมง ที่จะเป็นหนี้


        //         $diffhour = $beforMonthRemainHour ; //--- ระบบจะเก็บข้อมูลเฉพาะ ชั่วโมงคงเหลือ
        //         //--- หาวันหมดอายุ จากจำนวนคูปองที่มี
        //         $debtStartDate = date("Y-m-d H:i:s", strtotime($startDate. "+$beforMonthRemainHour hours"));

        //         // $parkingDebt = new ParkingDebt();
        //         // $parkingDebt->license_plate = $licensePlate ;
        //         // $parkingDebt->license_plate_category = $licensePlateCategory;
        //         // $parkingDebt->debt_hour = $debtHour ;
        //         // $parkingDebt->parking_use_id = 0 ;
        //         // $parkingDebt->room_id = $roomId ;
        //         // $parkingDebt->domain_id = $domainId ;
        //         // $parkingDebt->province_id = $provinceId ;
        //         // $parkingDebt->debt_type = 0 ;
        //         // $parkingDebt->start_date = Carbon::parse($debtStartDate) ;
        //         // $parkingDebt->end_date = Carbon::parse($expire) ;
        //         // $parkingDebt->created_at = Carbon::now();
        //         // $parkingDebt->created_by = $userId;
        //         // $parkingDebt->parking_checkin_id = $parkingCheckInId ;
        //         // $parkingDebt->save();

        //     }

        //     //--- หาวัน start ของเดือนถัดไป เพื่อไปใช้ ลอจิก เดือนปัจจุบัน
        //     $nextDateMonth = date('Y-m-d 00:00:00',strtotime($expire . "+1 days"));
        //     $startDate =  $nextDateMonth ;
        //     // echo "expire : $expire<BR>";
        //     // echo "str end : ".strtotime($expire)."<BR>";
        //     // echo "str start : ".strtotime($query->date_start)."<BR>";
        //     // echo "diffhour: $diffhour<BR>";
        //     // echo "couponfound : $beforMonthRemainHour<BR>";
        //     // echo "next startDate : $startDate<BR>";
        
        //     //--- บันทึกคูปองที่ใช้ได้ ของเดือนก่อนหน้า



        //     $query = new ParkingUse();
        //     $query->room_id = $roomId;
        //     $query->domain_id = $domainId;
        //     $query->start_date = Carbon::Parse($query->created_at) ;
        //     $query->end_date = Carbon::Parse($expire) ;
        //     $query->license_plate =  intval($licensePlate) ;
        //     $query->license_plate_category =  $licensePlateCategory ;
        //     $query->created_at = Carbon::now();
        //     $query->created_by = $userId ;
        //     $query->parking_checkin_id = $parkingCheckInId ;
        //     $query->province_id = $provinceId ;
        //     $query->parking_buy_id = 0 ;
        //     $query->hour_use = $diffhour ;
        //     $query->is_until_out = 0  ;
        //     $query->save();
        // }


        //--- เช็คว่ามี คูปองสำหรับเดือนปัจจุบันเพียงพอหรือไม่
        $currentMonthRemainHour = ParkingUse::getRemainHourByDate($roomId, date('Y-m-d', strtotime($startDate)), $exceptId);
        if ($currentMonthRemainHour<$hourSetUse) {
            DB::rollBack();
            return $this->respondWithError($this->langMessage('คูปองคงเหลือไม่เพียงพอ', 'Remain coupon not enough'));
        }
        //--- กำหนดชั่วโมงที่จะมีสิทธิ์จอดตามที่ระบุ
        // $startDate = date("Y-m-d", strtotime($startDate))." ".$startTime;
        $expire = date("Y-m-d H:i:s", strtotime($startDate. "+$hourSetUse hours"));




        // $totalHourUse = ceil((strtotime($expire)-strtotime($constStartDate))/(60*60)) ;
        // echo "start date : $constStartDate<BR>";
        // echo "start date current: $startDate<BR>";
        // echo "expire date : $expire<BR>";
        // echo "totalHourUse : $totalHourUse<BR>";
        // die;

        // echo "currentMonthRemainHour : $currentMonthRemainHour<BR>" ;
        // var_dump($query);die;
        if ($exceptId!=0) {
            $query = ParkingUse::where('id', $exceptId)->first();
            if (!empty($query)) {
                if (isset($query->used_date)) {
                    DB::rollBack();
                    return $this->respondWithError($this->langMessage('คูปองมีการใช้งานแล้ว ไม่สามารถทำการแก้ไขได้', 'Coupon Used cannot edit it'));
                }
            } else {
                $query = new ParkingUse();
            }
        } else {
            $query = new ParkingUse();
        }

        $query->room_id = $roomId;
        $query->domain_id = $domainId;
        $query->start_date = Carbon::Parse($startDate) ;
        $query->end_date = Carbon::Parse($expire) ;
        $query->license_plate = intval($licensePlate) ;
        $query->license_plate_category = $licensePlateCategory ;
        $query->created_at = Carbon::now();
        $query->created_by = $userId ;
        $query->province_id = $provinceId ;
        $query->parking_checkin_id = $parkingCheckInId ;
        $query->parking_buy_id = 0 ;
        $query->hour_use =$hourSetUse ;
        $query->is_until_out = $post['is_until_out']  ;
        $query->save();

        $checkIn = ParkingCheckIn::find($parkingCheckInId)->update(
            [  'set_used'=>1
            ,'set_used_by'=>Auth()->User()->id
            ,'set_used_at'=>Carbon::now()
            ,'set_used_hour'=>$hourSetUse
            ,'room_id'=>$roomId
            ,'is_until_out'=>$isUntilOut
            ,'coupon_time_limit'=>Carbon::Parse($expire)
            // ,'outed_at'=>$outedAt
            //,'hour_use'=>$totalHourUse
            ]
        );
       
        DB::commit();



        // $parkingCheckIn = ParkingCheckIn::find($post['parking_checkin_id']);
        // if(empty($parkingCheckIn)){
        //      return $this->respondWithError($this->langMessage('ไม่พบทะบียนรถที่ระบุ','Not found this car'));
        // }

        // if( strtotime($parkingCheckIn->created_at))






       
        // if(date('i',strtotime($post['end_date'])) > 0 ){
        //     $post['end_date'] = date("Y-m-d H", strtotime($post['end_date'].'+1 hours')).":00";
        // }

        // $diffInHour = $post['hour_use'] ;

      

        // if( $isUntilOut){
        //     $post['end_date'] = null ;

         
       
        //     $sql = "SELECT pu.id
        //             ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
        //             FROM parking_use pu
        //             LEFT JOIN rooms r ON r.id=pu.room_id
        //             WHERE license_plate=".$post['license_plate']."
        //             AND license_plate_category='".$post['license_plate_category']."'
        //             AND province_id=".$post['province_id']."
        //             AND used_date is null
        //             AND end_date is null";
        //     $block =  collect(DB::select(DB::raw($sql)))->first();

        //     if(!empty($block)){
        //         return $this->respondWithError($this->langMessage('ไม่สามาถใช้อี-คูปอง จนกว่าจะออก สำหรับ รถทะเบียนคันนี้ได้ เนื่องจากมีการใช้ อี-คูปอง จนกว่าจะออก ของห้อง '.$block->room_name.' อยู่','Wrong license plate used by room '.$block->room_name));
        //     }
        // }
       
        // // $diff = strtotime($post['end_date'])-strtotime($post['start_date']);
        // // $diffInHour = ceil($diff/3600) ;
        // if($diffInHour<=0){
        //     return $this->respondWithError($this->langMessage('ระบุเวลาให้ถูกต้อง','Wrong date time'));
        // }

        // // $remainHour = ParkingUse::getRemainHour($roomId,date('Y-m',strtotime($post['start_date'])).'-01');
        // $remainHour = ParkingUse::getPackageRemainHour($roomId,$post['package_id']);
        // if($remainHour < $diffInHour){
        //     return $this->respondWithError($this->langMessage('ชั่วโมงคงเหลือไม่เพียงพอ','Remain hour not enough'));
        // }

        // $parkingCheckIn = ParkingCheckIn::find($post['parking_checkin_id']);
        // if(empty($parkingCheckIn)){
        //      return $this->respondWithError($this->langMessage('ไม่พบทะบียนรถที่ระบุ','Not found this car'));
        // }

        // $parkingCheckIn->update(['set_used'=>1]);

        // $post['end_date'] = date("Y-m-d H:i", strtotime($parkingCheckIn->created_at.'+'.$diffInHour.' hours'));

        // $query = new ParkingUse();
        // $query->room_id = $roomId;
        // $query->domain_id = $domainId;
        // $query->start_date = $parkingCheckIn->created_at ;
        // $query->end_date = isset($post['end_date']) ?  Carbon::Parse($post['end_date']) : null ;
        // $query->license_plate =  intval($post['license_plate']) ;
        // $query->license_plate_category =  $post['license_plate_category'] ;
        // $query->created_at = Carbon::now();
        // $query->created_by = Auth()->user()->id ;
        // $query->province_id = $post['province_id'] ;
        // $query->parking_buy_id = $post['package_id'] ;
        // $query->hour_use = $diffInHour ;
        // $query->is_until_out = $post['is_until_out'] ;
    

        // $query->save();

        // // $packageUseId =  $query->id ;

        // // $sql = "SELECT t1.id,t1.total_hour,IFNULL(t2.used_hour,0) as used_hour , (t1.total_hour-IFNULL(t2.used_hour,0)) as remain_hour  FROM (
        // //         SELECT pb.id,SUM(pp.hour) as total_hour
        // //         FROM parking_buy pb
        // //         JOIN parking_package pp
        // //         ON pb.package_id = pp.id
        // //         WHERE pb.room_id=$roomId AND pb.domain_id= $domainId
        // //         AND DATE(pb.period_at) = '".date('Y-m',strtotime($post['start_date'])).'-01'."'
        // //         GROUP BY pb.id ) t1
        // //         LEFT JOIN (
        // //              SELECT parking_buy_id, sum(hour_use) as used_hour FROM parking_use_history
        // //         WHERE room_id=$roomId  AND domain_id= $domainId
        // //         GROUP BY parking_buy_id
        // //         ) t2
        // //         ON t2.parking_buy_id = t1.id
        // //          WHERE (t1.total_hour-IFNULL(t2.used_hour,0)) > 0
        // //         ORDER BY t1.id DESC";
   
        // // $check =  DB::select(DB::raw($sql));
        // // if(empty($check)){
        // //      return $this->respondWithError($this->langMessage('ชั่วโมงคงเหลือไม่เพียงพอ','Remain hour not enough'));
        // // }

        // // $setHour = $diffInHour ;

        // // foreach ($check as $key => $c) {
        // //     if($c->remain_hour >=  $setHour ){
        // //         $history[$key]['hour_use'] =  $setHour;
        // //         $history[$key]['parking_buy_id'] =  $c->id;
        // //         $setHour -= $diffInHour ;
        // //     }elseif($c->remain_hour <  $setHour ){
        // //         $history[$key]['hour_use'] =  $c->remain_hour ;
        // //         $history[$key]['parking_buy_id'] =  $c->id;
        // //         $setHour -= $c->remain_hour ;
        // //     }

        // //     $history[$key]['parking_use_id'] =  $packageUseId;
        // //     $history[$key]['created_at'] =  Carbon::now();
        // //     $history[$key]['created_by'] =  Auth()->User()->id;
        // //     $history[$key]['room_id'] =  $roomId;
        // //     $history[$key]['domain_id'] =  $domainId;


        // //     if($setHour==0){
        // //         break;
        // //     }

        // // }

        // // ParkingUseHistory::insert($history);

     



        return $this->respondWithItem(['parking_use_id'=>$query->id]);
    }
    public function update(Request $request, $domainId, $Id)
    {
        // $post = $request->all();
        // $query = ParkingUse::find($Id) ;
        // if(empty($query)){
        //     $query = new ParkingUse();
        // }
        // $query->fill($post)->save();
        // return $this->respondWithItem(['parking_use_id'=>$Id]);
    }
    public function destroy(Request $request, $domainId, $roomId, $id)
    {
        // ParkingUseHistory::where('parking_use_id',$id)->delete();
        // ParkingUse::where('id',$id)->update(['deleted_by'=>Auth()->user()->id]);
        $query = ParkingUse::where('id', $id)->first();
        $parkingCheckInId =  $query->parking_checkin_id ;
        if (isset($query->used_date)) {
            return $this->respondWithError($this->langMessage('มีการใช้งานแล้ว ไม่สามารถยกเลิกการใช้ได้', 'Cannot delete data used'));
        }

        $query->deleted_at = Carbon::now();
        $query->deleted_by = Auth()->user()->id;
        $query->save();

        ParkingCheckIn::find($parkingCheckInId)->update([
            'set_used'=>0
            ,'set_used_hour'=>0
            ,'set_used_by'=>null
        ]);

        return $this->respondWithItem(['parking_use_id'=>$id]);
    }
    
    public function getPackage(Request $request, $domainId, $roomId)
    {
        $post = $request->all();
        $query = ParkingBuy::from('parking_buy as pb')
                ->join('parking_package as pp', 'pp.id', '=', 'pb.package_id')
                ->where('pb.domain_id', $domainId)
                ->where('pb.room_id', $roomId)
                // ->where('pb.expired_at','>',Carbon::now())
                ->select(DB::raw("pb.id,pb.room_id,pb.package_id,pp.name as package_name,pp.price as package_price,pb.created_at,pb.expired_at"))
                ->get();
        $data['parking_buy_user'] = $query;
        return $this->respondWithItem($data);
    }
    public function getRemainHour(Request $request, $domainId, $roomId)
    {
        $post = $request->except('api_token', '_method');
        $data['remain_hour'] = ParkingUse::getRemainHour($roomId, $post['month_year'].'-01');
        return $this->respondWithItem($data);
    }

    public function getCheckIn(Request $request, $domainId, $roomId)
    {
        $post = $request->except('api_token');
        DB::enableQueryLog();
        $data['parking_checkins'] = ParkingCheckIn::from('parking_checkin as pc')
        ->leftJoin('provinces as p', 'pc.province_id', '=', 'p.PROVINCE_ID')
        ->leftJoin('users as u', 'u.id', '=', 'pc.created_by')
        ->leftJoin('parking_use as pu', function ($join) {
            $join->on('pu.parking_checkin_id', '=', 'pc.id')
            ->whereNull('pu.deleted_at');
        })

        ->select(DB::raw("pc.*,TRIM(p.PROVINCE_NAME) as province_name,u.first_name,u.last_name,IFNULL(pu.id,0) as parking_use_id"))
        ->where('pc.room_id', $roomId)
        ->whereNull('pc.used_at')
        ->get();
        return $this->respondWithItem($data);
    }

    public function searchCheckIn(Request $request, $domainId)
    {
        $post = $request->except('api_token');
        if (isset($post['license_plate']) && ($post['license_plate']!="" ||$post['license_plate']!=" ")) {
            $data['parking_checkins'] = ParkingCheckIn::from('parking_checkin as pc')
              ->leftJoin('provinces as p', 'pc.province_id', '=', 'p.PROVINCE_ID')
              ->select(DB::raw("pc.*,TRIM(p.PROVINCE_NAME) as province_name"))
              ->where('pc.license_plate', $post['license_plate'])
              ->where('pc.license_plate_category', $post['license_plate_category'])
              // ->where('pc.province_id',$post['province_id'])
              ->where('pc.domain_id', $domainId)
              ->whereNull('pc.used_at')
              ->get();
        } else {
            //--- เอาออก เพราะ เคสไม่ระบุห้อง แล้ว ตั้งใช้แล้วโดยห้อง 1 แล้วห้อง 2 มา ตั้งใช้อีก ทำให้ระบบเพี้ยน
            // $data['parking_checkins'] = ParkingCheckIn::from('parking_checkin as pc')
            // ->leftJoin('provinces as p', 'pc.province_id', '=', 'p.PROVINCE_ID')
            // ->select(DB::raw( "pc.*,TRIM(p.PROVINCE_NAME) as province_name"))
            // ->where('pc.domain_id',$domainId)
            // ->where('pc.set_used',0)
            // ->get();


            $data['parking_checkins'] = [];
        }

       
        return $this->respondWithItem($data);
    }

    

    private function validator($data)
    {
        return Validator::make($data, [
            'license_plate' => 'required|max:4',
            'license_plate_category' => 'required|max:3',
            'province_id' => 'required',
            // 'package_id' => 'required',
            // 'start_date' => 'required',
            'hour_use' => 'required',
        ]);
    }
}
