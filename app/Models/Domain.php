<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $table = 'domains';

    protected $fillable = ['name','unit', 'residence_name','company_name'];
    protected $dates = ['created_at', 'updated_at'];


    public function users(){
        return $this->belongsToMany('User', 'user_domains', 'domain_id', 'id_card');
    }

    public static function unitslist(){
    	return [1=>"01-10",2=>"11-15",3=>"16-25",4=>"26-50",5=>"51-75"
    			,6=>"76-100",7=>"101-150",8=>"151-200",9=>"201-250",10=>"251-300"
    			,11=>"301-400",12=>"401-500",13=>"501-600",14=>"601-700",15=>"701-800"
    			,16=>"801-900",17=>"901-1000",18=>"1001-1100",19=>"1101-1200",20=>"1201-1300"
    			,21=>"1301-1400",22=>"1401-1500",23=>"1501-1600",24=>"1601-1700"
    	  ] ;
    }
}
