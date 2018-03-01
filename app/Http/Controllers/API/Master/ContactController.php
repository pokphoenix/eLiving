<?php

namespace App\Http\Controllers\API\Master;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Master\ContactType;
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

class ContactController extends ApiController
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

    
    public function index(Request $request,$domainId){
        $data['contact_type']  = ContactType::all();
        return $this->respondWithItem($data);
    } 

    public function store(Request $request,$domainId){
        $user = Auth()->user() ;
        if(!$user->hasRole('admin')&&!$user->hasRole('officer')){
            return $this->respondWithError($this->langMessage('ไอดีของคุณไม่สามารถใช้งานส่วนนี้ได้ค่ะ','Not Permission'));
        }
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        if(!isset($post['status'])){
            $post['status'] = 0 ;
        }

        $query = new ContactType();
        $query->domain_id = $domainId ;
        $query->fill($post)->save();
        $id = $query->id ;
        return $this->respondWithItem(['id'=>$id]);
    }  

    public function edit($domainId,$id){
        $data['data']  = ContactType::find($id);
        return $this->respondWithItem($data);
    } 

    public function update(Request $request,$domainId,$id){
        $post = $request->all();
        $user = Auth()->user() ;
        if(!$user->hasRole('admin')&&!$user->hasRole('officer')){
            return $this->respondWithError($this->langMessage('ไอดีของคุณไม่สามารถใช้งานส่วนนี้ได้ค่ะ','Not Permission'));
        }

        unset($post['_method']);
        unset($post['api_token']);

        if(!isset($post['status'])){
            $post['status'] = 0 ;
        }

        $query = ContactType::find($id) ;
        $query->fill($post)->save();
        return $this->respondWithItem(['id'=>$id]);
    } 
   
    public function destroy(Request $request,$domainId,$id){
        $user = Auth()->user() ;
        if(!$user->hasRole('admin')&&!$user->hasRole('officer')){
            return $this->respondWithError($this->langMessage('ไอดีของคุณไม่สามารถใช้งานส่วนนี้ได้ค่ะ','Not Permission'));
        }
        $post = $request->all();
        $query = ContactType::find($id)->delete();
        return $this->respondWithItem(['id'=>$id]);
    } 
    

    private function validator($data)
    {
        return Validator::make($data, [
            'name_th' => 'required|max:255',
            'name_en' => 'required|max:255',
           
        ]);
    }

   
    
}
