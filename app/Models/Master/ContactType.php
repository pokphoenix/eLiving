<?php

namespace App\Models\Master;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    protected $table = 'master_contact_type';
    public $timestamps = false;
    
    protected $fillable = ['name_th','name_en','status','domain_id'];
    // protected $dates = ['created_at', 'updated_at'];

    

}
