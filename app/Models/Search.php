<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Search extends Model
{
   
    public static function memberTask($domainId,$name,$ids=null){
    	$url = url('') ;
        $sql = "SELECT u.id 
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,CASE WHEN u.id = ".auth()->user()->id." THEN CONCAT( u.first_name,' ',u.last_name,' (me)')
                WHEN u.nick_name is not null THEN CONCAT( u.first_name,' ',u.last_name ,' (',IFNULL(u.nick_name,''),')' )
                ELSE CONCAT( u.first_name,' ',u.last_name)
                END as text
                ,CASE WHEN u.id = ".auth()->user()->id." THEN 1 ELSE 0 END AS is_me
                FROM users u
                JOIN (
                    SELECT DISTINCT(id_card) FROM role_user WHERE domain_id = $domainId
                    AND role_id in (2)
                ) t2
                ON t2.id_card = u.id_card
                WHERE (u.first_name like '%".$name."%' OR u.last_name like '%".$name."%') ";
        if($ids){
            $arrId = "" ;
            foreach ($ids as $id){
                $arrId .= ",$id" ; 
            }
            $arrId = substr($arrId, 1) ;
            $sql .= " AND u.id not in ($arrId)" ;
        }
        $sql .= " ORDER BY is_me DESC,u.first_name ASC";
        $query = DB::select(DB::raw($sql));
        foreach ($query as $key => $q) {
            $query[$key]->img = getBase64Img($q->img);
        }
        return  $query;
        
    }

}
