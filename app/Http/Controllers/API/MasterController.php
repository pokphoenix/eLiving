<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Amphur;
use App\Models\District;
use App\Models\Domain;
use App\Models\Master\ChannelType;
use App\Models\Master\Prioritize;
use App\Models\Province;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MasterController extends ApiController
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

    public function channeltype(Request $request){
        $query = ChannelType::where('status',1)->get();
        $data['channel_type'] = $query ;
        return $this->respondWithItem($data);

    }
    public function channelicon(Request $request){
        $query = ['bank','bed','beer','bell','bicycle','book','camera','coffee','cutlery','gamepad','gift','glass','headphones','lock','soccer-ball-o','star','video-camera'];
        $data['channel_icon'] = $query ;
        return $this->respondWithItem($data);

    }

     public function unit(Request $request){
        $query = Domain::unitslist();
        $unit = [];
        foreach ($query as $key => $q) {
            $unit[$key]['id'] = $key;
            $unit[$key]['name'] = $q;
        }
        $data['units'] = array_values($unit);
        return $this->respondWithItem($data);

    }

    public function address(Request $request){
        $data['roles'] = Role::all()->toArray();
        $data['districts'] = District::orderBy('DISTRICT_NAME','ASC')->get()->toArray();
        $data['amphurs'] = Amphur::orderBy('AMPHUR_NAME','ASC')->get()->toArray();
        $data['provinces'] = Province::orderBy('PROVINCE_NAME','ASC')->get()->toArray();

        foreach ( $data['districts'] as $key => $value) {
             $data['districts'][$key] = array_change_key_case($value, CASE_LOWER);
        }
        foreach ( $data['amphurs'] as $key => $value) {
             $data['amphurs'][$key] = array_change_key_case($value, CASE_LOWER);
        }
        foreach ( $data['provinces'] as $key => $value) {
             $data['provinces'][$key] = array_change_key_case($value, CASE_LOWER);
        }

        return $this->respondWithItem($data);

    }
    
     public function role(Request $request){
        $data['roles'] = Role::where('id','<>',7)->get()->toArray();
        return $this->respondWithItem($data);
    } 
    public function prioritize(Request $request){
        $name = (App::isLocale('en')) ? "name_en" : "name_th" ;
        $select = " id,$name as name,status" ;
        $data['prioritizes'] = Prioritize::select(DB::raw($select))->get()->toArray();
        return $this->respondWithItem($data);
    }
}
