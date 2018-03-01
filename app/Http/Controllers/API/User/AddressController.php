<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Address;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\Search;
use App\Models\StatusHistory;
use App\Models\Task\Task;
use App\Models\Task\TaskCategory;
use App\Models\Task\TaskHistory;
use App\Models\Task\TaskViewer;
use App\Models\User\UserHistoryEmail;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class AddressController extends ApiController
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
        // $this->middleware('auth:api');
    }

  

    public function index(){
        $idcard = auth()->user()->id_card ;
        $domainId = auth()->user()->recent_domain ;
        $data['user_address'] =  User::getAddressList($domainId,$idcard);
        return $this->respondWithItem($data);
    } 
   
    public function show(){
        $data['user'] = auth()->user() ;
        return $this->respondWithItem($data);
    }

    public function store(Request $request){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $userId = auth()->user()->id;
        $idcard = auth()->user()->id_card;
        $domainId = auth()->user()->recent_domain;

        $post['id_card'] = auth()->user()->id_card;
        $post['domain_id'] = auth()->user()->recent_domain;
       
        $address = new Address();
        $address->fill($post)->save(); 

        $data['user_address'] =  User::getAddressList($domainId,$idcard);
        return $this->respondWithItem($data);
    }  

    public function edit(Request $request,$addressId){
        $post = $request->all();
        $address = Address::find($addressId);
        $data['address'] =  $address ;
        $domainId = auth()->user()->recent_domain;
        $idcard = auth()->user()->id_card;
        $data['user_address'] =  User::getAddressList($domainId,$idcard);
        return $this->respondWithItem($data);
    }  
    
    
    public function update(Request $request,$addressId)
    {
        $post = $request->all();
        $address = Address::find($addressId);
        $address->fill($post)->save();
        $domainId = auth()->user()->recent_domain;
        $idcard = auth()->user()->id_card;
        $data['user_address'] =  User::getAddressList($domainId,$idcard);
        return $this->respondWithItem($data);
    }
    public function active(Request $request,$addressId)
    {
        $post = $request->all();
        $domainId = auth()->user()->recent_domain;
        $idcard = auth()->user()->id_card;
        Address::where('domain_id',$domainId)->where('id_card',$idcard)->update(['active'=>0]);

        $address = Address::find($addressId);
        $address->fill($post)->save();


        $data['user_address'] =  User::getAddressList($domainId,$idcard);
        return $this->respondWithItem($data);
    }

    public function destroy($addressId)
    {
        $domainId = auth()->user()->recent_domain ;
        $idCard = auth()->user()->id_card ;
        
        $cnt =  Address::where('domain_id',$domainId)->where('id_card',$idCard)->count();
        if($cnt <= 1){
            return $this->respondWithError('Cannot delete all address');
        }

        $address = Address::find($addressId)->delete();
        $domainId = auth()->user()->recent_domain;
        $idcard = auth()->user()->id_card;
        $data['user_address'] =  User::getAddressList($domainId,$idcard);
        return $this->respondWithItem($data);
    }

   

    private function validator($data)
    {
        return Validator::make($data, [
            'address' => 'required|string',
            'province_id' => 'required|numeric',
            'amphur_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'zip_code' => 'required|digits:5',
        ]);
    } 
   
    
}
