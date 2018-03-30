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

class UserController extends ApiController
{
    private $sqlSub ='' ;
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
        $lang = getLang();
        $sqlSub = ",mpt.name_$lang as parcel_type_name ,mst.name_$lang as supplies_type_name";
        $this->sqlSub = $sqlSub ;
    }

    
    public function index($domainId, $roomId)
    {
        $sql = "SELECT p.*
                ".$this->sqlSub."
                FROM parcels as p
                
                LEFT JOIN master_parcel_type mpt 
                ON mpt.id = p.type 
                LEFT JOIN master_supplies_type mst 
                 ON mst.id = p.supplies_type 
                WHERE p.domain_id = $domainId
                AND p.room_id= $roomId
                ORDER BY created_at DESC
                " ;
      
        $data['parcel_officer']  =  DB::select(DB::raw($sql));
        foreach ($data['parcel_officer'] as $key => $v) {
            $data['parcel_officer'][$key]->status_color = Parcel::statusColor($v->type);
        }


      

        return $this->respondWithItem($data);
    }

    public function masterParcelType()
    {
        $lang = getLang();
        $sqlSub = "name_$lang as name";
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
       
        $query = new Parcel();
        $query->created_by = Auth()->user()->id;
        $query->created_at = Carbon::now();
        $query->domain_id = $domainId ;
        $query->fill($post)->save();

        $parcelId = $query->id ;



        $sql = "SELECT p.*
                ,CONCAT( IFNULL(r.name_prefix,''), IFNULL(r.name,''), IFNULL(r.name_surfix,'') ) as room_name
                 ".$this->sqlSub."
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

    public function show($domainId, $roomId, $id)
    {
        $sql = "SELECT p.*
                ".$this->sqlSub."
                FROM parcels as p
                
                LEFT JOIN master_parcel_type mpt 
                ON mpt.id = p.type 
                LEFT JOIN master_supplies_type mst 
                 ON mst.id = p.supplies_type 
                WHERE p.domain_id = $domainId
                AND p.id= $id
                " ;

        $data['parcel_officer']  =  collect(DB::select(DB::raw($sql)))->first();
        $data['parcel_officer']->status_color = Parcel::statusColor($data['parcel_officer']->type);
        

        return $this->respondWithItem($data);
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
        return $this->respondWithItem(['parking_package_id'=>$Id]);
    }
    public function destroy(Request $request, $domainId, $Id)
    {
        $post = $request->except('api_token', '_method');
        $query = Parcel::find($Id)->delete();
        return $this->respondWithItem(['parcel_id'=>$Id]);
    }
    public function code(Request $request, $domainId, $code)
    {
        $post = $request->except('api_token', '_method');
        unset($post['api_token']);
        $user= Auth()->user();
        // echo "name : ".$user->first_name." ".$user->last_name;
        $query = Parcel::where('qrcode_key', $code)->first() ;
        if (empty($query)) {
            return $this->respondWithError($this->langMessage('ไม่พบข้อมูลที่คุณเรียก', 'No data found'));
        }

        $query->receive_at = Carbon::now();
        $query->receive_name = $user->first_name." ".$user->last_name;
        $query->receive_tel = $user->tel;
        

        $query->save();
        return $this->respondWithItem(['parcel_id'=>$query->id]);
    }
    public function receive(Request $request, $domainId, $Id)
    {
        $post = $request->except('api_token', '_method');

        $validator = $this->validatorReceive($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        unset($post['api_token']);

        $query = Parcel::find($Id) ;
        $query->receive_at = Carbon::now();
        $query->fill($post)->save();
        return $this->respondWithItem(['parcel_id'=>$Id]);
    }
    public function unReceive(Request $request, $domainId, $Id)
    {
       
    
        $query = Parcel::find($Id) ;
        $query->receive_at = null;
        $query->receive_name = null;
        $query->receive_tel = null;

        $query->save();
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
    private function validatorReceive($data)
    {
        return Validator::make($data, [
           'receive_name' => 'required|',
        ]);
    }
    private function validatorCode($data)
    {
        return Validator::make($data, [
            'code' => 'required|',
        ]);
    }
}
