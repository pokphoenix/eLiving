<?php

namespace App\Models\Routine;


use App\Facades\Permission;
use App\Models\Room;
use App\Models\Task\TaskCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Routine extends Model
{
    protected $table = 'routines';
    public $timestamps = false;
    protected $fillable = ['title','category_id','domain_id','status','is_all_day','started_at','ended_at','repeat_type','is_never','repeat_ended_at','created_at','lastest_at'];
    protected $dates = ['created_at', 'repeat_ended_at','started_at','ended_at','lastest_at'];
    protected $hidden = ['api_token'];

    public function setStartedAtAttribute($value) {
      $this->attributes['started_at'] = (isset($value)) ? Carbon::createFromTimestamp(strtotime($value))->toDateTimeString()  : null ;
	} 
	public function setEndedAtAttribute($value) {
      	$this->attributes['ended_at'] = (isset($value)) ? Carbon::createFromTimestamp(strtotime($value))->toDateTimeString() : null ;
	} 
	public function setLastestAtAttribute($value) {
      $this->attributes['lastest_at'] = (isset($value)) ? Carbon::createFromTimestamp(strtotime($value))->toDateTimeString() : null ;
	} 
	public function setRepeatEndedAtAttribute($value) {
      $this->attributes['repeat_ended_at'] = (isset($value)) ? Carbon::createFromTimestamp(strtotime($value))->toDateTimeString() : null ;
	} 


	public static function getData($domainId,$cardId){

      $data['routine'] = Routine::find($cardId);

      return $data ;
  }

  public static function category(){
      return [ ['id'=>1,'name'=>'Technician'] ,['id'=>2,'name'=>'Adminitation'] ];
  }
	


}
