<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomUser extends Model
{
    protected $table = 'user_rooms';
    public $timestamps = false;
    
    protected $fillable = ['id_card','room_id','approve','approved_at','approved_by'];
    // protected $dates = ['created_at', 'updated_at'];
}
