<?php

namespace App\Http\Controllers\API\Main;

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

        $startDate = $request->input('start_date',strtotime(date('Y-m-d 00:00')));
        $endDate = $request->input('end_date',strtotime(date('Y-m-d 23:59:59')));
        
         $sqlLang = (App::isLocale('en'))  ? 'en' : 'th' ; 

        $sql = " c.*,mct.name_$sqlLang as type_name " ;

        $data['contacts']  = Company::from('companies as c')
                            ->join('master_contact_type as mct','mct.id','c.type')
                            ->select(DB::raw($sql))
                            ->orderBy('c.created_at','asc')
                            ->get();
        


        return $this->respondWithItem($data);
    } 

    public function contactType(Request $request,$domainId){
        $sqlLang = (App::isLocale('en'))  ? 'en' : 'th' ; 

        $sql = " id,name_$sqlLang as name " ;

      
        $data['contact_type']  = ContactType::where('status',1)->select(DB::raw($sql))->get();


        return $this->respondWithItem($data);
    } 

    

    public function store(Request $request,$domainId){
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        if(!isset($post['status'])){
            $post['status'] = 0 ;
        }

        $query = new Company();
        $query->created_by = Auth()->user()->id; 
        $query->created_at = Carbon::now();
        $query->domain_id = $domainId ;
        $query->fill($post)->save();
        $companyId = $query->id ;
        return $this->respondWithItem(['company_id'=>$companyId]);
    }  

    public function edit($domainId,$id){
        $data['contact']  = Company::find($id);
        return $this->respondWithItem($data);
    } 

    public function update(Request $request,$domainId,$Id){
        $post = $request->all();

        unset($post['_method']);
        unset($post['api_token']);

        if(!isset($post['status'])){
            $post['status'] = 0 ;
        }

        $query = Company::find($Id) ;
      
        $query->fill($post)->save();
        return $this->respondWithItem(['contact_id'=>$Id]);
    } 
   
    public function destroy(Request $request,$domainId,$Id){
        $post = $request->all();
        $query = Parcel::find($Id)->delete();
        return $this->respondWithItem(['parcel_id'=>$Id]);
    } 
    

    private function validator($data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'type' => 'required|numeric',
           
        ]);
    }

   
    
}
