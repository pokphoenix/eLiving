<?php

namespace App\Models\Parking;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;
class ParkingUseHistory extends Model
{
    protected $table = 'parking_use_history';
    public $timestamps = false;

    protected $fillable = ['parking_use_id','parking_buy_id', 'hour_use','created_at','created_by','package_id','room_id'];

	protected $dates = ['created_at'];

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
	public static function checkUsed($domainId,$roomId,$id){
		$sql = "SELECT room_id,start_date,end_date
        FROM parking_use_history 
        WHERE domain_id = $domainId
        AND room_id=$roomId
        AND parking_buy_id = $id
        ";
        $used = DB::select(DB::raw($sql));
        return  (!empty($used)) ? true : false ;
	}
   
}	
