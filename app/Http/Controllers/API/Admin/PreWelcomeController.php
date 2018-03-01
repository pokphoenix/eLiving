<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Admin\PreWelcome;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Validator;

class PreWelcomeController extends ApiController
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

    public function search(Request $request){
      
    }

    public function index($domainId){
        $data['pre_welcome']  = PreWelcome::find(1);
        return $this->respondWithItem($data);
    } 
   
    public function store(Request $request,$domainId){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        unset($post['api_token']);

        $query = new PreWelcome();
        $query->fill($post)->save();
        return $this->respondWithItem(['pre_welcome_id'=>$query->id]);
    }  
    public function update(Request $request,$domainId,$Id){
        $post = $request->all();
        $query = PreWelcome::find($Id) ;
        if(empty($query)){
            $query = new PreWelcome();
        }

        $query->fill($post)->save();

        return $this->respondWithItem(['pre_welcome_id'=>$Id]);
    } 
    public function destroy(Request $request,$domainId,$Id){
        $post = $request->all();
        $query = PreWelcome::find($Id)->delete();
        return $this->respondWithItem(['pre_welcome_id'=>$Id]);
    } 
    private function validator($data)
    {
        return Validator::make($data, [
            'description' => 'required|string|max:255',
        ]);
    }
    
}
