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

    public function lists(){
        $user = Auth::user();
        // $domain = User::find($user->id)->domain;

        try {
            $sql = "SELECT d.* ,ud.approve
                FROM  domains d
                INNER JOIN user_domains ud ON ud.domain_id = d.id 
                WHERE ud.id_card = ".$user->id_card ;
            $query = DB::select(DB::raw($sql));
            $data['domain'] = $query;
        }catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }


        // $data['domain'] = $domain;
        return $this->respondWithItem($data);
    }

    public function search(Request $request){
        $name = $request->input('name');
        $domain = Domain::where( 'name','LIKE','%'.$name.'%')->select('id','name as text')->get();
        return response()->json($domain);
    }

    public function index(){
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
        }catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
        $data['user'] = $user;
       
        return $this->respondWithItem($data);
    }

    public function store(Request $request){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $auth = Auth::user();
        $user = User::find($auth->id);
        $domains = new Domain();
        $domains->fill($post)->save(); 
        $user->joinDomain($domains->id,1);
        // $user->attachRole('admin');

        $user->makeUserRole('admin',$domains->id);
        $user->recent_domain = $domains->id ;
        $user->save();
        return $this->respondWithItem(['domain_id'=>$domains->id]);
    } 
    public function joinStore(Request $request){
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
        $room = Room::where('domain_id',$domainId)->where('id_card',$user->id_card)->first();
        $approve = 0 ;
        if(!empty($room)){
            $approve = 1 ;
            $user->joinRoom($room->id);
        }

        //--- เซ็ตเข้าร่วมโครงการ
        $user->joinDomain($domainId,$approve);

        //--- กำหนด role ให้ เป็น user 
        $user->makeUserRole('user',$domainId);


        return $this->respondWithItem(["domain_id"=>$domainId ,'approve'=>$approve ]);
    }

     private function validator($data)
    {
        return Validator::make($data, [

           
            'name' => 'required|string|max:255|unique:domains,name',
            'company_name' => 'required|string|max:255',
            'unit' => 'required|numeric',
        ]);
    }
}
