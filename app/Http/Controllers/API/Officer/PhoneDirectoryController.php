<?php

namespace App\Http\Controllers\API\Officer;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Images;
use App\Models\Notification;
use App\Models\PhoneDirectory;
use App\Models\Resolution\Resolution;
use App\Models\Resolution\ResolutionComment;
use App\Models\Resolution\ResolutionCompany;
use App\Models\Resolution\ResolutionCompanyAttach;
use App\Models\Resolution\ResolutionHistory;
use App\Models\Resolution\ResolutionItem;
use App\Models\Resolution\ResolutionItemCompany;
use App\Models\Resolution\ResolutionVote;
use App\Models\Room;
use App\Models\StatusHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class PhoneDirectoryController extends ApiController
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

    public function search(Request $request)
    {
    }

    public function index($domainId)
    {
        $data['phone_directory']  = PhoneDirectory::where('domain_id', $domainId)->first();
        return $this->respondWithItem($data);
    }
    public function data($domainId, $Id)
    {

        $data = ResolutionItem::getItemData($domainId, $Id);
        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId)
    {
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        unset($post['api_token']);

        $query = new PhoneDirectory();
        $query->domain_id = $domainId;
        $query->fill($post)->save();
        return $this->respondWithItem(['phone_directory_id'=>$query->id]);
    }
    public function update(Request $request, $domainId, $Id)
    {
        $post = $request->except('api_token', '_method');
        $query = PhoneDirectory::where('domain_id', $domainId)->first() ;
        if (empty($query)) {
            $query = new PhoneDirectory();
            $query->domain_id = $domainId;
        }
        $query->fill($post)->save();
        return $this->respondWithItem(['phone_directory_id'=>$Id]);
    }
    public function destroy(Request $request, $domainId, $Id)
    {
        $post = $request->except('api_token', '_method');
        $query = PhoneDirectory::find($Id)->delete();
        return $this->respondWithItem(['phone_directory_id'=>$Id]);
    }
    

    private function validator($data)
    {
        return Validator::make($data, [
            'title' => 'required|string|max:255|unique:resolutions,title',
        ]);
    }
}
