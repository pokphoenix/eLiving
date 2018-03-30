<?php

namespace App\Http\Controllers\API\Parcel;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Notification;
use App\Models\Parcel\Parcel;
use App\Models\Room;
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

    
    public function index($domainId)
    {

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
                ORDER BY p.created_at DESC
                " ;
      
        $data['parcel_officer']  =  DB::select(DB::raw($sql));
       
        



        return $this->respondWithItem($data);
    }

    public function currentDayLetterRoom($domainId)
    {
        $ids = Parcel::where('type', 1)->whereRaw("DATE(created_at) = DATE(now())")
                ->pluck('room_id')->toArray();
        $data['data'] = Room::where('domain_id', $domainId)
                    ->whereNotIn('id', $ids)
                    ->select(DB::raw("id,CONCAT( IFNULL(name_prefix,''), IFNULL(name,''), IFNULL(name_surfix,'') ) as text"))
                    ->get();
        return $this->respondWithItem($data);
    }


    public function masterParcelType()
    {
        if (getLang()=='en') {
            $sqlSub = "name_en as name";
        } else {
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
  

    public function store(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $post['send_date'] = Carbon::parse($post['send_date']);
       

        if (isset($post['supplies_code'])) {
            $p = Parcel::where('supplies_code', $post['supplies_code'])->first();
            if (!empty($p)) {
                return $this->respondWithError($this->langMessage('เลขที่พัสดุซ้ำ', 'repeat suplies code'));
            }
        }

       

        $query              = new Parcel();
        $query->created_by  = Auth()->user()->id;
        $query->created_at  = Carbon::now();
        $query->domain_id   = $domainId ;
        $query->parcel_code = Parcel::GetID($domainId, $post['type']);
        $query->qrcode_key  = Parcel::GetHashKey($domainId);
        $query->fill($post)->save();

        $parcelId = $query->id ;


        $lang=getLang();

        $sqlSub = ",mpt.name_$lang as parcel_type_name ,mst.name_$lang as supplies_type_name";

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
                    if (getLang()=='en') {
                        $notiMsg .= "to  ".$noti->supplies_send_name." code ".$noti->supplies_code;
                    } else {
                        $notiMsg .= "ถึง  ".$noti->supplies_send_name." เลขพัสดุ ".$noti->supplies_code;
                    }
                } elseif ($noti->type==3) {
                    if (getLang()=='en') {
                        $notiMsg .= "to ".$noti->gift_receive_name."  description ".$noti->gift_description;
                    } else {
                        $notiMsg .= "ถึง  ".$noti->gift_receive_name."  ลักษณะ ".$noti->gift_description;
                    }
                }
            }
            if (getLang()=='en') {
                $notiMsg .=" room ".$noti->room_name ;
            } else {
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
        if (!empty($query)) {
            Notification::addNotificationMulti($query, $domainId, $notiMsg, 3, 6, null, true);
        }
        return $this->respondWithItem(['parcel_id'=>$parcelId,'noti_msg'=> $notiMsg]);
    }

    public function letter(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $validator = $this->validatorletter($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $post['send_date'] = Carbon::parse($post['send_date']);
       

        if (!isset($post['item'])) {
             return $this->respondWithError($this->langMessage('กรุณาเลือกห้อง', 'please choose room'));
        }

        $carbonNow = Carbon::now();
        $userId = Auth()->user()->id;
        $lang = getLang();
        $notiMsg = "";
        $sql = "SELECT name_$lang as parcel_type_name FROM master_parcel_type WHERE id= 1" ;
        $type = collect(DB::select(DB::raw($sql)))->first();
        if (!empty($type)) {
            $notiMsg .= $type->parcel_type_name;
        }


        foreach ($post['item'] as $key => $p) {
            $sql2 = "SELECT *
                FROM parcels
                WHERE type = 1 AND room_id = $p AND  DATE(created_at) = DATE(now())" ;
                $query = collect(DB::select(DB::raw($sql2)))->first();
            if (!empty($query)) {
                continue;
            }

            $insert['created_at'] = $carbonNow ;
            $insert['domain_id'] = $domainId  ;
            $insert['created_by'] = $userId ;
            $insert['parcel_code'] = Parcel::GetID($domainId, 1);
            $insert['qrcode_key'] = Parcel::GetHashKey($domainId);
            $insert['type'] = 1 ;
            $insert['room_id'] = $p ;
            $parcels[] = $insert ;

            $sql = "SELECT CONCAT( IFNULL(name_prefix,''), IFNULL(name,''), IFNULL(name_surfix,'') ) as room_name FROM rooms WHERE id= $p" ;
            $room = collect(DB::select(DB::raw($sql)))->first();
            if (!empty($room)) {
                $roomName = $room->room_name;
                if ($lang=='en') {
                    $notiMsg .=" room ".$roomName ;
                } else {
                    $notiMsg .=" ส่งห้อง ".$roomName ;
                }
                
                $sql2 = "SELECT ur.id_card
                ,ud.noti_player_id
                ,ud.noti_player_id_mobile 
                FROM user_rooms  ur
                JOIN user_domains ud 
                ON ud.id_card = ur.id_card 
                AND ud.domain_id = $domainId
                WHERE ur.approve = 1 AND ur.room_id =".$p ;
                $query = DB::select(DB::raw($sql2));
                if (!empty($query)) {
                    Notification::addNotificationMulti($query, $domainId, $notiMsg, 3, 6, null, true);
                }
            }
        }

        if (isset($parcels)) {
            Parcel::insert($parcels);
        }
        return $this->respondWithItem(['noti_msg'=> $notiMsg]);
    }

    public function edit($domainId, $id)
    {
        $data['parking_package']  = Parcel::find($id);
        return $this->respondWithItem($data);
    }

    public function update(Request $request, $domainId, $id)
    {
        $post = $request->except('api_token', '_method');
        unset($post['_method']);
        unset($post['api_token']);

        $post['send_date'] = (isset($post['send_date']) ? Carbon::parse($post['send_date']) : Carbon::now());

        if (isset($post['supplies_code'])) {
            $p = Parcel::where('supplies_code', $post['supplies_code'])->where('id', '<>', $id)->first();
            if (!empty($p)) {
                return $this->respondWithError($this->langMessage('เลขที่พัสดุซ้ำ', 'repeat suplies code'));
            }
        }

        $query = Parcel::find($id)->update($post);

        return $this->respondWithItem(['parcel_id'=>$id]);
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

    private function validatorletter($data)
    {
        return Validator::make($data, [
           'item' => 'required',
           'send_date' => 'required|',
        ]);
    }
}
