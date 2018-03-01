<?php

namespace App\Models\Chat;


use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    public $timestamps = false;
    protected $fillable = ['sender_id','receiver_id', 'text','created_at','seen_at'];
    protected $dates = ['created_at', 'seen_at'];

}
