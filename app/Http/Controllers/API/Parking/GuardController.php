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

class GuardController extends ApiController
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
         $sql = "SELECT pc.* 
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                ,pv.PROVINCE_NAME as province_name
                FROM parking_checkin pc
                LEFT JOIN rooms as r 
                ON r.id=pc.room_id
                JOIN provinces as pv 
                ON pv.PROVINCE_ID = pc.province_id 
                WHERE DATE_FORMAT(now() ,'%Y-%m') >= DATE_FORMAT(pc.created_at,'%Y-%m')
                AND pc.domain_id = $domainId
                AND pc.used_at is null";

        $data['parking_guard_search']  = DB::select(DB::raw($sql));
       
        return $this->respondWithItem($data);
    }
  
    public function checkHour(Request $request, $domainId)
    {
        $post = $request->except('api_token');
        
        $id = $post['id'] ;
        if (!isset($id)) {
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูลที่เลือก', 'Not Found Data'));
        }

        $sql = "SELECT * , DATE(created_at) as date_start, DATE(now()) as date_now
                FROM parking_checkin
                WHERE  id= $id "  ;
        $query = collect(DB::select(DB::raw($sql)))->first();
        if (empty($query)) {
             return $this->respondWithError($this->langMessage('ไม่พบทะบียนรถที่ระบุ', 'Not found this car'));
        }

        $OwnerRoomSetUse = (isset($query->set_used)) ? $query->set_used : 0 ;

        // if(isset($post['license_plate'])){
        //     $b4License = "";
        //     foreach ($post['license_plate'] as $key => $p) {
        //         if (!empty($b4License) && $b4License!= $p){
        //             return $this->respondWithError($this->langMessage('กรุณาเลือกทะเบียนรถให้เหมือนกัน','Wrong License Plate'));
        //         }
        //         $b4License = $p ;
        //     }
        // }

        $freeParkingMin = Setting::getVal($domainId, 'PARKING_FREE') ;
        $now = isset($post['check_out_date']) ? strtotime($post['check_out_date']) : Setting::getServerTime();
        $now += 0 ;
        $data['total_has_hour'] = 0;
        $data['total_use_real_hour'] = 0;
        $data['total_debt_hour'] = 0;

        $data['has_hour'] = 0;
        $data['use_real_hour'] = 0;
        $data['debt_hour'] = 0;
        $data['max_end_date'] = 0;
        $data['now_date'] = $now+0;

        $data['previus_month_has_hour'] = 0;
        $data['previus_month_use_real_hour'] = 0;
        $data['previus_month_debt_hour'] = 0;
        $data['previus_month_max_end_date'] = 0;

        //--- ถ้าจอดรถไม่เกินจำนวนที่ระบุไว้ ก็จอดฟรี
        $timestampStartDate = strtotime($query->created_at);
        if ($freeParkingMin>0) {
            if (($now-$timestampStartDate)<=($freeParkingMin*60)) {
                $data['has_hour'] = $freeParkingMin/60;
                $data['use_real_hour'] = ($now-$timestampStartDate)/60/60;
                $data['debt_hour'] = 0;
                $data['max_end_date'] = $timestampStartDate+($freeParkingMin*60);
                return $this->respondWithItem($data);
            }
        }



        $hour = date('H') ;
        if (date('i')>0) {
            $hour += 1 ;
        }
       
        $debtHour = 0 ;
        $lastestId = 0;

        $licensePlateTemp = "";

        
       
        $licensePlate  = $query->license_plate;
        $licensePlateCategory  = $query->license_plate_category;
        $provinceId  = $query->province_id;
        $hourSetUse = $query->set_used_hour ;
      
        $monthStartDate = date('Y-m', strtotime($query->date_start)) ;
        $constStartDate = $query->created_at ;
        $startDate = $constStartDate ;

        $startTime = date('H:i:s', strtotime($startDate)) ;
        $roomId = $query->room_id ;
       
        $beforMonthRemainHour = 0 ;
        $beforMonthUseRealhour = 0;
        $previusMonthDebtHour = 0;
        // echo " date_start ".$query->date_start."<BR>";
        // echo " date_now ".$query->date_now."<BR>";
        // echo " monthStartDate ".$monthStartDate."<BR>";
        // echo " monthStartDate ".date('Y-m',strtotime($query->date_now))."<BR>";

        // die;

      
        //---  ถ้าคนละเดือน ต้องเช็ค คูปองของวันก่อนหน้า ว่ามีหรือไม่
        if ($query->date_start != $query->date_now && $monthStartDate != date('Y-m', strtotime($query->date_now))) {
            $beforMonthRemainHour = 0;
            if ($OwnerRoomSetUse==1) {
                //--- ดึงคูปองของเดือนก่อนหน้า
                $beforMonthRemainHour = ParkingUse::getRemainHourByDate($roomId, date('Y-m-d', strtotime($query->date_start)));
            }
            $beforMonthRemainHour +=0;

    
            //--- หาวันสุดท้ายของเดือนก่อนหน้า
            $expire = date("Y-m-t 23:59:59", strtotime($startDate));
            //--- หาจำนวนชั่วโมง ที่ต้องใช้ จนถึงวันสิ้นสุด
            $beforMonthUseRealhour = ceil((strtotime($expire)-strtotime($query->date_start))/(60*60)) ;

           
          

            //--- ถ้าจำนวนชั่วโมงที่มีอยู่ น้อยกว่า ชั่วโมงที่ต้องใช้จนสิ้นเดือน
            
            if ($beforMonthRemainHour<$beforMonthUseRealhour) {
                $previusMonthDebtHour = $beforMonthUseRealhour-$beforMonthRemainHour ;
                $debtHour += $previusMonthDebtHour ;
                //-- หาค่า ชั่วโมง ที่จะเป็นหนี้
                $diffhour = $beforMonthRemainHour ; //--- ระบบจะเก็บข้อมูลเฉพาะ ชั่วโมงคงเหลือ
                //--- หาวันหมดอายุ จากจำนวนคูปองที่มี
                $debtEndDate = date("Y-m-d H:i:s", strtotime($startDate. "+$beforMonthRemainHour hours"));

                // $parkingDebt = new ParkingDebt();
                // $parkingDebt->license_plate = $licensePlate ;
                // $parkingDebt->license_plate_category = $licensePlateCategory;
                // $parkingDebt->debt_hour = $debtHour ;
                // $parkingDebt->parking_use_id = 0 ;
                // $parkingDebt->room_id = $roomId ;
                // $parkingDebt->domain_id = $domainId ;
                // $parkingDebt->province_id = $provinceId ;
                // $parkingDebt->debt_type = 0 ;
                // $parkingDebt->start_date = Carbon::parse($debtStartDate) ;
                // $parkingDebt->end_date = Carbon::parse($expire) ;
                // $parkingDebt->created_at = Carbon::now();
                // $parkingDebt->created_by = $userId;
                // $parkingDebt->parking_checkin_id = $parkingCheckInId ;
                // $parkingDebt->save();
            }

            //--- หาวัน start ของเดือนถัดไป เพื่อไปใช้ ลอจิก เดือนปัจจุบัน
            $nextDateMonth = date('Y-m-d 00:00:00', strtotime($expire . "+1 days"));
            $startDate =  $nextDateMonth ;
            // echo "expire : $expire<BR>";
            // echo "str end : ".strtotime($expire)."<BR>";
            // echo "str start : ".strtotime($query->date_start)."<BR>";
            // echo "diffhour: $diffhour<BR>";
            // echo "couponfound : $beforMonthRemainHour<BR>";
            // echo "next startDate : $startDate<BR>";
        
            //--- บันทึกคูปองที่ใช้ได้ ของเดือนก่อนหน้า

            $data['previus_month_has_hour'] =  $beforMonthRemainHour;
            $data['previus_month_use_real_hour'] = $beforMonthUseRealhour;
            $data['previus_month_debt_hour'] = $previusMonthDebtHour;
            $data['previus_month_max_end_date'] = $debtEndDate;
        }

      
        $currentMonthRemainHour = 0;
        if ($OwnerRoomSetUse==1) {
             //--- เช็คว่ามี คูปองสำหรับเดือนปัจจุบันเพียงพอหรือไม่
            $currentMonthRemainHour = ParkingUse::getRemainHourByDate($roomId, date('Y-m-d', strtotime($startDate)));
        }
      
      //echo "currentMonthRemainHour : $currentMonthRemainHour<BR>";
      //echo "hourSetUse : $hourSetUse<BR>";



        //--- ถ้าชั่วโมงใช้ที่กำหนดมาเป็น 0 คือ จนกว่าจะออก
        if ($hourSetUse==0) {
            $hourSetUse = ceil(($now-strtotime($startDate))/(60*60)) ;
        }

        
        //--- คูปองคงเหลือเดือนปัจจุบนั น้อยกว่า คูปองที่กำหนดมา   ให้ใช้ จำนวนคูปองจากที่เหลือ
        if ($currentMonthRemainHour<$hourSetUse) {
            $hourSetUse = $currentMonthRemainHour ;
        }

        //--- กำหนดชั่วโมงที่จะมีสิทธิ์จอดตามที่ระบุ
        $startDate = date("Y-m-d", strtotime($startDate))." ".$startTime;
        $expire = date("Y-m-d H:i:s", strtotime($startDate. "+$hourSetUse hours"));

      

        $currentMonthUseRealHour = ceil(($now-strtotime($startDate))/(60*60)) ;
        $currentMonthHasHour = ceil((strtotime($expire)-strtotime($startDate))/(60*60)) ;

        $currentMonthDebtHour = $currentMonthUseRealHour-$currentMonthHasHour;


        // echo "now : ".date('Y-m-d H:i:s',$now)."<BR>" ;
        // echo "start : ".$startDate."<BR>" ;
        // echo "currentMonthUseRealHour : ".$currentMonthUseRealHour."<BR>" ;

        // echo "expire : $expire<BR>";
        // echo "start : $startDate<BR>";
        // echo "currentMonthHasHour : ".$currentMonthHasHour."<BR>";

        // echo "currentMonthDebtHour : ".($currentMonthUseRealHour-$currentMonthHasHour);die;


        $data['total_has_hour'] =  $currentMonthHasHour+$beforMonthRemainHour;
        $data['total_use_real_hour'] = $currentMonthUseRealHour+$beforMonthUseRealhour;
        $data['total_debt_hour'] = $currentMonthDebtHour+$previusMonthDebtHour;
        
        $data['has_hour'] =  $currentMonthHasHour;
        $data['use_real_hour'] = $currentMonthUseRealHour;
        $data['debt_hour'] = $currentMonthDebtHour;
        $data['max_end_date'] = strtotime($expire) ;
       
        // echo "startDate : ".$startDate."<BR>" ;
        // echo "now : ".date("Y-m-d H:i:s",$now)."<BR>" ;
        // echo "expire : ".$expire."<BR>" ;

        // echo "currentMonthUseRealHour : $currentMonthUseRealHour<BR>";
        // echo "currentMonthHasHour : $currentMonthHasHour<BR>";
        // echo "currentMonthDebtHour : $currentMonthDebtHour<BR>";

        // echo "beforMonthUseRealhour : $beforMonthUseRealhour<BR>";
        // echo "beforMonthRemainHour : $beforMonthRemainHour<BR>";
        // echo "previusMonthDebtHour : $previusMonthDebtHour<BR>";


      

        // echo "total_use_real_hour ". $data['total_use_real_hour']."<BR>";
        // echo "total_has_hour ".$data['total_has_hour']."<BR>";
        // echo "total_debt_hour ".$data['total_debt_hour']."<BR>";

        // // die;

        // echo "start date : $constStartDate<BR>";
        // echo "start date current: $startDate<BR>";
        // echo "expire date : $expire<BR>";
        // echo "totalHourUse : $totalHourUse<BR>";
        // die;

        // echo "currentMonthRemainHour : $currentMonthRemainHour<BR>" ;
        // var_dump($query);die;

        $outedAt = Carbon::Parse($expire) ;




  //       if(isset($post['id'])){
  //           $hasHour = 0 ;
  //           $remainHour = 0 ;
  // // data.date
  //           $minStartDate = 0 ;
  //           $maxEndDate = 0 ;
  //           foreach ($post['id'] as $key => $id) {
  //               $parking = ParkingCheckIn::find($id);
  //               if(empty($parking)){
  //                   return $this->respondWithError($this->langMessage('ไม่พบข้อมูลที่เลือก','Not Found Data'));
  //               }
  //               $licensePlate = $parking->license_plate_category." ".$parking->license_plate." ".$parking->province_id;
  //               if(empty($licensePlateTemp)){
  //                   $licensePlateTemp=$licensePlate;
  //               }
  //               if($licensePlateTemp!=$licensePlate){
  //                   return $this->respondWithError($this->langMessage('กรุณาเลือกทะเบียนรถให้เหมือนกัน','Wrong License Plate'));
  //               }



  //               $startDate = strtotime($parking->start_date);
  //               $now = Setting::getServerTime();
  //               // echo "start : $startDate<BR>";
  //               // echo "now : $now<BR>";
  //               // echo "now : ".($now-$startDate)."<BR>";
  //               // var_dump(($now-$startDate)<=($freeParkingMin*60));
  //               // var_dump($freeParkingMin);die;

  //               if($freeParkingMin>0){
  //                   if(($now-$startDate)<=($freeParkingMin*60)){
  //                       $data['has_hour'] = $freeParkingMin/60;
  //                       $data['use_real_hour'] = ($now-$startDate)/60;
  //                       $data['debt_hour'] = 0;
  //                       $data['max_end_date'] = $startDate+($freeParkingMin*60);
  //                       $data['now_date'] = $now;
  //                       return $this->respondWithItem($data);
  //                   }
  //               }




  //               $unixStartDate = strtotime($parking->start_date) ;
  //               if($minStartDate==0||$minStartDate>$unixStartDate){
  //                   $minStartDate = $unixStartDate ;

  //               }

  //               if($minStartDate==0||$minStartDate<$unixStartDate){
  //                   $lastestId =  $parking->id ;
  //               }


            

  //               $unixEndDate = $now;
  //               if($parking->is_until_out==0){
  //                   $unixEndDate = strtotime($parking->end_date) ;
  //                   $diff = $unixEndDate -  $unixStartDate ;
  //                   $diffInHour = ceil($diff/3600) ;
  //                   $hasHour += $diffInHour;
  //                   if($maxEndDate==0||$maxEndDate<$unixEndDate){
  //                       $maxEndDate = $unixEndDate ;

  //                   }

  //               }else{
  //                   $remainHour = ParkingUse::getPackageRemainHour($parking->room_id,$parking->parking_buy_id);
  //                   $diff = $unixEndDate - $unixStartDate ;
  //                   $diffInHour = ceil($diff/3600) ;
  //                   $hasHour += ($remainHour<$diffInHour) ? $remainHour : $diffInHour;

  //                   //--- คูปองจนกว่าจะออก จะไม่มี max enddate มา จึงต้องเอา เวลาที่เหลือ มารวมกับ วันที่เริ่มใช้
  //                   $maxEndDate = $minStartDate+($hasHour*3600);

  //               }
  //           }
  //           $useRealHour = ceil(($now-$minStartDate)/3600);

        

  //           if($useRealHour>$hasHour){
  //               $debtHour = $useRealHour-$hasHour;
  //           }

  //       }
      
        // $data['has_hour'] = $hasHour;
        // $data['use_real_hour'] = $useRealHour;
        // $data['debt_hour'] = $debtHour;
        // $data['max_end_date'] = $maxEndDate;
        // $data['now_date'] = $now;
        return $this->respondWithItem($data);
    }


    public function store(Request $request, $domainId)
    {
        $post = $request->except('api_token');
          
      
        $userId = Auth()->user()->id;
        $id = $post['id'] ;
        if (!isset($id)) {
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูลที่เลือก', 'Not Found Data'));
        }

        $manualOut = isset($post['manual_out']) ? $post['manual_out'] : 0;



        DB::beginTransaction();

        $sql = "SELECT * , DATE(created_at) as date_start, DATE(now()) as date_now
                FROM parking_checkin
                WHERE  id= $id "  ;
        $query = collect(DB::select(DB::raw($sql)))->first();
        if (empty($query)) {
             DB::rollBack();
             return $this->respondWithError($this->langMessage('ไม่พบทะบียนรถที่ระบุ', 'Not found this car'));
        }

        $OwnerRoomSetUse = (isset($query->set_used)) ? $query->set_used : 0 ;

        // if(isset($post['license_plate'])){
        //     $b4License = "";
        //     foreach ($post['license_plate'] as $key => $p) {
        //         if (!empty($b4License) && $b4License!= $p){
        //             return $this->respondWithError($this->langMessage('กรุณาเลือกทะเบียนรถให้เหมือนกัน','Wrong License Plate'));
        //         }
        //         $b4License = $p ;
        //     }
        // }

        $freeParkingMin = Setting::getVal($domainId, 'PARKING_FREE') ;
        $now = isset($post['check_out_date']) ? strtotime($post['check_out_date']) : Setting::getServerTime();
        $carbonNow = Carbon::now();
        $carbonOut = Carbon::parse(date("Y-m-d H:i:s", $now));

        $data['total_has_hour'] = 0;
        $data['total_use_real_hour'] = 0;
        $data['total_debt_hour'] = 0;

        $data['has_hour'] = 0;
        $data['use_real_hour'] = 0;
        $data['debt_hour'] = 0;
        $data['max_end_date'] = 0;
        $data['now_date'] = date("Y-m-d H:i:s", $now);

        $data['previus_month_has_hour'] = 0;
        $data['previus_month_use_real_hour'] = 0;
        $data['previus_month_debt_hour'] = 0;
        $data['previus_month_max_end_date'] = 0;

        //--- ถ้าจอดรถไม่เกินจำนวนที่ระบุไว้ ก็จอดฟรี
        $timestampStartDate = strtotime($query->created_at);
        if ($freeParkingMin>0) {
            if (($now-$timestampStartDate)<=($freeParkingMin*60)) {
                ParkingUse::where('parking_checkin_id', '=', $id)->update(
                    [
                     'used_date'=>$carbonNow
                    ,'end_date'=>$carbonOut
                    ]
                );
                $checkIn = ParkingCheckIn::find($id)->update(
                    ['free_park'=>1
                    ,'manual_out'=>$manualOut
                    ,'hour_use'=>($freeParkingMin/60)
                    ,'outed_at'=>$carbonOut
                    ,'used_at'=>$carbonNow
                    ]
                );
                DB::commit();
                return $this->respondWithItem(['text'=>'success']);
            }
        }



       

        $licensePlate  = $query->license_plate;
        $licensePlateCategory  = $query->license_plate_category;
        $provinceId  = $query->province_id;
        $hourSetUse = $query->set_used_hour ;
    
        $monthStartDate = date('Y-m', strtotime($query->date_start)) ;
        $constStartDate = $query->created_at ;
        $startDate = $constStartDate ;

        $startTime = date('H:i:s', strtotime($startDate)) ;
        $roomId = $query->room_id ;
       
        $beforMonthRemainHour = 0 ;
        $beforMonthUseRealhour = 0;
        $previusMonthDebtHour = 0;

       
        $hasPreviousMonth = false;
        // echo " date_start ".$query->date_start."<BR>";
        // echo " date_now ".$query->date_now."<BR>";
        // echo " monthStartDate ".$monthStartDate."<BR>";
        // echo " monthStartDate ".date('Y-m',strtotime($query->date_now))."<BR>";

        // die;
        //---  ถ้าคนละเดือน ต้องเช็ค คูปองของวันก่อนหน้า ว่ามีหรือไม่
        if ($query->date_start != $query->date_now && $monthStartDate != date('Y-m', strtotime($query->date_now))) {
            $hasPreviousMonth = true;

            $beforMonthRemainHour = 0;
            if ($OwnerRoomSetUse==1) {
                //--- ดึงคูปองของเดือนก่อนหน้า
                $beforMonthRemainHour = ParkingUse::getRemainHourByDate($roomId, date('Y-m-d', strtotime($query->date_start)));
            }
    
            //--- หาวันสุดท้ายของเดือนก่อนหน้า
            $endOfMonth = date("Y-m-t 23:59:59", strtotime($startDate));
            //--- หาจำนวนชั่วโมง ที่ต้องใช้ จนถึงวันสิ้นสุด
            $beforMonthUseRealhour = ceil((strtotime($endOfMonth)-strtotime($query->date_start))/(60*60)) ;

            $expire =  $endOfMonth;

            //--- ถ้าจำนวนชั่วโมงที่มีอยู่ น้อยกว่า ชั่วโมงที่ต้องใช้จนสิ้นเดือน
            
            if ($beforMonthRemainHour<$beforMonthUseRealhour) {
                $previusMonthDebtHour = $beforMonthUseRealhour-$beforMonthRemainHour ;
              
                //--- หาวันหมดอายุ จากจำนวนคูปองที่มี
                $debtEndDate = date("Y-m-d H:i:s", strtotime($startDate. "+$beforMonthRemainHour hours"));

                $parkingDebt = new ParkingDebt();
                $parkingDebt->license_plate = $licensePlate ;
                $parkingDebt->license_plate_category = $licensePlateCategory;
                $parkingDebt->debt_hour = $previusMonthDebtHour ;
                $parkingDebt->parking_use_id = 0 ;
                $parkingDebt->room_id = $roomId ;
                $parkingDebt->domain_id = $domainId ;
                $parkingDebt->province_id = $provinceId ;
                $parkingDebt->debt_type = 0 ;
                $parkingDebt->start_date = Carbon::parse($debtEndDate) ;
                $parkingDebt->end_date = Carbon::parse($endOfMonth) ;
                $parkingDebt->created_at = $carbonNow;
                $parkingDebt->created_by = $userId;
                $parkingDebt->parking_checkin_id = $id ;
                $parkingDebt->save();

                $expire = $debtEndDate ;
                $beforMonthUseRealhour = $beforMonthRemainHour; //--  เก็บข้อมูลใช้เท่าที่มีเหลือ
            }

            $query = new ParkingUse();
            $query->room_id = $roomId;
            $query->domain_id = $domainId;
            $query->start_date = Carbon::Parse($constStartDate) ;
            $query->end_date = Carbon::Parse($expire) ;
            $query->license_plate = intval($licensePlate) ;
            $query->license_plate_category = $licensePlateCategory ;
            $query->created_at = $carbonNow;
            $query->created_by = $userId ;
            $query->province_id = $provinceId ;
            $query->parking_checkin_id = $id ;
            $query->parking_buy_id = 0 ;
            $query->hour_use =  $beforMonthUseRealhour ;
            $query->used_date =  $carbonNow ;
            $query->is_until_out = 1  ;
            $query->save();

            $previousMonthUseId = $query->id ;

            //--- หาวัน start ของเดือนถัดไป เพื่อไปใช้ ลอจิก เดือนปัจจุบัน
            $nextDateMonth = date('Y-m-d 00:00:00', strtotime($endOfMonth . "+1 days"));
            $startDate =  $nextDateMonth ;
            // echo "expire : $expire<BR>";
            // echo "str end : ".strtotime($expire)."<BR>";
            // echo "str start : ".strtotime($query->date_start)."<BR>";
            // echo "diffhour: $diffhour<BR>";
            // echo "couponfound : $beforMonthRemainHour<BR>";
            // echo "next startDate : $startDate<BR>";
        
            //--- บันทึกคูปองที่ใช้ได้ ของเดือนก่อนหน้า

            $data['previus_month_has_hour'] =  $beforMonthRemainHour;
            $data['previus_month_use_real_hour'] = $beforMonthUseRealhour;
            $data['previus_month_debt_hour'] = $previusMonthDebtHour;
            $data['previus_month_max_end_date'] = strtotime($debtEndDate);
        }

        $currentMonthRemainHour = 0;
        if ($OwnerRoomSetUse==1) {
            //--- เช็คว่ามี คูปองสำหรับเดือนปัจจุบันเพียงพอหรือไม่
            $currentMonthRemainHour = ParkingUse::getRemainHourByDate($roomId, date('Y-m-d', strtotime($startDate)));
        }

        //--- ถ้าชั่วโมงใช้ที่กำหนดมาเป็น 0 คือ จนกว่าจะออก
        $isUntilOut = false;
        if ($hourSetUse==0) {
            $isUntilOut = true;
            $hourSetUse = ceil(($now-strtotime($startDate))/(60*60)) ;
        }


        //--- คูปองคงเหลือเดือนปัจจุบนั น้อยกว่า คูปองที่กำหนดมา   ให้ใช้ จำนวนคูปองจากที่เหลือ
        if ($currentMonthRemainHour<$hourSetUse) {
            $hourSetUse = $currentMonthRemainHour ;
        }

        //--- กำหนดชั่วโมงที่จะมีสิทธิ์จอดตามที่ระบุ
        $startDate = date("Y-m-d", strtotime($startDate))." ".$startTime;
        $expire = date("Y-m-d H:i:s", strtotime($startDate. "+$hourSetUse hours"));
        

     
        //--- ถ้าเวลาออกจริงน้อยกว่า เวลาที่ระบุมา ให้บันทึกเท่าที่ออกจริง
        
        $hasSetReal =false;
        if ($now < strtotime($expire)) {
            $expire = date("Y-m-d H:i:s", $now) ;
            $hasSetReal =true;
        }

        $currentMonthUseRealHour = ceil(($now-strtotime($startDate))/(60*60)) ;
        $currentMonthHasHour = ceil((strtotime($expire)-strtotime($startDate))/(60*60)) ;

        $currentMonthDebtHour = $currentMonthUseRealHour-$currentMonthHasHour;


        // echo "startDate : ".$startDate."<BR>" ;
        // echo "now : ".date("Y-m-d H:i:s",$now)."<BR>" ;
        // echo "expire : ".$expire."<BR>" ;

        // echo "currentMonthUseRealHour : $currentMonthUseRealHour<BR>";
        // echo "currentMonthHasHour : $currentMonthHasHour<BR>";
        // echo "currentMonthDebtHour : $currentMonthDebtHour<BR>";

        // echo "beforMonthUseRealhour : $beforMonthUseRealhour<BR>";
        // echo "beforMonthRemainHour : $beforMonthRemainHour<BR>";
        // echo "previusMonthDebtHour : $previusMonthDebtHour<BR>";


      

        // echo "total_use_real_hour ". $data['total_use_real_hour']."<BR>";
        // echo "total_has_hour ".$data['total_has_hour']."<BR>";
        // echo "total_debt_hour ".$data['total_debt_hour']."<BR>";

        // die;




        if ($currentMonthDebtHour>0) {
            $parkingDebt = new ParkingDebt();
            $parkingDebt->license_plate = $licensePlate ;
            $parkingDebt->license_plate_category = $licensePlateCategory;
            $parkingDebt->debt_hour = $currentMonthDebtHour ;
            $parkingDebt->parking_use_id = 0 ;
            $parkingDebt->parking_checkin_id = $id ;
            $parkingDebt->room_id = $roomId ;
            $parkingDebt->domain_id = $domainId ;
            $parkingDebt->province_id = $provinceId ;
            $parkingDebt->debt_type = $post['debt_type'] ;
            $parkingDebt->start_date = Carbon::parse(date("Y-m-d H:i:s", strtotime($expire. "+1 seconds")))  ;
            $parkingDebt->end_date = $carbonOut;
            $parkingDebt->created_at = $carbonNow;
            $parkingDebt->created_by = $userId;
            $parkingDebt->save();
            // ParkingDebt::insert($history);
        }

        $use = ParkingUse::where('parking_checkin_id', '=', $id);

        if (isset($previousMonthUseId)) {
            $use->whereNotIn('id', [$previousMonthUseId]);
        }
        $update['used_date'] = $carbonNow;
        if ($isUntilOut) {
            $update['end_date'] = $carbonNow;
        }
        //---ปรับข้อมูลใช้
        if ($hasSetReal) {
            $update['end_date'] = $carbonNow;
            $update['hour_use'] = $currentMonthUseRealHour;
        }

        

        if ($hasPreviousMonth) {
            $update['end_date'] = Carbon::parse($expire);
            $update['start_date'] = Carbon::parse($startDate);
        }
        $use->update($update);

       
        $checkIn = ParkingCheckIn::find($id)->update(
            [  'hour_use'=>$currentMonthUseRealHour+$beforMonthUseRealhour
            ,'outed_at'=>$carbonOut
            ,'used_at'=>$carbonNow
            ,'manual_out'=>$manualOut
            ]
        );

        DB::commit();

  //       if(isset($post['license_plate'])){
  //           $b4License = "";
  //           foreach ($post['license_plate'] as $key => $p) {
  //               if (!empty($b4License) && $b4License!= $p){
  //                   return $this->respondWithError($this->langMessage('กรุณาเลือกทะเบียนรถให้เหมือนกัน','Wrong License Plate'));
  //               }
  //               $b4License = $p ;
  //           }
  //       }


  //       $freeParkingMin = Setting::getVal($domainId,'PARKING_FREE') ;
  //       $now = Setting::getServerTime();
  //       $debtHour = 0 ;
  //       $lastestId = 0;
  //       if(isset($post['id'])){
  //           $hasHour = 0 ;
  //           $remainHour = 0 ;
  // // data.date
  //           $minStartDate = 0 ;
  //           foreach ($post['id'] as $key => $id) {
  //               $parking = ParkingUse::find($id);
  //               $licensePlate = $parking->license_plate;
  //               $roomId = $parking->room_id;
  //               $licensePlateCategory = $parking->license_plate_category;
  //               $provinceId = $parking->province_id;

  //               $startDate = strtotime($parking->start_date);
  //               if($freeParkingMin>0){
  //                   if(($now-$startDate)<=($freeParkingMin*60)){
  //                      break;
  //                   }
  //               }
  //               $unixStartDate = strtotime($parking->start_date) ;
  //               if($minStartDate==0||$minStartDate>$unixStartDate){
  //                   $minStartDate = $unixStartDate ;
                 
  //               }
  //               if( $minStartDate == $unixStartDate||$minStartDate<$unixStartDate){
  //                   $lastestId =  $parking->id ;
  //               }

  //               $unixEndDate = $now;
  //               if($parking->is_until_out==0){
  //                   $unixEndDate = strtotime($parking->end_date) ;
  //                   $diff = $unixEndDate -  $unixStartDate ;
  //                   $diffInHour = ceil($diff/3600) ;
  //                   $hasHour += $diffInHour;
  //                   // $lastestId =  $parking->id ;
  //               }else{

  //                   if(count($post['id'])>1){
  //                       return $this->respondWithError($this->langMessage('กรุณาใช้อี-คูปอง จนกว่าจะออก เพียงใบเดียวต่อครั้ง','Please select one E-Coupon'));
  //                   }

  //                   $remainHour = ParkingUse::getPackageRemainHour($parking->room_id,$parking->parking_buy_id);
  //                   $diff = $unixEndDate - $unixStartDate ;
  //                   $diffInHour = ceil($diff/3600) ;
  //                   $hasHour += ($remainHour<$diffInHour) ? $remainHour : $diffInHour;
  //                   $lastestId =  $parking->id ;
  //               }

                
               
  //           }



  //           $useRealHour = ceil(($now-$minStartDate)/3600);

            
          


  //           if($useRealHour>$hasHour){
  //             $debtHour = $useRealHour-$hasHour;
  //           }


  //           // echo 'มีชั่วโมงในระบบ'.$hasHour."<BR>";
  //           // echo 'ชั่วโมงใช้จริง'.$useRealHour."<BR>";
  //           // echo 'id ที่ใช่ '.$lastestId."<BR>";
  //           // echo 'ค้างชำระ '.$debtHour."<BR>";
  //       }
  //       // echo "start : $startDate<BR>";
  //       // echo "now : $now<BR>";
  //       // echo "freeParkingMin : $freeParkingMin<BR>";
  //       // var_dump(($now-$startDate)<=($freeParkingMin*60));


  //       if(isset($post['id'])){
  //           $update = ['used_date' => Carbon::now() ] ;
  //           // ParkingUse::whereIn('id', $post['id'])->whereNull('used_date')->update($update);
  //           foreach ($post['id'] as $key => $id) {

  //               $parking = ParkingUse::find($id);

  //               $startDate = strtotime($parking->start_date) ;
  //               // $now = time();
  //               // var_dump($minStartDate);
  //               // var_dump($startDate);
  //               // var_dump($minStartDate < $startDate);

  //               if( $now < $minStartDate ) {
  //                   return $this->respondWithError($this->langMessage('ไม่สามารถบันทึกการใช้คูปองนี้ได้ เนื่องจากช่วงเวลาผิด','Wrong Date'));
  //               }

         
  //               if($parking->is_until_out==1){
  //                   $hour = date('H') ;
  //                   if(date('i')>0){
  //                       $hour += 1 ;
  //                   }
  //                   $usedDate = date('Y-m-d')." $hour:00" ;
  //                   $update['end_date'] = Carbon::parse($usedDate);
  //                   $unixStartDate = strtotime($parking->start_date) ;
  //                   $unixEndDate = strtotime($usedDate) ;
  //                   $diff = $unixEndDate -  $unixStartDate ;
  //                   $diffInHour = ceil($diff/3600) ;
  //                    $update['hour_use'] = $diffInHour;

  //               }

  //               $history[$key]['parking_use_id'] = $id ;
  //               $history[$key]['domain_id'] = $domainId ;
  //               $history[$key]['created_at'] = Carbon::now();
  //               $history[$key]['created_by'] = Auth()->user()->id;
  //               $history[$key]['start_date'] = $parking->start_date;
  //               $history[$key]['end_date'] = ($parking->is_until_out) ? Carbon::parse($usedDate) : $parking->end_date;
  //               $history[$key]['status'] = 1;

  //               $parking->update($update);
  //           }
  //           ParkingHistory::insert($history);
  //       }

  //       if ($debtHour>0){
  //           $parkingDebt = new ParkingDebt();
  //           $parkingDebt->license_plate = $licensePlate ;
  //           $parkingDebt->license_plate_category = $licensePlateCategory;
  //           $parkingDebt->debt_hour = $debtHour ;
  //           $parkingDebt->parking_use_id = $lastestId ;
  //           $parkingDebt->room_id = $roomId ;
  //           $parkingDebt->domain_id = $domainId ;
  //           $parkingDebt->province_id = $provinceId ;
  //           $parkingDebt->debt_type = $post['debt_type'] ;
  //           $parkingDebt->start_date = Carbon::parse(date('Y-m-d H:i',($minStartDate+($hasHour*3600)) ))  ;
  //           $parkingDebt->end_date = Carbon::parse(date('Y-m-d H:i',($minStartDate+($useRealHour*3600))));
  //           $parkingDebt->created_at = Carbon::now();
  //           $parkingDebt->created_by = Auth()->user()->id;
  //           $parkingDebt->save();
  //           // ParkingDebt::insert($history);
  //       }

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
    
    public function notInSystem(Request $request, $domainId)
    {
        $post = $request->except('api_token');

        $parkingDebt = new ParkingDebt();
        $parkingDebt->license_plate = $post['license_plate'] ;
        $parkingDebt->license_plate_category = $post['license_plate_category'] ;
        $parkingDebt->debt_hour = 0 ;
        $parkingDebt->parking_use_id = 0 ;
        $parkingDebt->room_id = 0 ;
        $parkingDebt->domain_id = $domainId ;
        $parkingDebt->province_id = $post['province_id'] ;
        $parkingDebt->debt_type = 0 ;
        $parkingDebt->no_data_in = 1 ;
        $parkingDebt->start_date = null ;
        $parkingDebt->end_date = Carbon::now();
        $parkingDebt->created_at = Carbon::now();
        $parkingDebt->created_by = Auth()->user()->id;
        $parkingDebt->save();
         return $this->respondWithItem(['parking_debt_id'=>$parkingDebt->id]);
    }
    
    public function search(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $sqlLike = "";
        if (isset($post['license_plate_category'])&& !empty($post['license_plate_category'])) {
            $sqlLike .= " pc.license_plate_category like '%".$post['license_plate_category']."%'" ;
        }
        if (isset($post['license_plate'])&& !empty($post['license_plate'])) {
            if (!empty($sqlLike)) {
                $sqlLike .= " OR " ;
            }
            $sqlLike .= " pc.license_plate like '%".$post['license_plate']."%'" ;
        }

        if (!empty($sqlLike)) {
            $sqlLike = "  AND (
                    $sqlLike
                ) ";
        }

        // $sqlLike = " license_plate_category like '%".$post['license_plate_category']."%'  OR license_plate like '%".$post['license_plate']."%'" ;

        $sql = "SELECT pc.* 
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                ,pv.PROVINCE_NAME as province_name
                FROM parking_checkin  pc
                LEFT JOIN rooms as r 
                ON r.id=pc.room_id
                JOIN provinces as pv 
                ON pv.PROVINCE_ID = pc.province_id 
                WHERE  pc.domain_id = $domainId
                $sqlLike
                AND pc.used_at is null 
                ";

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
