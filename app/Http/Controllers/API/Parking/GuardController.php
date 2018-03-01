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

    
    public function index($domainId){
       
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
  
    public function checkHour(Request $request,$domainId){
        $post = $request->all();
      

        if(isset($post['license_plate'])){
            $b4License = "";
            foreach ($post['license_plate'] as $key => $p) {
                if (!empty($b4License) && $b4License!= $p){
                    return $this->respondWithError($this->langMessage('กรุณาเลือกทะเบียนรถให้เหมือนกัน','Wrong License Plate'));
                }
                $b4License = $p ;
            }
        }



        $hour = date('H') ;
        if(date('i')>0){
            $hour += 1 ;
        }
       
        $debtHour = 0 ;
        $lastestId = 0;
        if(isset($post['id'])){
            $hasHour = 0 ;
            $remainHour = 0 ;
  // data.date
            $minStartDate = 0 ;
            $maxEndDate = 0 ;
            foreach ($post['id'] as $key => $id) {
              
                $parking = ParkingUse::find($id);
               
                $unixStartDate = strtotime($parking->start_date) ;
                if($minStartDate==0||$minStartDate>$unixStartDate){
                    $minStartDate = $unixStartDate ;

                } 

                if($minStartDate==0||$minStartDate<$unixStartDate){ 
                    $lastestId =  $parking->id ;
                }


               

                $unixEndDate = time();
                if($parking->is_until_out==0){
                    $unixEndDate = strtotime($parking->end_date) ;
                    $diff = $unixEndDate -  $unixStartDate ;
                    $diffInHour = ceil($diff/3600) ;
                    $hasHour += $diffInHour;
                    if($maxEndDate==0||$maxEndDate<$unixEndDate){
                        $maxEndDate = $unixEndDate ;

                    }

                }else{
                    $remainHour = ParkingUse::getPackageRemainHour($parking->room_id,$parking->parking_buy_id);
                    $diff = $unixEndDate - $unixStartDate ;
                    $diffInHour = ceil($diff/3600) ;
                    $hasHour += ($remainHour<$diffInHour) ? $remainHour : $diffInHour;
                }
            }
            $useRealHour = ceil((time()-$minStartDate)/3600);
            if($useRealHour>$hasHour){
              $debtHour = $useRealHour-$hasHour;
            }

        }
      
        $data['has_hour'] = $hasHour;
        $data['use_real_hour'] = $useRealHour;
        $data['debt_hour'] = $debtHour;
        $data['max_end_date'] = $maxEndDate;
        $data['now_date'] = time();
        return $this->respondWithItem($data);
    }


    public function store(Request $request,$domainId){
        $post = $request->all();
       
       

        if(isset($post['license_plate'])){
            $b4License = "";
            foreach ($post['license_plate'] as $key => $p) {
                if (!empty($b4License) && $b4License!= $p){
                    return $this->respondWithError($this->langMessage('กรุณาเลือกทะเบียนรถให้เหมือนกัน','Wrong License Plate'));
                }
                $b4License = $p ;
            }
        }

        $debtHour = 0 ;
        $lastestId = 0;
        if(isset($post['id'])){
            $hasHour = 0 ;
            $remainHour = 0 ;
  // data.date
            $minStartDate = 0 ;
            foreach ($post['id'] as $key => $id) {
                $parking = ParkingUse::find($id);
                $licensePlate = $parking->license_plate;
                $roomId = $parking->room_id;
                $licensePlateCategory = $parking->license_plate_category;
                $provinceId = $parking->province_id;



                $unixStartDate = strtotime($parking->start_date) ;
                if($minStartDate==0||$minStartDate>$unixStartDate){
                    $minStartDate = $unixStartDate ;

                }

                if($minStartDate==0||$minStartDate<$unixStartDate){ 
                    $lastestId =  $parking->id ;
                }

                $unixEndDate = time();
                if($parking->is_until_out==0){
                    $unixEndDate = strtotime($parking->end_date) ;
                    $diff = $unixEndDate -  $unixStartDate ;
                    $diffInHour = ceil($diff/3600) ;
                    $hasHour += $diffInHour;
                }else{

                    if(count($post['id'])>1){
                        return $this->respondWithError($this->langMessage('กรุณาใช้อี-คูปอง จนกว่าจะออก เพียงใบเดียวต่อครั้ง','Please select one E-Coupon'));
                    }

                    $remainHour = ParkingUse::getPackageRemainHour($parking->room_id,$parking->parking_buy_id);
                    $diff = $unixEndDate - $unixStartDate ;
                    $diffInHour = ceil($diff/3600) ;
                    $hasHour += ($remainHour<$diffInHour) ? $remainHour : $diffInHour;

                }

                
               
            }



            $useRealHour = ceil((time()-$minStartDate)/3600);

            // echo 'มีชั่วโมงในระบบ'.$hasHour."<BR>";
            // echo 'ชั่วโมงใช้จริง'.$useRealHour."<BR>";


            if($useRealHour>$hasHour){
              $debtHour = $useRealHour-$hasHour;
            }

        }

        if(isset($post['id'])){
            $update = ['used_date' => Carbon::now() ] ;
            // ParkingUse::whereIn('id', $post['id'])->whereNull('used_date')->update($update);
            foreach ($post['id'] as $key => $id) {

                $parking = ParkingUse::find($id);

                $startDate = strtotime($parking->start_date) ;
                // $now = time();
                // var_dump($minStartDate);
                // var_dump($startDate);
                // var_dump($minStartDate < $startDate);

                if( time() < $minStartDate ) {
                    return $this->respondWithError($this->langMessage('ไม่สามารถบันทึกการใช้คูปองนี้ได้ เนื่องจากช่วงเวลาผิด','Wrong Date'));
                }

         
                if($parking->is_until_out==1){
                    $hour = date('H') ;
                    if(date('i')>0){
                        $hour += 1 ;
                    }
                    $usedDate = date('Y-m-d')." $hour:00" ;
                    $update['end_date'] = Carbon::parse($usedDate);
                    $unixStartDate = strtotime($parking->start_date) ;
                    $unixEndDate = strtotime($usedDate) ;
                    $diff = $unixEndDate -  $unixStartDate ;
                    $diffInHour = ceil($diff/3600) ;
                     $update['hour_use'] = $diffInHour;

                }

                $history[$key]['parking_use_id'] = $id ;
                $history[$key]['domain_id'] = $domainId ;
                $history[$key]['created_at'] = Carbon::now();
                $history[$key]['created_by'] = Auth()->user()->id; 
                $history[$key]['start_date'] = $parking->start_date; 
                $history[$key]['end_date'] = ($parking->is_until_out) ? Carbon::parse($usedDate) : $parking->end_date; 
                $history[$key]['status'] = 1; 

                $parking->update($update);
            }
            ParkingHistory::insert($history);
        }

        if ($debtHour>0){
            $parkingDebt = new ParkingDebt();
            $parkingDebt->license_plate = $licensePlate ;
            $parkingDebt->license_plate_category = $licensePlateCategory;
            $parkingDebt->debt_hour = $debtHour ;
            $parkingDebt->parking_use_id = $lastestId ;
            $parkingDebt->room_id = $roomId ; 
            $parkingDebt->domain_id = $domainId ; 
            $parkingDebt->province_id = $provinceId ; 
            $parkingDebt->start_date = Carbon::parse(date('Y-m-d H:i',($minStartDate+($hasHour*3600)) ))  ;
            $parkingDebt->end_date = Carbon::parse(date('Y-m-d H:i',($minStartDate+($useRealHour*3600))));
            $parkingDebt->created_at = Carbon::now(); 
            $parkingDebt->created_by = Auth()->user()->id; 
            $parkingDebt->save();
            // ParkingDebt::insert($history);
        }

        return $this->respondWithItem(['text'=>'success']);
    }  

    public function edit($domainId,$id){
        
    } 

    public function update(Request $request,$domainId,$Id){
        $post = $request->all();

        unset($post['_method']);
        unset($post['api_token']);

        $query = ParkingPackage::find($Id) ;
        if(empty($query)){
            $query = new ParkingPackage();
        }
        $query->fill($post)->save();
        return $this->respondWithItem(['parking_package_id'=>$Id]);
    } 
    public function destroy(Request $request,$domainId,$Id){
       
    } 
    
    public function search(Request $request,$domainId){
        $post = $request->all();
        $sqlLike = "";
        if(isset($post['license_plate_category'])&& !empty($post['license_plate_category']) ){
            $sqlLike .= " pu.license_plate_category like '%".$post['license_plate_category']."%'" ;
        }
        if(isset($post['license_plate'])&& !empty($post['license_plate']) ){
            if(!empty($sqlLike)){
                $sqlLike .= " OR " ;
            }
            $sqlLike .= " pu.license_plate like '%".$post['license_plate']."%'" ;
        }

        if(!empty($sqlLike)){
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
