<?php

namespace App\Models\Quotation;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class QuotationCompanyAttach extends Model
{


    protected $table = 'quotation_item_company_attachment';
    public $timestamps = false;
    protected $fillable = ['quotation_id','company_id', 'domain_id','path','image','file_name','file_code','file_extension','file_size'];
  
	
	public static function getAttachment($domainId,$companyId,$quotationId){
     
        $query = QuotationCompanyAttach::where('quotation_id',$quotationId)
            ->where('domain_id',$domainId)
            ->get();
        if (isset($companyId)){
            $query = QuotationCompanyAttach::where('quotation_id',$quotationId)
            ->where('domain_id',$domainId)
            ->where('company_id',$companyId)
            ->get();
        }
        foreach ($query as $key => $q) {
            // $query[$key]->img = getBase64Img(url('/public/storage/'.$q->path.'/'.$q->filename));
            $query[$key]->img = (isset($q->file_code))? CONST_APP_URL_SAVE_IMAGE.'/api/view/'.$q->file_code.'?api_token=33ae2f309f127ec78e051ba3075602fc' : getBase64Img(url('/public/storage/'.$q->path.'/'.$q->filename));
            unset($query[$key]->path);
        }
        return  $query;
    }
    public static function getAttachmentList($domainId,$quotationId){
		$query = QuotationCompanyAttach::where('quotation_id',$quotationId)
            ->where('domain_id',$domainId)
            ->get();

        foreach ($query as $key => $q) {
            // $query[$key]->img = getBase64Img(url('/public/storage/'.$q->path.'/'.$q->filename));
            $query[$key]->img = (isset($q->file_code))? CONST_APP_URL_SAVE_IMAGE.'/api/view/'.$q->file_code.'?api_token=33ae2f309f127ec78e051ba3075602fc' : getBase64Img(url('/public/storage/'.$q->path.'/'.$q->filename));
            unset($query[$key]->path);
        }
        
        return  $query;
	}
}
