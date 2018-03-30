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

class DomainController extends ApiController
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
        $data['logo_domain'] = Setting::getVal($domainId, 'LOGO_DOMAIN');
        $data['name_domain'] = Setting::getVal($domainId, 'NAME_DOMAIN');
       
        return $this->respondWithItem($data);
    }
   
   
    public function store(Request $request)
    {
    }

    public function update(Request $request, $id)
    {
        $post = $request->except('api_token', '_method');
        if (isset($post['hidden_logo_domain'])) {
            $img = Images::upload($post['hidden_logo_domain']);
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
            Setting::where('keys', 'LOGO_DOMAIN')->update($update);
        }

        Setting::where('keys', 'NAME_DOMAIN')->update(['values'=> $post['name_domain']]);

        return $this->respondWithItem($post);
    }
}
