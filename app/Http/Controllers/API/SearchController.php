<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\ApiController ;
use App\Models\Amphur;
use App\Models\Channel\ChannelUser;
use App\Models\Company;
use App\Models\District;
use App\Models\Province;
use App\Models\Room;
use App\Models\Search;
use App\Models\Task\TaskMember;
use App\Models\UserTemp;
use App\Models\Zipcode;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Route;
use stdClass ;
class SearchController extends ApiController
{
    private $route = 'purchase/quotation' ;
    private $title = 'นิติ' ;
    private $view = 'officer.purchase.quatation' ;

    public function __construct(){
    }


    public function company(Request $request){
        $name = $request->input('name');
         // $domain = Company::all();
        $domain = Company::where( 'name','LIKE','%'.$name.'%')->where('status',1)->select('id','name as text')->get();
        return response()->json($domain);
    }

    public function user(Request $request){
        $name = $request->input('name');
         // $domain = Company::all();
        $user = User::where( 'first_name','LIKE','%'.$name.'%')
                    ->orWhere( 'last_name','LIKE','%'.$name.'%')
                    ->orWhere( 'id_card','LIKE','%'.$name.'%')
                    ->select(DB::raw( 'id,CONCAT( first_name," ",last_name) as text,id_card' ))
                    ->get();
        return response()->json($user);
    }

     public function userData(Request $request){
        $idCard = $request->input('id_card');
         // $domain = Company::all();
        $user = User::where('id_card',$idCard)
                ->select(DB::raw('first_name,last_name,id_card,email,tel'))
                ->get();
        if(empty($user)){
            return $this->respondWithError('not found');
        }
        $data['user'] = $user ;
        return $this->respondWithItem($data);
    }

    public function channelMember(Request $request,$domainId,$channelId){
        $name = $request->input('name');
        $ids = ChannelUser::where('channel_id', '=', $channelId)->pluck('user_id')->toArray();
        $user = User::from('users as u')
                    ->join('user_domains as d', 'd.id_card', '=', 'u.id_card')
                    ->whereNotIn('u.id', $ids)
                    ->where(function($q) use ($name) {
                          $q->where( 'u.first_name','LIKE','%'.$name.'%')
                            ->orWhere( 'u.last_name','LIKE','%'.$name.'%');
                    })
                    ->where('d.approve',1)
                    ->select(DB::raw("u.id,CASE WHEN u.id = ".auth()->user()->id." THEN CONCAT( u.first_name,' ',u.last_name,' (me)')
                WHEN u.nick_name is not null THEN CONCAT( u.first_name,' ',u.last_name ,' (',IFNULL(u.nick_name,''),')' )
                ELSE CONCAT( u.first_name,' ',u.last_name)
                END as text,u.id_card" ))
                    ->get();
        return response()->json($user);
    }

     public function room(Request $request,$domainId){
        $name = $request->input('name');
        $data['data'] = Room::where( 'name','LIKE','%'.$name.'%')
                    ->orWhere( 'name_prefix','LIKE','%'.$name.'%')
                    ->select(DB::raw( "id,CONCAT( IFNULL(name_prefix,''), IFNULL(name,''), IFNULL(name_surfix,'') ) as text" ))
                    ->get();
        return $this->respondWithItem($data);
    }
    
    public function memberTask(Request $request,$domainId,$taskId){
        $name = $request->input('name');
        $url = url('') ;

        $ids = TaskMember::where('task_id', '=', $taskId)->where('domain_id', '=', $domainId)->pluck('user_id')->toArray();


        $user = Search::memberTask($domainId,$name,$ids);
        return response()->json($user);
    }

    public function temp(Request $request){
        $idCard = $request->input('id_card');
         // $domain = Company::all();
        $user = UserTemp::where( 'id_card',$idCard)
                    ->where('domain_id',1)
                    ->select(DB::raw('id_card'))
                    ->first();
        $data['user'] = $user ;
        return $this->respondWithItem($data);
    }

    public function district(Request $request){
        $name = $request->input('name');
        $data = District::from('districts as d')
        ->join('amphures as a', 'd.AMPHUR_ID', '=', 'a.AMPHUR_ID')
        ->join('provinces as p', 'a.PROVINCE_ID', '=', 'p.PROVINCE_ID')
        ->join('zipcodes as z', 'z.district_code', '=', 'd.DISTRICT_CODE')
        ->select(DB::raw( "CONCAT('ต.',d.DISTRICT_NAME,' อ.',a.AMPHUR_NAME,'จ. ',p.PROVINCE_NAME,',',z.zipcode) as text,d.DISTRICT_ID as district_id,d.DISTRICT_NAME as district_name,a.AMPHUR_ID as amphur_id,a.AMPHUR_NAME as amphur_name,p.PROVINCE_ID as province_id,p.PROVINCE_NAME as province_name,z.zipcode "))
        ->where('d.DISTRICT_NAME','LIKE','%'.$name.'%')
        ->where('d.active',1)
        ->orderBy('d.DISTRICT_NAME','asc')
        ->get();
        return response()->json($data);
    } 
    public function amphur(Request $request){
        $name = $request->input('name');
        $domain = Amphur::from('amphures as a')
                ->join('provinces as p', 'a.PROVINCE_ID', '=', 'p.PROVINCE_ID')
                ->where('a.AMPHUR_NAME','LIKE','%'.$name.'%')
                ->select(DB::raw( "CONCAT(' อ.',a.AMPHUR_NAME,'จ. ',p.PROVINCE_NAME) as text,a.AMPHUR_ID as amphur_id,a.AMPHUR_NAME as amphur_name,p.PROVINCE_ID as province_id,p.PROVINCE_NAME as province_name"))
                ->where('a.AMPHUR_NAME','LIKE','%'.$name.'%')
                ->where('a.active',1)
                ->orderBy('a.AMPHUR_NAME','asc')
                ->get();
        return response()->json($domain);
    } 
    public function province(Request $request){
        $name = $request->input('name');
        $domain = Province::where('PROVINCE_NAME','LIKE','%'.$name.'%')
                ->where('active',1)
                ->select(DB::raw( "PROVINCE_NAME as text,PROVINCE_ID as id,PROVINCE_NAME as name"))
                ->orderBy('PROVINCE_NAME','asc')
                ->get();
        return response()->json($domain);
    }

    public function amphurById(Request $request){
        $provinceId = $request->input('id');
        $amphur = Amphur::where('PROVINCE_ID',$provinceId)
                ->where('active',1)
                ->select(DB::raw( "AMPHUR_NAME as name,AMPHUR_ID as id"))
                ->orderBy('AMPHUR_NAME','asc')
                ->get();
        $data['amphurs'] = $amphur ;
        return $this->respondWithItem($data);
    } 
    public function districtById(Request $request){
        $amphurId = $request->input('id');
        $amphur = District::where('AMPHUR_ID',$amphurId)
                ->where('active',1)
                ->select(DB::raw( "DISTRICT_NAME as name,DISTRICT_ID as id"))
                ->orderBy('DISTRICT_NAME','asc')
                ->get();
        $data['districts'] = $amphur ;
        return $this->respondWithItem($data);
    } 
    public function ZipcodeById(Request $request){
        $districtId = $request->input('id');
        $amphur = Zipcode::from('zipcodes as z')
                ->join('districts as d', 'z.district_code', '=', 'd.DISTRICT_CODE')
                ->where('d.DISTRICT_ID',$districtId)
                ->select(DB::raw( "z.zipcode as name,z.id as id"))
                ->first();
        $data['zipcode'] = $amphur ;
        return $this->respondWithItem($data);
    }
}
