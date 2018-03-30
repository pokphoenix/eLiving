<?php

namespace App\Models\Quotation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class QuotationComment extends Model
{
    protected $table = 'quotation_comments';
    public $timestamps = false;

    protected $fillable = ['quotation_id','domain_id', 'description','created_at','created_by'];
}
