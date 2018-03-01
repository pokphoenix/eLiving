<?php

namespace App\Http\Controllers\API\Parcel;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Notification;
use App\Models\Parcel\Parcel;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class OfficerController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function __construct()
    {
    }

    
    public function index($domainId){

        if(App::isLocale('en')){
            $sqlSub = ",mpt.name_en as parcel_type_name ,mst.name_en as supplies_type_name";
        }else{
            $sqlSub = ",mpt.name_th as parcel_type_name ,mst.name_th as supplies_type_name";
        }

        $sql = "SELECT p.*
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                 $sqlSub
                FROM parcels as p
                JOIN rooms as r 
                ON r.id=p.room_id
                LEFT JOIN master_parcel_type mpt 
                ON mpt.id = p.type 
                LEFT JOIN master_supplies_type mst 
                 ON mst.id = p.supplies_type 
                WHERE p.domain_id = $domainId
                ORDER BY p.created_at DESC
                " ;
      
        $data['parcel_officer']  =  DB::select(DB::raw($sql));
       




        return $this->respondWithItem($data);
    } 

    public function masterParcelType(){
        if(App::isLocale('en')){
            $sqlSub = "name_en as name";
        }else{
            $sqlSub = "name_th as name";
        }
        $sql = "SELECT id,$sqlSub
                FROM master_parcel_type 
                WHERE status = 1 ORDER BY id ASC" ;
      
        $data['master_parcel_type']  =  DB::select(DB::raw($sql));
        
        $sql = "SELECT id,$sqlSub
                FROM master_supplies_type 
                WHERE status = 1 ORDER BY id ASC" ;
      
        $data['master_parcel_supplies_type']  =  DB::select(DB::raw($sql));


       
        return $this->respondWithItem($data);  
    }
  

    public function store(Request $request,$domainId){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $post['send_date'] = Carbon::parse($post['send_date']);
       

        if(isset($post['supplies_code'])){
            $p = Parcel::where('supplies_code',$post['supplies_code'])->first();
            if(!empty($p)){
                return $this->respondWithError($this->langMessage('เลขที่พัสดุซ้ำ', 'repeat suplies code'));
            }

        }

       

        $query = new Parcel();
        $query->created_by = Auth()->user()->id; 
        $query->created_at = Carbon::now();
        $query->domain_id = $domainId ;
        $query->parcel_code = Parcel::GetID($domainId);
        $query->fill($post)->save();

        $parcelId = $query->id ;


        if(App::isLocale('en')){
            $sqlSub = ",mpt.name_en as parcel_type_name ,mst.name_en as supplies_type_name";
        }else{
            $sqlSub = ",mpt.name_th as parcel_type_name ,mst.name_th as supplies_type_name";
        }

        $sql = "SELECT p.*
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                 $sqlSub
                FROM parcels as p
                JOIN rooms as r 
                ON r.id=p.room_id
                LEFT JOIN master_parcel_type mpt 
                ON mpt.id = p.type 
                LEFT JOIN master_supplies_type mst 
                 ON mst.id = p.supplies_type 
                WHERE p.domain_id = $domainId AND p.id= ". $parcelId
                ;
      
        $noti  =  collect(DB::select(DB::raw($sql)))->first();

        $notiMsg = "";

        if (!empty($noti)){
            $notiMsg .=  $noti->parcel_type_name;
            if($noti->type!=1){
                if($noti->type==2){
                    if (App::isLocale('en')){
                       $notiMsg .= "to  ".$noti->supplies_send_name." code ".$noti->supplies_code;
                    }else{
                        $notiMsg .= "ถึง  คุณ ".$noti->supplies_send_name." เลขพัสดุ ".$noti->supplies_code;
                    }
                    
                }elseif($noti->type==3){
                    if (App::isLocale('en')){ 
                        $notiMsg .= "to ".$noti->gift_receive_name."  description ".$noti->gift_description;
                    }else{
                        $notiMsg .= "ถึง  คุณ ".$noti->gift_receive_name."  ลักษณะ ".$noti->gift_description;
                    }
                    
                }
            }
            if (App::isLocale('en')){ 
                $notiMsg .=" room ".$noti->room_name ;
            }else{
                $notiMsg .=" ส่งห้อง ".$noti->room_name ;
            }
            
        }

        $sql2 = "SELECT ur.id_card
                ,ud.noti_player_id
                ,ud.noti_player_id_mobile 
                FROM user_rooms  ur
                JOIN user_domains ud 
                ON ud.id_card = ur.id_card 
                AND ud.domain_id = $domainId
                WHERE ur.approve = 1 AND ur.room_id =".$noti->room_id ;
        $query = DB::select(DB::raw($sql2));
        if(!empty($query)){
            Notification::addNotificationMulti($query,$domainId,$notiMsg,3,6,null,true);
        }
        return $this->respondWithItem(['parcel_id'=>$parcelId,'noti_msg'=> $notiMsg]);
    }  

    public function edit($domainId,$id){
        $data['parking_package']  = Parcel::find($id);
        return $this->respondWithItem($data);
    } 

    public function update(Request $request,$domainId,$id){
        $post = $request->all();
        unset($post['_method']);
        unset($post['api_token']);

        $post['send_date'] = (isset($post['send_date']) ? Carbon::parse($post['send_date']) : Carbon::now());

        if(isset($post['supplies_code'])){
            $p = Parcel::where('supplies_code',$post['supplies_code'])->where('id','<>',$id)->first();
            if(!empty($p)){
                return $this->respondWithError($this->langMessage('เลขที่พัสดุซ้ำ', 'repeat suplies code'));
            }

        }

        $query = Parcel::find($id)->update($post);

        return $this->respondWithItem(['parcel_id'=>$id]);
    } 
    public function destroy(Request $request,$domainId,$Id){
        $post = $request->all();
        $query = Parcel::find($Id)->delete();
        return $this->respondWithItem(['parcel_id'=>$Id]);
    } 
    

    private function validator($data)
    {
        return Validator::make($data, [
            'room_id' => 'required|numeric',
            'send_date' => 'required|',
            'type' => 'required|numeric',
           
        ]);
    }
    
}
