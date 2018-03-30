<?php

namespace App\Models\Quotation;

use Illuminate\Database\Eloquent\Model;

class QuotationHistory extends Model
{
    protected $table = 'quotation_historys';
    public $timestamps = false;
    protected $fillable = ['quotation_id', 'domain_id','status','created_by','pin','assign_to_user_id','move_to_pioritized','quotation_comment_id','quotation_attach_id'];
}
