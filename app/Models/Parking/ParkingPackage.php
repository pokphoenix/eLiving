<?php

namespace App\Models\Parking;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParkingPackage extends Model
{
    use SoftDeletes;
    

    protected $table = 'parking_package';
    public $timestamps = false;

    protected $fillable = ['name','hour', 'price','domain_id','created_by','created_at','status','times_limit','deleted_at','deleted_by','public_start','public_end'];
    
    protected $dates = ['created_at','deleted_at','public_start','public_end'];
}
