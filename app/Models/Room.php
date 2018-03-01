<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';
    public $timestamps = false;
    
    protected $fillable = ['name','description', 'domain_id','id_card','name_prefix','name_surfix'];
    // protected $dates = ['created_at', 'updated_at'];


    public function users(){
        return $this->belongsToMany('User', 'user_rooms', 'room_id', 'user_id');
    }

}
