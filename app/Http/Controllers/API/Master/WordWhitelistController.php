<?php

namespace App\Http\Controllers\API\Master;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Master\WordWhiteList;
use App\User;
use Auth;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class WordWhiteListController extends ApiController
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

    private $variable = 'word_whitelist';
    private $table = 'master_word_white_list';


    public function __construct()
    {
    }

    
    public function index(Request $request, $domainId)
    {
        $data[$this->variable]  = WordWhiteList::all();
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

        $query = WordWhiteList::create($post);
        // $query->fill($post)->save();
        $id = $query->id ;
        return $this->respondWithItem(['id'=>$id]);
    }

    public function edit($domainId, $id)
    {
        $data['data']  = WordWhiteList::find($id);
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

        $query = WordWhiteList::find($id) ;
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
        $query = WordWhiteList::find($id)->delete();
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
                    'text' => 'required|string|max:255|unique:'.$this->table.',text',
                ];
            case 'PUT':
                $id = $request->route($this->variable);
                $validate =  [
                    'text' => 'required|string|max:255|unique:'.$this->table.',text,'.$id,
                ];
            case 'PATCH':
            default:
                break;
        }
        return Validator::make($request->all(), $validate);
    }
}
