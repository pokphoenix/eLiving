<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Domain;
use App\Models\Room;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DomainController extends ApiController
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

    public function lists()
    {
        $user = Auth::user();
        // $domain = User::find($user->id)->domain;

        try {
            $sql = "SELECT d.* ,ud.approve
                FROM  domains d
                INNER JOIN user_domains ud ON ud.domain_id = d.id 
                WHERE ud.id_card = '".$user->id_card."'" ;
            $query = DB::select(DB::raw($sql));
            $data['domain'] = $query;
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }


        // $data['domain'] = $domain;
        return $this->respondWithItem($data);
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $domain = Domain::where('name', 'LIKE', '%'.$name.'%')->select('id', 'name as text')->get();
        return response()->json($domain);
    }

    public function index()
    {
        $user = Auth::user();
        // $domain = User::find($user->id);
        $user->approve_domain = Auth()->user()->checkApprove() ;
        try {
            $query = "";
            $sql = "SELECT d.* ,ud.approve
                FROM  domains d
                INNER JOIN user_domains ud ON ud.domain_id = d.id 
                INNER JOIN users u ON u.id_card = ud.id_card
                WHERE u.id = ".$user->id ;
            $query = DB::select(DB::raw($sql));
            $data['domain'] = $query;
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
        $data['user'] = $user;
       
        return $this->respondWithItem($data);
    }

    public function listDomain()
    {
        try {
            $data['domain'] = Domain::all();
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
        return $this->respondWithItem($data);
    }

    public function store(Request $request)
    {
        $post = $request->except('api_token', '_method');
        
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
       

        $auth = Auth::user();
        $user = User::find($auth->id);
        $domains = new Domain();
        $domains->fill($post)->save();

        $domainId = $domains->id ;

        DB::insert(DB::raw("insert into settings (`keys`,`values`,`status`,`domain_id`,`description`) 
            select `keys`,`values`,1,$domainId,`description` 
            from settings where domain_id = 1  ")) ;

        DB::insert(DB::raw("insert into master_contact_type (`name_th`,`name_en`,`status`,`domain_id`) 
            select `name_th`,`name_en`,1,$domainId
            from master_contact_type where domain_id = 1 ")) ;

        DB::insert(DB::raw("insert into phone_directory (`text`,`domain_id`) 
            select `text`,$domainId
            from phone_directory where domain_id = 1")) ;

        DB::insert(DB::raw("insert into pre_welcome (`text`,`created_at`,`domain_id`) 
            select `text`,now(),$domainId
            from pre_welcome where domain_id = 1")) ;


        DB::update(DB::raw("update settings set `values`='".$post['url_name']."' where `keys`='KEY_HASH' AND domain_id=$domainId "));
        DB::update(DB::raw("update settings set `values`=SUBSTR(UUID(),1,24) where `keys`='KEY_ENCRYPT' AND domain_id=$domainId "));


        $user->joinDomain($domainId, 1);
        // $user->attachRole('admin');

        $user->makeUserRole('admin', $domainId);
        $user->recent_domain = $domainId ;
        $user->save();
        return $this->respondWithItem(['domain_name'=>$post['url_name'],'domain_id'=>$domainId]);
    }


    public function update(Request $request, $id)
    {
        $post = $request->except('api_token', '_method');
        
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $domains = Domain::find($id)->fill($post)->save();
        return $this->respondWithItem(['domain_name'=>$post['url_name'],'domain_id'=>$id]);
    }

    public function destroy(Request $request, $id)
    {
        $post = $request->except('api_token', '_method');
        
        
        $count = DB::table('user_domains')->where('domain_id', $id)->count();
        echo $count ;
        die;
        if ($count>1) {
             return $this->respondWithError($this->langMessage('ไม่สามารถลบโครงการที่ท่านเลือกเนื่องจากมีผู้ใช้เข้าใช้งานเกิน 1 คนแล้ว', 'Cannot delete this domain'));
        }


        $domains = Domain::find($id)->fill($post)->save();
        return $this->respondWithItem(['domain_name'=>$post['url_name'],'domain_id'=>$id]);
    }


    public function joinStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_id' => 'required|int'
        ]);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $domainId = $request->input('domain_id');

        $auth = Auth::user();
        $user = User::find($auth->id);

        //--- เช็ค id_card ว่ามีห้องในโครงการนี้หรือไม่ ถ้ามี จะ approve ทันที
        $room = Room::where('domain_id', $domainId)->where('id_card', $user->id_card)->first();
        $approve = 0 ;
        if (!empty($room)) {
            $approve = 1 ;
            $user->joinRoom($room->id);
        }

        //--- เซ็ตเข้าร่วมโครงการ
        $user->joinDomain($domainId, $approve);

        //--- กำหนด role ให้ เป็น user
        $user->makeUserRole('user', $domainId);


        return $this->respondWithItem(["domain_id"=>$domainId ,'approve'=>$approve ]);
    }

    private function validator($request)
    {
        switch ($request->method()) {
            case 'GET':
            case 'DELETE':
                return [];
            case 'POST':
                $validate = [
                    'name' => 'required|string|max:255|unique:domains,name',
                    'url_name' => 'required|string|max:255|unique:domains,url_name',
                ];
            case 'PUT':
                $id = $request->route('domain_id');
                $validate =  [
                    'name' => 'required|string|max:255|unique:domains,name,'.$id,
                    'url_name' => 'required|string|max:255|unique:domains,url_name,'.$id,
                ];
            case 'PATCH':
            default:
                break;
        }
        return Validator::make($request->all(), $validate);
    }
}
