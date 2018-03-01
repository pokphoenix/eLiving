<?php

namespace App\Models\Quotation;



use App\Models\Company;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class QuotationSetting extends Model
{

	

    protected $table = 'quotation_setting';
    public $timestamps = false;

    protected $fillable = ['header_th','subject_th', 'inform_th','remark_th','sign_1_th','sign_2_th','domain_id','header_en','subject_en', 'inform_en','remark_en','sign_1_en','sign_2_en','logo_left','logo_right'];
   	

   
}	
