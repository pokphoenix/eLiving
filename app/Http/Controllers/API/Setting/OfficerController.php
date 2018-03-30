<?php

namespace App\Http\Controllers\API\Setting;

use App\Http\Controllers\ApiController;
use App\Models\Images;
use App\Models\Setting;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class OfficerController extends ApiController
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

  

    public function index($domainId)
    {
        $data['logo_officer'] = Setting::getVal($domainId, 'LOGO_OFFICER');
        $data['name_officer'] = Setting::getVal($domainId, 'PARCEL_OFFICER_HEADER');
        return $this->respondWithItem($data);
    }
   
   
    public function store(Request $request)
    {
    }

    public function update(Request $request, $id)
    {
        $post = $request->except('api_token', '_method');
        if (isset($post['hidden_logo_officer'])) {
            $img = Images::upload($post['hidden_logo_officer']);
            if (!$img['result']) {
                return $this->respondWithError($img['error']);
            }
            if (isset($img)&&isset($img['file'])) {
                if (is_array($img['file'])) {
                    foreach ($img['file'] as $key => $v) {
                        $post['value'] =  url('public/upload/'.$v['filePath'].'/'.$v['fileName']);
                    }
                }
            }
            $update['values'] = $post['value'] ;
            Setting::where('keys', 'LOGO_OFFICER')->update($update);
        }
        Setting::where('keys', 'PARCEL_OFFICER_HEADER')->update(['values'=> $post['officer_name'] ]);

        return $this->respondWithItem(['text'=>'success']);
    }
}
