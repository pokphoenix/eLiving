<?php

namespace App\Models\Post;


use Illuminate\Database\Eloquent\Model;
use App;
use DB;
class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = ['description', 'status','domain_id','created_by','public_start_at','public_end_at','type','public_role','prioritize'];
    protected $dates = ['created_at', 'updated_at','public_start_at','public_end_at'];

    public static function getListData($domainId,$type=1,$searchQuery=null,$order=null){
        $sqlLang =  (App::isLocale('en')) ? 'tc.name_en' : 'tc.name_th' ;
    	$prioLang =  (App::isLocale('en')) ? 'mp.name_en' : 'mp.name_th' ;

        if(is_null($order)){
            $order = " ORDER BY p.created_at DESC " ;
        }

    	$sql = "select p.*
                ,u.id as member_id
                ,CONCAT( u.first_name,' ',u.last_name) as member_name 
                ,u.first_name 
                ,u.last_name 
               ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as member_img 
             
               ,IFNULL(t2.cnt_like,0) as cnt_like
               ,IFNULL(t3.cnt_comment,0) as cnt_comment
                ,$prioLang as prioritize_name
                ,p.prioritize as prioritize_id
            
                from posts as p 
                
                left join master_prioritize as mp 
                ON mp.id= p.prioritize
               
                left join users as u 
                on p.created_by = u.id

				left join (
					SELECT post_id,count(post_id) as cnt_like FROM post_like
					WHERE status_like=1 
					GROUP BY post_id 
				) t2 
				ON t2.post_id = p.id

				left join (
					SELECT post_id,count(post_id) as cnt_comment FROM post_comments
					GROUP BY post_id
				) t3 
				ON t3.post_id = p.id

                WHERE p.domain_id = $domainId
                AND p.type=$type
                $searchQuery
                $order
               ";
        
        $querys   =  DB::select(DB::raw($sql));
         $data['posts'] = [];

        foreach ($querys as $key => $q) {
            $data['posts'][$q->id]['id'] = $q->id ;
            $data['posts'][$q->id]['description'] = $q->description ;
            $data['posts'][$q->id]['created_at'] = $q->created_at ;
            $data['posts'][$q->id]['created_by'] = $q->created_by ;
            $data['posts'][$q->id]['prioritize_name'] = $q->prioritize_name ;
            $data['posts'][$q->id]['prioritize_id'] = $q->prioritize_id ;
            $data['posts'][$q->id]['status'] = $q->status ;
            $data['posts'][$q->id]['domain_id'] = $q->domain_id ;
            $data['posts'][$q->id]['public_start_at'] = $q->public_start_at ;
            $data['posts'][$q->id]['public_end_at'] = $q->public_end_at ;

            $roles = [];
            if(isset($q->public_role)){
                $roles = explode(',',$q->public_role) ;
            }

             $data['posts'][$q->id]['public_role'] = $roles ;

            $data['posts'][$q->id]['user_id'] = $q->member_id ;
            $data['posts'][$q->id]['user_displayname'] = $q->member_name ;
            $data['posts'][$q->id]['user_first_name'] = $q->first_name ;
            $data['posts'][$q->id]['user_last_name'] = $q->last_name ;
            $data['posts'][$q->id]['user_img'] = getBase64Img($q->member_img) ;
                  
            $data['posts'][$q->id]['post_like'] = $q->cnt_like ;
            $data['posts'][$q->id]['post_comment'] = $q->cnt_comment ;
            $data['posts'][$q->id]['comments'] = self::getComment($domainId,$q->id) ;
            $data['posts'][$q->id]['attachments'] = self::getAttachment($domainId,$q->id) ;
        }

       


        return array_values($data['posts']) ;
    }

    public static function getComment($domainId,$id){
		$sql = "SELECT u.id as user_id,CONCAT( u.first_name,' ',u.last_name) as user_name
                ,CASE WHEN u.profile_url is not null AND u.avartar_id=0 THEN u.profile_url
                ELSE CONCAT( '".url('')."/public/img/profile/',u.avartar_id,'.png') 
                END as img 
                ,tc.id as comment_id
                ,tc.description as comment_description
                ,UNIX_TIMESTAMP(tc.created_at) as ts_created_at
                FROM users u
                JOIN post_comments tc 
                ON tc.created_by = u.id 
                WHERE tc.domain_id =$domainId AND tc.post_id=$id ORDER BY tc.created_at DESC";
        $query = DB::select(DB::raw($sql));
		foreach ($query as $key => $q) {
			$query[$key]->img = getBase64Img($q->img);
		}
        return  $query;
	} 
	public static function getAttachment($domainId,$id){
		$sql = "SELECT ta.*
				,CONCAT( '".url('')."/public/storage/',ta.path,'/',ta.filename) as file_path
                    FROM post_attachments ta
                    WHERE ta.domain_id=$domainId AND ta.post_id=$id
                    ORDER BY ta.id DESC
                ";
        $query = DB::select(DB::raw($sql));
		foreach ($query as $key => $q) {
			$query[$key]->file_path = getBase64Img($q->file_path);
		}
        return  $query;
	}

}
