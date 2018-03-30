<?php

namespace App\Http\Controllers\API\Parcel;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Notification;
use App\Models\Parcel\Parcel;
use App\Models\Setting;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class PrintController extends ApiController
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

    
    public function index(Request $request, $domainId)
    {

        $startDate = $request->input('start_date', strtotime(date('Y-m-d 00:00')));
        $endDate = $request->input('end_date', strtotime(date('Y-m-d 23:59:59')));
        


        $data['parcel_officer']  = Parcel::getList($domainId, "2", $startDate, $endDate);
        return $this->respondWithItem($data);
    }

    public function getSetting($domainId)
    {
       
       
        $data['logo_domain'] = Setting::getVal($domainId, 'LOGO_DOMAIN');
        $data['logo_officer'] = Setting::getVal($domainId, 'LOGO_OFFICER');
        $data['header_officer'] = Setting::getVal($domainId, 'PARCEL_OFFICER_HEADER');
        $data['ads'] = Setting::getVal($domainId, 'ADS_APPLICATION');


       
        return $this->respondWithItem($data);
    }
    public function getGift(Request $request, $domainId)
    {
        $startDate = $request->input('start_date', strtotime(date('Y-m-d 00:00')));
        $endDate = $request->input('end_date', strtotime(date('Y-m-d 23:59:59')));
        $data['parcel_officer']  = Parcel::getList($domainId, "3", $startDate, $endDate);
        return $this->respondWithItem($data);
    }
    public function getParcelView(Request $request, $domainId)
    {
        $id = $request->input('id');
        if (getLang()=='en') {
            $sqlSub = ",mpt.name_en as parcel_type_name ,mst.name_en as supplies_type_name";
        } else {
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
                AND p.id in (".$id.")
                ORDER BY p.created_at ASC
                " ;
        $data['parcel_officer']  = DB::select(DB::raw($sql));
        return $this->respondWithItem($data);
    }
    public function getMail(Request $request, $domainId)
    {
        $startDate = $request->input('start_date', strtotime(date('Y-m-d 00:00')));
        $endDate = $request->input('end_date', strtotime(date('Y-m-d 23:59:59')));
        $data['parcel_officer']  = Parcel::getList($domainId, "2", $startDate, $endDate);
        return $this->respondWithItem($data);
    }
   

    public function store(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

       
        $post['send_date'] = (isset($post['send_date'])) ? Carbon::parse($post['send_date']) : Carbon::now() ;
       
        $query = new Parcel();
        $query->created_by = Auth()->user()->id;
        $query->created_at = Carbon::now();
        $query->domain_id = $domainId ;
        $query->parcel_code = Parcel::GetID();
        $query->fill($post)->save();

        $parcelId = $query->id ;


        if (getLang()=='en') {
            $sqlSub = ",mpt.name_en as parcel_type_name ,mst.name_en as supplies_type_name";
        } else {
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

        if (!empty($noti)) {
            $notiMsg .=  $noti->parcel_type_name;
            if ($noti->type!=1) {
                if ($noti->type==2) {
                    $notiMsg .= "ถึง  ".$noti->supplies_send_name." ".$noti->supplies_code;
                } elseif ($noti->type==3) {
                    $notiMsg .= "ถึง  ".$noti->gift_receive_name." ".$noti->gift_description;
                }
            }
            $notiMsg .=" ส่งห้อง ".$noti->room_name ;
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
        if (!empty($query)) {
            Notification::addNotificationMulti($query, $domainId, $notiMsg, 3, 6, null, true);
        }
        return $this->respondWithItem(['parcel_id'=>$parcelId,'noti_msg'=> $notiMsg]);
    }

    public function edit($domainId, $id)
    {
        $data['parking_package']  = Parcel::find($id);
        return $this->respondWithItem($data);
    }

    public function update(Request $request, $domainId, $Id)
    {
        $post = $request->except('api_token', '_method');

        unset($post['_method']);
        unset($post['api_token']);

        $query = Parcel::find($Id) ;
        if (empty($query)) {
            $query = new Parcel();
        }
        $query->fill($post)->save();
        return $this->respondWithItem(['parcel_id'=>$Id]);
    }
   
    public function destroy(Request $request, $domainId, $Id)
    {
        $post = $request->except('api_token', '_method');
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
