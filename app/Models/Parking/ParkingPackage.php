<?php

namespace App\Models\Parking;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;
class ParkingPackage extends Model
{

	

    protected $table = 'parking_package';
    public $timestamps = false;

    protected $fillable = ['name','hour', 'price','domain_id','created_by','created_at','status','times_limit'];
   	
    protected $dates = ['created_at'];
   
}	
