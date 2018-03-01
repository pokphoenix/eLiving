<?php

namespace App\Models\Esticker;


use App;
use App\Models\Esticker\EstickerLicensePlate;
use App\Models\Province;
use App\Models\Room;
use DB;
use Illuminate\Database\Eloquent\Model;
class Esticker extends Model
{
    protected $table = 'e_sticker';
    public $timestamps = false;
    protected $fillable = ['room_id', 'domain_id','year','created_at','created_by'];
    protected $dates = ['created_at'];

    public static function getListData($domainId,$type=1,$searchQuery=null,$order=null){
        $sqlLang =  (App::isLocale('en')) ? 'tc.name_en' : 'tc.name_th' ;
    	$prioLang =  (App::isLocale('en')) ? 'mp.name_en' : 'mp.name_th' ;

        if(is_null($order)){
            $order = " ORDER BY p.created_at DESC " ;
        }

    	$sql = "select e.*
                ,pv.PROVINCE_NAME as province_name
                ,CONCAT( IFNULL(r.name_prefix,''),IFNULL(r.name,''),IFNULL(r.name_surfix,'') ) as room_name
                ,ep.license_plate_category
                ,ep.license_plate
               
            
                FROM e_sticker as e
                JOIN rooms as r 
                ON r.id = e.room_id

                Left Join e_sticker_license_plate as ep 
                ON ep.e_sticker_id= e.id
               
                JOIN provinces as pv 
                ON pv.PROVINCE_ID = ep.province_id 

				
                WHERE e.domain_id = $domainId
                ORDER BY e.created_at DESC
               ";
        
        $querys   =  DB::select(DB::raw($sql));
         $data['e_sticker'] = [];

        foreach ($querys as $key => $q) {
            $data['e_sticker'][$q->id]['id'] = $q->id ;
            $data['e_sticker'][$q->id]['room_id'] = $q->room_id ;
            $data['e_sticker'][$q->id]['room_name'] = $q->room_name ;
            $data['e_sticker'][$q->id]['year'] = $q->year ;
            $data['e_sticker'][$q->id]['created_at'] = $q->created_at ;
            $data['e_sticker'][$q->id]['domain_id'] = $q->domain_id ;
            $data['e_sticker'][$q->id]['qrcode'] = $q->qrcode ;
            if(isset($q->license_plate)){
                $license['province_name'] = $q->province_name;
                $license['license_plate'] = $q->license_plate;
                $license['license_plate_category'] = $q->license_plate_category;
                $data['e_sticker'][$q->id]['license_plate_list'][] = $license ;
            }else{
                $data['e_sticker'][$q->id]['license_plate_list'] = [];
            }
        }

       


        return array_values($data['e_sticker']) ;
    }

    public function license_plate(){
        return $this->hasMany('App\Models\Esticker\EstickerLicensePlate', 'e_sticker_id', 'id');
    }
    public function province(){
        return $this->hasOne(Province::class, 'province_id', 'province_id');
    }
    public function room(){
        return $this->hasOne(Room::class, 'id', 'room_id');
    }

    public static function getData($domainId,$id){
        $sql = "select e.*
                ,pv.PROVINCE_NAME as province_name
                ,CONCAT( IFNULL(r.name_prefix,''),IFNULL(r.name,''),IFNULL(r.name_surfix,'') ) as room_name
                ,ep.license_plate_category
                ,ep.license_plate
                ,ep.province_id
               
            
                FROM e_sticker as e
                JOIN rooms as r 
                ON r.id = e.room_id

                LEFT JOIN e_sticker_license_plate as ep 
                ON ep.e_sticker_id= e.id
               
                LEFT JOIN provinces as pv 
                ON pv.PROVINCE_ID = ep.province_id 

                
                WHERE e.domain_id = $domainId
                AND e.id = $id
                ";
        $querys   =  DB::select(DB::raw($sql));
        $data['e_sticker'] = [];
        if(empty($querys)){
            return [] ;
        }
        foreach ($querys as $key => $q) {
            $data['e_sticker'][$q->id]['id'] = $q->id ;
            $data['e_sticker'][$q->id]['room_id'] = $q->room_id ;
            $data['e_sticker'][$q->id]['room_name'] = $q->room_name ;
            $data['e_sticker'][$q->id]['year'] = $q->year ;
            $data['e_sticker'][$q->id]['created_at'] = $q->created_at ;
            $data['e_sticker'][$q->id]['domain_id'] = $q->domain_id ;
            $data['e_sticker'][$q->id]['qrcode'] = $q->qrcode ;
            if(isset($q->license_plate)){
                $license['province_name'] = $q->province_name;
                $license['province_id'] = $q->province_id;
                $license['license_plate'] = $q->license_plate;
                $license['license_plate_category'] = $q->license_plate_category;
                $data['e_sticker'][$q->id]['license_plate_list'][] = $license ;
            }else{
                $data['e_sticker'][$q->id]['license_plate_list'] = [];
            }
        }
        return array_values($data['e_sticker'])[0] ;     

    }
   

}
