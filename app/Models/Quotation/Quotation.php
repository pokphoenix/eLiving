<?php

namespace App\Models\Quotation;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'quotations';

    protected $fillable = ['title','description', 'status','domain_id','vote_winner','doned_at','voting_at','created_by','status'];
    protected $dates = ['created_at', 'updated_at'];
}
