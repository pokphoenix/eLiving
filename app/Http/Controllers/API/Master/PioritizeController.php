<?php

namespace App\Http\Controllers\API\Master;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Master\Prioritize;
use App\User;
use Auth;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class PioritizeController extends ApiController
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

    private $route = 'pioritize';
    private $table = 'master_prioritize';


    public function __construct()
    {
    }

    
    public function index(Request $request, $domainId)
    {
        $data[$this->route]  = Prioritize::all();
        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId)
    {
        $user = Auth()->user() ;
        if (!$user->hasRole('admin')&&!$user->hasRole('officer')) {
            return $this->respondWithError($this->langMessage('ไอดีของคุณไม่สามารถใช้งานส่วนนี้ได้ค่ะ', 'Not Permission'));
        }
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        if (!isset($post['status'])) {
            $post['status'] = 0 ;
        }

        $query = Prioritize::create($post);
        // $query->fill($post)->save();
        $id = $query->id ;
        return $this->respondWithItem(['id'=>$id]);
    }

    public function edit($domainId, $id)
    {
        $data['data']  = Prioritize::find($id);
        return $this->respondWithItem($data);
    }

    public function update(Request $request, $domainId, $id)
    {
        $post = $request->except('api_token', '_method');
        $user = Auth()->user() ;
        if (!$user->hasRole('admin')&&!$user->hasRole('officer')) {
            return $this->respondWithError($this->langMessage('ไอดีของคุณไม่สามารถใช้งานส่วนนี้ได้ค่ะ', 'Not Permission'));
        }
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        if (!isset($post['status'])) {
            $post['status'] = 0 ;
        }

        $query = Prioritize::find($id) ;
        $query->fill($post)->save();
        return $this->respondWithItem(['id'=>$id]);
    }
   
    public function destroy(Request $request, $domainId, $id)
    {
        $user = Auth()->user() ;
        if (!$user->hasRole('admin')&&!$user->hasRole('officer')) {
            return $this->respondWithError($this->langMessage('ไอดีของคุณไม่สามารถใช้งานส่วนนี้ได้ค่ะ', 'Not Permission'));
        }
        $post = $request->except('api_token', '_method');
        $query = Prioritize::find($id)->delete();
        return $this->respondWithItem(['id'=>$id]);
    }
    

    private function validator($request)
    {
        switch ($request->method()) {
            case 'GET':
            case 'DELETE':
                return [];
            case 'POST':
                $validate = [
                    'name_th' => 'required|string|max:255|unique:'.$this->table.',name_th',
                    'name_en' => 'required|string|max:255|unique:'.$this->table.',name_en',
                ];
            case 'PUT':
                $id = $request->route($this->route);
                $validate =  [
                    'name_th' => 'required|string|max:255|unique:'.$this->table.',name_th,'.$id,
                    'name_en' => 'required|string|max:255|unique:'.$this->table.',name_en,'.$id,
                ];
            case 'PATCH':
            default:
                break;
        }
        return Validator::make($request->all(), $validate);
    }
}
