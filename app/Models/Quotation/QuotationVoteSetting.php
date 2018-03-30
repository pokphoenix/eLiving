<?php

namespace App\Models\Quotation;

use App\Models\Company;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class QuotationVoteSetting extends Model
{

    

    protected $table = 'quotation_vote_setting';
    public $timestamps = false;

    protected $fillable = ['domain_id','board_count', 'is_auto'];
}
