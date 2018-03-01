<?php

namespace App\Http\Controllers\API\Parking;

use App\Http\Controllers\ApiController;
use App\Models\Parking\ParkingPackage;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;


class PackageController extends ApiController
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
        $data['parking_packages']  = ParkingPackage::where('domain_id',$domainId)->get();
        return $this->respondWithItem($data);
    } 
  

    public function store(Request $request,$domainId){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $query = ParkingPackage::where('name',$post['name'])->where('domain_id',$domainId)->first();
        if(!empty($query)){
            return $this->respondWithError($this->langMessage('ชื่อแพ็คเกจซ้ำ','name is already exits'));
        }

        unset($post['api_token']);
        $query = new ParkingPackage();
        $query->created_by = Auth()->user()->id; 
        $query->created_at = Carbon::now();
        $query->domain_id = $domainId ;
        $query->fill($post)->save();
        return $this->respondWithItem(['parking_package_id'=>$query->id]);
    }  

    public function edit($domainId,$id){
        $data['parking_package']  = ParkingPackage::find($id);
        return $this->respondWithItem($data);
    } 

    public function update(Request $request,$domainId,$Id){
        $post = $request->all();

        unset($post['_method']);
        unset($post['api_token']);

        $query = ParkingPackage::find($Id) ;
        if(empty($query)){
            $query = new ParkingPackage();
        }
        $query->fill($post)->save();
        return $this->respondWithItem(['parking_package_id'=>$Id]);
    } 
    public function destroy(Request $request,$domainId,$Id){
        $post = $request->all();
        $query = PhoneDirectory::find($Id)->delete();
        return $this->respondWithItem(['phone_directory_id'=>$Id]);
    } 
    

    private function validator($data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'hour' => 'required|numeric',
            'price' => 'required|numeric',
        ]);
    }
    
}
