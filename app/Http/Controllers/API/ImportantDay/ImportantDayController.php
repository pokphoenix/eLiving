<?php

namespace App\Http\Controllers\API\ImportantDay;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Notification;
use App\Models\Setting;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;


class ImportantDayController extends ApiController
{

    public function __construct()
    {
    }

    
    public function getForMobile(Request $request,$domainId){
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $roles = auth()->user()->getRolesId();
        //$user_id = auth()->user()->id;
        //var_dump($roles); die;
        $data['important_days'] = DB::table('important_day as i')
            ->select('i.*')
            ->whereBetween('i.start_date',array($startDate,$endDate))
            ->orWhereBetween('i.end_date',array($startDate,$endDate) )
            ->whereIn('i.role_id',$roles)
            ->orderBy('i.id', 'desc')
            ->limit(1000)
            ->get();
        return $this->respondWithItem($data);
    }

    public function store(Request $request,$domainId){
        //return 'This is POST';
        //return $this->respondWithItem($result);
        if($request->has('start_date')){
            $insert['start_date'] = Carbon::parse( $request->input('start_date'));
        }
        if($request->has('end_date')){
            $insert['end_date'] = Carbon::parse( $request->input('end_date'));
        }
        if($request->has('all_day')){
            $insert['all_day'] = $request->input('all_day');
        }
        if($request->has('day_name')){
            $insert['day_name'] = $request->input('day_name');
        }
        if($request->has('priority')){
            $insert['priority'] = $request->input('priority');
        }

        if($request->has('role_id')){
            $insert['role_id'] = $request->input('role_id');
        }
        DB::table('important_day')->insert($insert);
        $data['result'] = true;
        return $this->respondWithItem($data);
    }

    public function index(Request $request,$domainId){
       // $startDate = $request->input('start_date');
       // $endDate = $request->input('end_date');
        $data['important_days'] = DB::table('important_day as i')
            ->join('roles as r', 'r.id', '=', 'i.role_id')
            ->select('i.*','r.display_name as role_name')
            //->whereBetween('i.start_date',array($startDate,$endDate))
            //->orWhereBetween('i.end_date',array($startDate,$endDate))
            ->orderBy('i.id', 'desc')
            ->get();
        return $this->respondWithItem($data);
    }

    public function edit($domainId,$id){
       // return 'This is get>>/photos/{photo}/edit';
        $data['important_days'] = DB::table('important_day as i')
            ->select('i.*')
            ->where('i.id',$id)
            ->get();
        return $this->respondWithItem($data);
    } 

    public function update(Request $request,$domainId,$id){
        if($request->has('start_date')){
            $update['start_date'] = Carbon::parse( $request->input('start_date'));
        }
        if($request->has('end_date')){
            $update['end_date'] = Carbon::parse( $request->input('end_date'));
        }
        if($request->has('all_day')){
            $update['all_day'] = $request->input('all_day');
        }
        if($request->has('day_name')){
            $update['day_name'] = $request->input('day_name');
        }
        if($request->has('priority')){
            $update['priority'] = $request->input('priority');
        }

        if($request->has('role_id')){
            $update['role_id'] = $request->input('role_id');
        }

        DB::table('important_day')
            ->where('id',$id)
            ->update($update);

        $result['result'] = true;
        return $this->respondWithItem($result);
    } 
   
    public function destroy(Request $request,$domainId,$id){
        DB::table('important_day')
            ->where('id',$id)
            ->delete();
        $result['result'] = true;
        return $this->respondWithItem($result);
    }
}
