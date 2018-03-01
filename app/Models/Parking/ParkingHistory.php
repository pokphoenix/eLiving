<?php

namespace App\Models\Parking;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;
class ParkingHistory extends Model
{
    protected $table = 'parking_history';
    public $timestamps = false;

    protected $fillable = ['parking_use_id','created_by', 'status'];

	protected $dates = ['created_at'];


   
}	
