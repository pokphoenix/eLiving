<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserTemp extends User
{
    protected $table = 'user_temps';

    
    protected $fillable = ['id','domain_id', 'first_name','last_name','id_card','email','remark','tel'];
    // protected $dates = ['created_at', 'updated_at'];


    public static function getEditData($domainId,$idcard){
        $sql = "SELECT u.id_card,u.first_name,u.last_name,u.email,u.tel,r.name as room_name
        ,u.remark,CASE WHEN ud.approve=4 THEN 1 ELSE 0 END as is_ban
        ,u.nick_name
                FROM  user_temps u 
                LEFT JOIN user_domains ud
                ON ud.id_card = u.id_card
                AND ud.domain_id = ".$domainId."
                LEFT JOIN user_rooms ur 
                ON ur.id_card = u.id_card
                LEFT JOIN rooms r 
                ON r.id = ur.room_id
                AND r.domain_id = ud.domain_id
                WHERE u.id_card = '".$idcard."'" ;
        $user = collect(DB::select(DB::raw($sql)))->first(); 

        $user = (array)$user;
        if(!empty($user)){
            $user['role'] = [];
            $sql2 = "SELECT rol.name as role_name
                FROM  role_user ru 
                LEFT JOIN roles rol 
                ON rol.id = ru.role_id
                WHERE ru.id_card = '".$idcard."' AND ru.domain_id=$domainId" ;
            $query2 = DB::select(DB::raw($sql2)); 
            if(!empty($query2)){
                foreach ($query2 as $key => $q) {
                     $user['role'][] = $q->role_name ;
                }
               
            } 
        }
       return $user ;
    }

}
