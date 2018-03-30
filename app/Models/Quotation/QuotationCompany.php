<?php

namespace App\Models\Quotation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QuotationCompany extends Model
{
    protected $table = 'quotation_company';
    public $timestamps = false;
    protected $fillable = ['quotation_id','company_id', 'domain_id','price_b4_vat','vat','price_total','discount','price_net','remark','payment_term','guarantee'];



    public static function companyList($domainId, $id)
    {
         $sql = "SELECT qc.price_net,c.name,c.id
                , CASE WHEN  t2.first_name is null THEN t3.first_name ELSE t2.first_name END as first_name
                , CASE WHEN  t2.last_name is null THEN t3.last_name ELSE t2.last_name END as last_name
                FROM quotation_company qc 
                JOIN companies c ON c.id = qc.company_id
                AND c.domain_id = qc.domain_id
                LEFT JOIN 
                    ( SELECT qv.company_id,u.first_name,u.last_name 
                    FROM quotation_vote  qv
                    JOIN users u ON qv.user_id = u.id
                    WHERE qv.quotation_id = $id
                    AND qv.domain_id = $domainId
                     ) as t2
                ON t2.company_id = qc.company_id
    
                LEFT JOIN 
                    ( SELECT company_id,first_name,last_name 
                    FROM quotation_vote_instead
                    WHERE quotation_id = $id
                    AND domain_id = $domainId
                     ) as t3
                ON t3.company_id = qc.company_id

                WHERE qc.quotation_id = $id
                AND qc.domain_id = $domainId
                ORDER BY qc.id ASC
                ";
        $query = DB::select(DB::raw($sql));
        $qc = [];
        if (!empty($query)) {
            foreach ($query as $key => $v) {
                $qc[$v->id]['id'] = $v->id ;
                $qc[$v->id]['name'] = $v->name ;
                $qc[$v->id]['price_net'] = $v->price_net ;
                if (!isset($qc[$v->id]['user'])) {
                     $qc[$v->id]['user'] = [];
                }
                $user = [];
                if (isset($v->first_name)&&isset($v->last_name)) {
                    $user['name'] = $v->first_name." ".$v->last_name ;
                    $qc[$v->id]['user'][] = $user ;
                }
               
                $qc[$v->id]['vote_count'] = count($qc[$v->id]['user']) ;
            }
        }
        $data['quotation_companys'] =  array_values($qc);
        return $data ;
    }
}
