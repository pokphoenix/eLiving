<?php

namespace App\Models\Esticker;

use Illuminate\Database\Eloquent\Model;
use App;
use DB;

class EstickerPrintLog extends Model
{
    protected $table = 'e_sticker_print_log';
    public $timestamps = false;
    protected $fillable = ['e_sticker_id','created_at','created_by','request_name','request_tel','request_type','request_remark'];
}
