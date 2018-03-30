<?php

namespace App\Models\Quotation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class QuotationItemCompany extends Model
{

    use SoftDeletes;

    protected $table = 'quotation_item_company';
    public $timestamps = false;
    protected $fillable = ['quotation_id','company_id', 'domain_id','quotation_item_id','price_per_unit','price'];
    protected $dates = ['deleted_at'];
    
    public static function upSert($data)
    {
        $sql = "INSERT INTO quotation_item_company (company_id,quotation_id, quotation_item_id,domain_id,price_per_unit,price) VALUES " ;

        foreach ($data as $key => $d) {
            $sql .= "(".$d['company_id'].
                    ",".$d['quotation_id'].
                    ",".$d['quotation_item_id'].
                    ",".$d['domain_id'].
                    ",".$d['price_per_unit'].
                    ",".$d['price'].")," ;
        }
        $sql = substr($sql, 0, -1) ;
        $sql .= " ON DUPLICATE KEY UPDATE
				company_id = VALUES(company_id)
				,quotation_id = VALUES(quotation_id)
				,quotation_item_id = VALUES(quotation_item_id)
				,domain_id = VALUES(domain_id)
				,price_per_unit = VALUES(price_per_unit)
				,price = VALUES(price)";
        $query = DB::insert(DB::raw($sql));
    }
}
