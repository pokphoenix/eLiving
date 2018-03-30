<?php
namespace App\Models\Parking;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParkingBuy extends Model
{

    use SoftDeletes;

    protected $table = 'parking_buy';
    public $timestamps = false;

    protected $fillable = ['domain_id','room_id', 'package_id','created_by','created_at','expired_at','user_buy_name','period_at','id_card_buyer','deleted_at','deleted_by','buyer_tel','is_offline'];
    
    protected $dates = ['created_at','expired_at','deleted_at'];


    public static function getRemain($domainId, $packageId, $roomId, $periodAt, $exceptId = null)
    {
        $subSql = "";
        if (isset($exceptId)) {
            $subSql = " AND id !=$exceptId " ;
        }
        $sql = " SELECT 
                    IFNULL( 
                    (SELECT times_limit FROM parking_package  WHERE id=$packageId AND (public_end is null OR  now() BETWEEN public_start AND public_end  ) )
                    ,0) - 
                    IFNULL( 
                    (SELECT COUNT(package_id) FROM parking_buy 
                        WHERE domain_id=$domainId 
                        AND room_id=$roomId 
                        AND package_id=$packageId 
                        AND deleted_at is null
                        AND period_at='".$periodAt."' $subSql ) 
                    ,0 ) as remain_limit";
        return collect(DB::select(DB::raw($sql)))->first()->remain_limit;
    }
}
