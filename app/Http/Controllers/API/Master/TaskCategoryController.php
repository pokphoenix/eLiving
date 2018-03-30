<?php

namespace App\Http\Controllers\API\Master;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Task\TaskCategory;
use App\User;
use Auth;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class TaskCategoryController extends ApiController
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

    private $variable = 'task_category';
    private $table = 'master_task_category';


    public function __construct()
    {
    }

    
    public function index(Request $request, $domainId)
    {
        $data[$this->variable]  = TaskCategory::all();
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

        $repeat = TaskCategory::where('name_th', $post['name_th'])
        ->where('type', $post['type'])
        ->first();
        if (!empty($repeat)) {
            return $this->respondWithError($this->langMessage('ชื่อภาษาไทยซ้ำ', 'Name (TH) already exits'));
        }

        $repeat = TaskCategory::where('name_en', $post['name_en'])
        ->where('type', $post['type'])
        ->first();
        if (!empty($repeat)) {
            return $this->respondWithError($this->langMessage('ชื่อภาษาไทยซ้ำ', 'Name (EN) already exits'));
        }


        if (!isset($post['status'])) {
            $post['status'] = 0 ;
        }

        $query = TaskCategory::create($post);
        // $query->fill($post)->save();
        $id = $query->id ;
        return $this->respondWithItem(['id'=>$id]);
    }

    public function edit($domainId, $id)
    {
        $data['data']  = TaskCategory::find($id);
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

        $repeat = TaskCategory::where('name_th', $post['name_th'])
        ->where('type', $post['type'])
        ->where('id', '<>', $id)
        ->first();
        if (!empty($repeat)) {
            return $this->respondWithError($this->langMessage('ชื่อภาษาไทยซ้ำ', 'Name (TH) already exits'));
        }

        $repeat = TaskCategory::where('name_en', $post['name_en'])
        ->where('type', $post['type'])
        ->where('id', '<>', $id)
        ->first();
        if (!empty($repeat)) {
            return $this->respondWithError($this->langMessage('ชื่อภาษาไทยซ้ำ', 'Name (EN) already exits'));
        }

        if (!isset($post['status'])) {
            $post['status'] = 0 ;
        }

        $query = TaskCategory::find($id) ;
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
        $query = TaskCategory::find($id)->delete();
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
                    'name_th' => 'required|string|max:255',
                    'name_en' => 'required|string|max:255',
                    'color' => 'required|string|max:255',
                ];
            case 'PUT':
                $id = $request->route($this->variable);
                $validate =  [
                    'name_th' => 'required|string|max:255',
                    'name_en' => 'required|string|max:255',
                    'color' => 'required|string|max:255',
                ];
            case 'PATCH':
            default:
                break;
        }
        return Validator::make($request->all(), $validate);
    }
}
