<?php

namespace App\Models\Parking;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ParkingUse extends Model
{
    use SoftDeletes;

    protected $table = 'parking_use';
    public $timestamps = false;

    protected $fillable = ['room_id','license_plate', 'start_date','end_date','used_date','domain_id','license_plate_category','province_id','parking_buy_id','hour_use','deleted_at','deleted_by','is_until_out'];

	protected $dates = ['created_at','deleted_at'];

	public static function getRemainHour($roomId,$date){
		 $sql = "SELECT 
                IFNULL((SELECT SUM(pp.hour) 
                FROM parking_buy pb  
                JOIN parking_package pp 
                ON pb.package_id = pp.id 
                WHERE pb.room_id=$roomId
                AND DATE(pb.period_at) = '$date'
               
                ),0) 
                - IFNULL((SELECT SUM(TIMESTAMPDIFF(HOUR, start_date, end_date)) FROM parking_use
                WHERE room_id=$roomId
                AND (end_date  BETWEEN DATE_FORMAT('$date' ,'%Y-%m-01') AND LAST_DAY('$date') ) 
                ),0) 
                as total_remain_hour";

       return  collect(DB::select(DB::raw($sql)))->first()->total_remain_hour ;
	}

    public static function getPackageRemainHour($roomId,$id){
         $sql = "SELECT 
                IFNULL((SELECT pp.hour
                FROM parking_buy pb  
                JOIN parking_package pp 
                ON pb.package_id = pp.id 
                WHERE pb.room_id=$roomId
                AND pb.id = $id
               
                ),0) 
                - IFNULL((SELECT SUM(hour_use) FROM (
                    SELECT SUM(TIMESTAMPDIFF(HOUR, start_date, end_date)) as hour_use FROM parking_use
                    WHERE room_id=$roomId AND is_until_out=0
                    AND parking_buy_id =$id
                    UNION ALL 
                    SELECT SUM(TIMESTAMPDIFF(HOUR, start_date, end_date)) as hour_use FROM parking_use
                    WHERE room_id=$roomId  AND is_until_out=1 AND end_date is not null
                    AND parking_buy_id =$id  
                ) t1 ),0)
                as total_remain_hour";
       return  collect(DB::select(DB::raw($sql)))->first()->total_remain_hour ;
    }

	public static function checkUsed($domainId,$roomId,$id){
		$sql = "SELECT room_id,start_date,end_date
        FROM parking_use 
        WHERE domain_id = $domainId
        AND room_id=$roomId
        AND parking_buy_id = $id 
        LIMIT 1 ;
        ";
        $used = DB::select(DB::raw($sql));
        return  (!empty($used)) ? true : false ;
	}
   
}	
