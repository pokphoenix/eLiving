<?php

namespace App\Tools;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Domain extends Model
{
    protected $domain ;

    public function __construct()
    {
        $sql = "SELECT *
                FROM  domains
                WHERE id = ".Auth::user()->recent_domain  ;
        $query = collect(DB::select(DB::raw($sql)))->first();
        $this->domain = $query ;
    }

    public function getDomainName()
    {
        return (isset($this->domain)) ? $this->domain->name  : '' ;
    }
}
