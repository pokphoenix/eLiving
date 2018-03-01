<?php

namespace App\Models\Parking;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ParkingDebt extends Model
{
	use SoftDeletes;
    protected $table = 'parking_debt';
    public $timestamps = false;

    protected $fillable = ['debt_hour','room_id', 'license_plate_category','license_plate','start_date','end_date','created_at','created_by','deleted_at','deleted_by','parking_use_id','domain_id'];

	protected $dates = ['created_at'];


   
}	
