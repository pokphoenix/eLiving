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

    
    public function index($domainId)
    {

         $sql = "SELECT *
                FROM parking_package 
                WHERE domain_id=$domainId
                AND public_start<now()
                AND (public_end is null OR public_end >now())
                AND deleted_at is null
                ORDER BY public_start ASC" ;
        $data['parking_packages']  =  DB::select(DB::raw($sql));
        // $data['parking_packages']  = ParkingPackage::where('domain_id',$domainId)
        // ->where('public_start','>',)
        // ->get();
        return $this->respondWithItem($data);
    }
    public function search($domainId)
    {
        $data['parking_packages']  = ParkingPackage::where('domain_id', $domainId)->withTrashed()->get();
        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId)
    {
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            $request->except('api_token', '_method');
        }

        $query = ParkingPackage::where('name', $post['name'])->where('domain_id', $domainId)->first();
        if (!empty($query)) {
            return $this->respondWithError($this->langMessage('ชื่อแพ็คเกจซ้ำ', 'name is already exits'));
        }

        // echo Carbon::parse($post['public_start'])."<BR>" ;
        // echo gettype($post['public_end']) ;
        // var_dump(isset($post['public_end'])&&$post['public_end']!="null") ;
        //die;

        unset($post['api_token']);
        $query = new ParkingPackage();
        $query->created_by = Auth()->user()->id;
        $query->created_at = Carbon::now();
        $query->public_start = isset($post['public_start']) ?  Carbon::parse($post['public_start']) : Carbon::now() ;
        $query->public_end = isset($post['public_end'])&&$post['public_end']!="null" ? Carbon::parse($post['public_end']) : null ;
        $query->domain_id = $domainId ;

        unset($post['public_start']);
        unset($post['public_end']);

        $query->fill($post)->save();
        return $this->respondWithItem(['parking_package_id'=>$query->id]);
    }

    public function edit($domainId, $id)
    {
        $data['parking_package']  = ParkingPackage::find($id);
        return $this->respondWithItem($data);
    }

    public function update(Request $request, $domainId, $Id)
    {
        $post = $request->all();

        unset($post['_method']);
        unset($post['api_token']);

        $query = ParkingPackage::find($Id) ;
        if (empty($query)) {
            $query = new ParkingPackage();
        }

        $query->public_start = isset($post['public_start']) ?  Carbon::parse($post['public_start']) : Carbon::now() ;
        $query->public_end = isset($post['public_end'])&&$post['public_end']!="null" ? Carbon::parse($post['public_end']) : null ;
        unset($post['public_start']);
        unset($post['public_end']);

        $query->fill($post)->save();
        return $this->respondWithItem(['parking_package_id'=>$Id]);
    }
    public function destroy(Request $request, $domainId, $Id)
    {
        $post = $request->all();
        $query = ParkingPackage::find($Id);

        if (!empty($query)) {
            $query->update(['deleted_by'=>Auth()->user()->id]);
            $query->delete();
        }

        
        return $this->respondWithItem(['parking_package_id'=>$Id]);
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
