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
    protected $fillable = ['room_id', 'domain_id','year','created_at','created_by','no','car_number','qrcode'];
    protected $dates = ['created_at'];

    public static function getListData($domainId, $type = 1, $searchQuery = null)
    {
      

        $lang = getLang();
       

        $sql = "select epl.*
                ,e.room_id
                ,e.domain_id
                ,e.year
                ,e.qrcode
                ,mer.name_$lang as reason_text
                ,pv.PROVINCE_NAME as province_name
                ,CONCAT( IFNULL(r.name_prefix,''),IFNULL(r.name,''),IFNULL(r.name_surfix,'') ) as room_name
                ,ep.license_plate_category
                ,ep.license_plate
               
            
                FROM e_sticker_print_log epl 
                JOIN e_sticker as e
                ON e.id = epl.e_sticker_id

                
                JOIN rooms as r 
                ON r.id = e.room_id

                LEFT JOIN master_esticker_reason mer 
                ON mer.id = epl.request_type

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
            $data['e_sticker'][$q->id]['e_sticker_id'] = $q->e_sticker_id ;
            $data['e_sticker'][$q->id]['room_id'] = $q->room_id ;
            $data['e_sticker'][$q->id]['room_name'] = $q->room_name ;
            $data['e_sticker'][$q->id]['year'] = $q->year ;
            $data['e_sticker'][$q->id]['created_at'] = $q->created_at ;
            $data['e_sticker'][$q->id]['domain_id'] = $q->domain_id ;
            $data['e_sticker'][$q->id]['qrcode'] = $q->qrcode ;
            $data['e_sticker'][$q->id]['reason_text'] = $q->reason_text ;
            if (isset($q->license_plate)) {
                $license['province_name'] = $q->province_name;
                $license['license_plate'] = $q->license_plate;
                $license['license_plate_category'] = $q->license_plate_category;
                $data['e_sticker'][$q->id]['license_plate_list'][] = $license ;
            } else {
                $data['e_sticker'][$q->id]['license_plate_list'] = [];
            }
        }

       


        return array_values($data['e_sticker']) ;
    }

    public function license_plate()
    {
        return $this->hasMany('App\Models\Esticker\EstickerLicensePlate', 'e_sticker_id', 'id');
    }
    public function province()
    {
        return $this->hasOne(Province::class, 'province_id', 'province_id');
    }
    public function room()
    {
        return $this->hasOne(Room::class, 'id', 'room_id');
    }

    public static function getData($domainId, $id, $searchQuery = null)
    {

        $where = "  AND e.id = $id ";
        if (is_null($id)) {
            $where = $searchQuery ;
        }

        $sql = "select e.*
                ,pv.PROVINCE_NAME as province_name
                ,CONCAT( IFNULL(r.name_prefix,''),IFNULL(r.name,''),IFNULL(r.name_surfix,'') ) as room_name
                ,ep.license_plate_category
                ,ep.license_plate
                ,ep.province_id
                ,ep.car_owner_name
                ,ep.car_owner_tel
                ,u.first_name
                ,u.last_name
               
            
                FROM e_sticker as e
                JOIN rooms as r 
                ON r.id = e.room_id

                LEFT JOIN e_sticker_license_plate as ep 
                ON ep.e_sticker_id= e.id
               
                LEFT JOIN provinces as pv 
                ON pv.PROVINCE_ID = ep.province_id 
    
                LEFT JOIN users as u 
                ON u.id = e.created_by
                    
                WHERE e.domain_id = $domainId
                $where
                ";
        $querys   =  DB::select(DB::raw($sql));
        $data['e_sticker'] = [];
        if (empty($querys)) {
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
            $data['e_sticker'][$q->id]['first_name'] = $q->first_name ;
            $data['e_sticker'][$q->id]['last_name'] = $q->last_name ;
            $data['e_sticker'][$q->id]['no'] = $q->no ;
           
            if (isset($q->license_plate)) {
                $license['province_name'] = $q->province_name;
                $license['province_id'] = $q->province_id;
                $license['license_plate'] = $q->license_plate;
                $license['license_plate_category'] = $q->license_plate_category;
                $license['car_owner_name'] = $q->car_owner_name;
                $license['car_owner_tel'] = $q->car_owner_tel;
                $data['e_sticker'][$q->id]['license_plate_list'][] = $license ;
            } else {
                $data['e_sticker'][$q->id]['license_plate_list'] = [];
            }
        }
        return array_values($data['e_sticker'])[0] ;
    }
   

    public static function GetID($domainId)
    {
        $sql = "SELECT CONCAT(          
                DATE_FORMAT(NOW(), 'ES%Y')
                ,LPAD(IFNULL(
                    (SELECT code_id+1
                    FROM running_number
                    WHERE SUBSTR(id, 1 , 6)  = DATE_FORMAT(NOW(), 'ES%Y')
                    AND domain_id=$domainId
                    ORDER BY id DESC
                    LIMIT 1
                    ),1
                ),5,'0')) as id
                ,IFNULL(
                    (SELECT code_id+1
                    FROM running_number
                    WHERE SUBSTR(id, 1 , 6)  = DATE_FORMAT(NOW(), 'ES%Y')
                     AND domain_id=$domainId
                    ORDER BY id DESC
                    LIMIT 1
                    ),1
                ) as code_id
                " ;
               
        $query = collect(DB::select(DB::raw($sql)))->first();
        $id = $query->id;
        $codeId = $query->code_id;
        $sql = "INSERT INTO running_number (id,code_id,domain_id) VALUE ('$id','$codeId',$domainId)" ;
        DB::insert(DB::raw($sql));
        return $codeId ;
    }
}
