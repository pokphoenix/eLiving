<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'user_address';

    protected $fillable = ['id_card','domain_id', 'address','district_id','amphur_id','province_id','zip_code','address_name','active'];
    protected $dates = ['created_at', 'updated_at'];
}
