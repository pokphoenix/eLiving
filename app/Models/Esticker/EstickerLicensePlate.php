<?php

namespace App\Models\Esticker;


use Illuminate\Database\Eloquent\Model;
use App;
use DB;
class EstickerLicensePlate extends Model
{
    protected $table = 'e_sticker_license_plate';
     public $timestamps = false;
    protected $fillable = ['license_plate', 'license_plate_category','province_id','e_sticker_id'];
   
   
    public function esticker()
    {
        return $this->belongsTo('App\Models\Esticker\Esticker', 'id', 'e_sticker_id');
    }
}
