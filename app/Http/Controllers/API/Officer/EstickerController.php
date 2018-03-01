<?php

namespace App\Http\Controllers\API\Officer;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Esticker\Esticker;
use App\Models\Esticker\EstickerLicensePlate;
use App\Models\Notification;
use App\Models\Province;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class EstickerController extends ApiController
{
    private $type = 3 ;

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

    public function logPrint($domainId,$id){
        $userId= Auth()->user()->id;
        $sql = "INSERT INTO e_sticker_print_log
                (e_sticker_id,created_at,created_by)
                VALUES
                ($id,now(),$userId)";
        DB::insert(DB::raw($sql));
        return $this->respondWithItem(['text'=>'success']);
    }

    public function index($domainId){
        $likeRoleQuery = "";
        $searchQuery = "";
        // $sql = "SELECT r.name as role_name 
        //         FROM role_user  ru
        //         JOIN roles r ON r.id=ru.role_id
        //         WHERE ru.id_card = '".Auth()->user()->id_card."' AND ru.domain_id=".$domainId ;
        // $roles = DB::select(DB::raw($sql));
        // if (!empty($roles)){
        //     $roleQuery = "";
        //     foreach ($roles as $key => $r) {
        //         $roleQuery .= " or p.public_role like '%".$r->role_name."%' ";
        //     }
        //     $roleQuery = substr($roleQuery,3);
        //     $likeRoleQuery .= " AND ( $roleQuery  ) ";
        // }

        // $searchQuery = "AND (now() BETWEEN  p.public_start_at  AND p.public_end_at  OR now() >  p.public_start_at )  ".$likeRoleQuery ;
        $data['e_sticker'] = Esticker::getListData($domainId,$this->type,$searchQuery);
     
        return $this->respondWithItem($data);
    } 
    

    public function store(Request $request,$domainId){
        $userId = Auth::user()->id ;
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $license = (gettype($post['license_plate_list'])=="string") ? json_decode($post['license_plate_list']) : $post['license_plate_list'] ;
       

        
        $now = Carbon::now();
        $folderName = "upload/qrcode/".$domainId."/".$now->year."/" ;
        $fileName = $post['room_id'].".png" ;
        $filePath =  $folderName.$fileName ;

        $insert = new Esticker();
        $insert->room_id = $post['room_id'] ;
        $insert->domain_id = $domainId ;
        $insert->year = $post['year'] ;
        $insert->created_at = $now ;
        $insert->created_by = Auth()->user()->id ;
        $insert->qrcode = url("public/".$filePath) ;
        $insert->save();
        $estickerId = $insert->id ;


      


        foreach ($license as $key => $value) {
            $ins = new EstickerLicensePlate();
            if(is_array($value)){
                $ins->license_plate = $value['license_plate'];
                $ins->license_plate_category = $value['license_plate_category'];
                $ins->province_id = $value['province_id'];
            }else{
                $ins->license_plate = $value->license_plate;
                $ins->license_plate_category = $value->license_plate_category;
                $ins->province_id = $value->province_id;
            }
            $ins->e_sticker_id =  $estickerId;
            $ins->save();
        }

        


        
        $esticker =  Esticker::getData($domainId,$estickerId);

      
      
        $text = "";
        if(!empty($esticker['license_plate_list'])){
            foreach ($esticker['license_plate_list'] as $key => $v) {
                $text .= ",เลขทะเบียนรถ ".$v['license_plate_category']." ".$v['license_plate']." ". $v['province_name'] ;
            }
        }
        $text .=" ปีที่ใช้งาน ".($esticker['year']+543)." วันที่ออกสติกเกอร์ ".date('d m',strtotime($esticker['created_at']))." ".(date('Y',strtotime($esticker['created_at']))+543) ;
        $text = substr($text,1);
    
        $qrcode = $this->generateQRCode($text,$domainId,$post['room_id']);

        $data['e_sticker'] = $esticker;
        $data['e_sticker']['code'] = $qrcode['code'] ;
        return $this->respondWithItem($data);
    }  

    public function edit(Request $request,$domainId,$id){
        $query = Esticker::getData($domainId,$id);
        $data['e_sticker'] = $query ;
        return $this->respondWithItem($data);
    }

    public function update(Request $request,$domainId,$id){
        $userId = Auth::user()->id ;
        $post = $request->all();
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $license = (gettype($post['license_plate_list'])=="string") ? json_decode($post['license_plate_list']) : $post['license_plate_list'] ;
       

        
        $now = Carbon::now();
        $folderName = "upload/qrcode/".$domainId."/".$now->year."/" ;
        $fileName = $post['room_id'].".png" ;
        $filePath =  $folderName.$fileName ;

        $insert = Esticker::find($id);
        $insert->room_id = $post['room_id'] ;
        $insert->year = $post['year'] ;
        $insert->qrcode = url("public/".$filePath) ;
        $insert->save();
       

        EstickerLicensePlate::where('e_sticker_id',$id)->delete();

        foreach ($license as $key => $value) {
            $ins = new EstickerLicensePlate();
            if(is_array($value)){
                $ins->license_plate = $value['license_plate'];
                $ins->license_plate_category = $value['license_plate_category'];
                $ins->province_id = $value['province_id'];
            }else{
                $ins->license_plate = $value->license_plate;
                $ins->license_plate_category = $value->license_plate_category;
                $ins->province_id = $value->province_id;
            }
            $ins->e_sticker_id =  $id;
            $ins->save();
        }

        


        
        $esticker =  Esticker::getData($domainId,$id);

      
      
        $text = "";
        if(!empty($esticker['license_plate_list'])){
            foreach ($esticker['license_plate_list'] as $key => $v) {
                $text .= ",เลขทะเบียนรถ ".$v['license_plate_category']." ".$v['license_plate']." ". $v['province_name'] ;
            }
        }
        $text .=" ปีที่ใช้งาน ".($esticker['year']+543)." วันที่ออกสติกเกอร์ ".date('d m',strtotime($esticker['created_at']))." ".(date('Y',strtotime($esticker['created_at']))+543) ;
        $text = substr($text,1);
    
        $qrcode = $this->generateQRCode($text,$domainId,$post['room_id']);

        $data['e_sticker'] = $esticker;
        $data['e_sticker']['code'] = $qrcode['code'] ;
        return $this->respondWithItem($data);
    }  

    public function destroy(Request $request,$domainId,$id){
        if(!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('admin')){
            return $this->respondWithError(langMessage('คุณไม่สามารถใช้งานส่วนนี้ได้ค่ะ','Not permission'));
        }
        EstickerLicensePlate::where('e_sticker_id',$id)->delete();
        Esticker::find($id)->delete();
        return $this->respondWithItem(['text'=>'delete success']);
    }


    

    private function validator($data)
    {
        return Validator::make($data, [
            'room_id' => 'required|numeric',
            'license_plate_list' => 'required|string',
            'year' => 'required|numeric',
        ]);
    }
    private function validatorItem($data)
    {
        return Validator::make($data, [
            'item' => 'required|string|max:255',
        ]);
    }

    private function generateQRCode($text,$domainId,$roomId){
        $now = Carbon::now();
        $folderName = "upload/qrcode/".$domainId."/".$now->year."/" ;
        $fileName = $roomId .".png" ;
        $filePath =  $folderName.$fileName ;
        if (!is_dir(public_path($folderName))) {
            File::makeDirectory(public_path($folderName),0755,true);  
        }

        $hash = "A(GJ$" ;

        $data['text'] = $text ;
        $encodeHash = base64_encode(substr((md5($hash)), 0,10)) ;
        // $token = substr( base64_encode(md5($hash).".".base64_encode(json_encode($text))), 0,10) ;
        $base64Text = base64_encode(json_encode($text)) ;

        $firstText = substr($base64Text, 0,19);
        $secondText = substr($base64Text, 19);


        $code = base64_encode($encodeHash.base64_encode($firstText.$encodeHash.$secondText)) ;


        $renderer = new \BaconQrCode\Renderer\Image\Png();
        $renderer->setHeight(256);
        $renderer->setWidth(256);



        $writer = new \BaconQrCode\Writer($renderer);
        $writer->writeFile($code, public_path($filePath));

        $data['filePath'] = $filePath ;
        $data['code'] = $code ;
       
        return $data ;
    }

    private function saveImage($domainId,$files)
    {

        try {
            $result = ['result'=>true,'error'=>''];

            foreach($files as $key=>$file){
                
                if(gettype($file)=="array"){
                    $fileData = $file['data'];
                    $fileName = time().'_'.$file['name'];
                    $name = $file['name'];
                }else{
                    $fileData = $file->data ;
                    $fileName = time().'_'.$file->name;
                    $name = $file->name;
                }
                list($mime, $data)   = explode(';', $fileData);
                list(, $data)       = explode(',', $data);
                $data = base64_decode($data);
               
                $folderName = $domainId."/".date('Ym') ;
                if (!is_dir(public_path('storage/'.$folderName))) {
                    File::makeDirectory(public_path('storage/'.$folderName),0755,true);  
                }
                $savePath = public_path('storage/'.$folderName.'/').$fileName;
                file_put_contents($savePath, $data);
                $result['path'][$key] = $folderName;
                $result['filename'][$key] = $fileName;
                $result['name'][$key] = $name;

            }
        }catch (\Exception $e) {
            $result = ['result'=>false,'error'=>$e->getMessage()] ;
        }
        return $result ;
    }
}
