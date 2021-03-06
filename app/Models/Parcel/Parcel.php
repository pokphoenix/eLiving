<?php

namespace App\Models\Parcel;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use App;

class Parcel extends Model
{
    protected $table = 'parcels';
    public $timestamps = false;

    protected $fillable = ['room_id','type','supplies_send_name','supplies_type','supplies_code','gift_receive_name','gift_send_name','gift_description','receive_name','receive_at','receive_tel','created_at','created_by','domain_id','send_date','position'];

    protected $dates = ['created_at','send_date'];

    
    public static function statusColor($type)
    {
        switch ($type) {
            case 1:
                $color = "#00c0ef" ;
                break;
            case 2:
                $color = "#f39c12" ;
                break;
            case 3:
                $color = "#00a65a" ;
                break;
        }

        return $color ;
    }

    public static function GetID($domainId, $type)
    {
        switch ($type) {
            case 1:
                $code = 'PL';
                break;
            case 2:
                $code = 'PS';
                break;
            case 3:
                $code = 'PG';
                break;
            default:
                $code = 'BB';
                break;
        }

        $sql = "SELECT CONCAT(          
                DATE_FORMAT(NOW(), '$code%Y%m%d')
                ,LPAD(IFNULL(
                    (SELECT SUBSTR(id, 11 , 13)+1
                    FROM running_number
                    WHERE SUBSTR(id, 1 , 10)  = DATE_FORMAT(NOW(), '$code%Y%m%d')
                    AND domain_id=$domainId
                    ORDER BY id DESC
                    LIMIT 1
                    ),1
                ),4,'0')) as id
				,IFNULL(
                    (SELECT SUBSTR(id, 11 , 13)+1
                    FROM running_number
                    WHERE SUBSTR(id, 1 , 10)  = DATE_FORMAT(NOW(), '$code%Y%m%d')
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

    public static function GetHashKey($domainId)
    {
        $hash = time().$domainId.str_random(10);
        $base64Hash = base64_encode($hash);
        return substr($base64Hash, 0, 20);
    }

    public static function getList($domainId, $arrayID, $startDate = null, $endDate = null)
    {
        $lang = getLang();
        $sqlSub = ",mpt.name_$lang as parcel_type_name ,mst.name_$lang as supplies_type_name";

        $sql = "SELECT p.*
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                 $sqlSub
                FROM parcels as p
                JOIN rooms as r 
                ON r.id=p.room_id
                LEFT JOIN master_parcel_type mpt 
                ON mpt.id = p.type 
                LEFT JOIN master_supplies_type mst 
                 ON mst.id = p.supplies_type 
                WHERE p.domain_id = $domainId
                AND p.type in (".$arrayID.")
                
                " ;
        
        $search = " AND DATE_FORMAT(p.created_at,'%Y%m%d') = DATE_FORMAT(now(),'%Y%m%d')" ;

        if (isset($startDate)&&isset($endDate)) {
            $search = "AND p.created_at BETWEEN from_unixtime($startDate) AND from_unixtime($endDate) ";
        }

        $sql .= $search ;
        $sql .= " ORDER BY p.created_at ASC" ;

  

        return   DB::select(DB::raw($sql));
    }
}
