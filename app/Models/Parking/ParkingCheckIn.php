<?php

namespace App\Models\Parking;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;

class ParkingCheckIn extends Model
{
    protected $table = 'parking_checkin';
    public $timestamps = false;

    protected $fillable = ['license_plate','license_plate_category', 'province_id','created_at','created_by','room_id','is_no_room','outed_at','hour_use','set_used','set_used_hour','set_used_at','set_used_by','used_at','is_until_out','coupon_time_limit','manual_in','manual_out','free_park'];

    // protected $dates = ['created_at'];
}
