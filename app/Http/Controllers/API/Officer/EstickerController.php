<?php

namespace App\Http\Controllers\API\Officer;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Esticker\Esticker;
use App\Models\Esticker\EstickerLicensePlate;
use App\Models\Esticker\EstickerPrintLog;
use App\Models\Images;
use App\Models\Notification;
use App\Models\Province;
use App\Models\Setting;
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

    public function logPrint(Request $request, $domainId, $id)
    {
        $post = $request->except('api_token', '_method');
        $userId= Auth()->user()->id;
        $insert = new EstickerPrintLog();
        $insert->e_sticker_id = $id ;
        $insert->created_at = Carbon::now() ;
        $insert->created_by = $userId ;
        $insert->request_name = $post['user_name'] ;
        $insert->request_tel = $post['user_tel'] ;
        $insert->request_type = $post['reason'] ;
        $insert->request_remark = $post['remark'] ;
        $insert->save();
        return $this->respondWithItem(['text'=>'success','print_id'=>$insert->id , 'e_sticker'=> ['id'=>$id]  ]);
    }

    public function index($domainId)
    {
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
        $data['e_stickers'] = Esticker::getListData($domainId);
     
        return $this->respondWithItem($data);
    }
    

    public function search(Request $request, $domainId, $roomId)
    {
        $carNumber = $request->input('car_number');
        $searchQuery = " AND e.room_id =$roomId AND e.car_number=$carNumber " ;
        $query = Esticker::getData($domainId, null, $searchQuery);
        $data['e_sticker'] = $query ;
        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId)
    {
        $userId = Auth::user()->id ;
        $post = $request->except('api_token', '_method');

        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }
        $license = (gettype($post['license_plate_list'])=="string") ? json_decode($post['license_plate_list']) : $post['license_plate_list'] ;
       
        
        $now = Carbon::now();
        $folderName = "upload/qrcode/".$domainId."/".$now->year."/" ;
        $fileName = $post['room_id'].".png" ;
        $filePath =  $folderName.$fileName ;


        $insert = Esticker::where('room_id', $post['room_id'])
        ->where('car_number', $post['car_number'])
        ->first();

        $caseUpdate = false;

        if (empty($insert)) {
            $caseUpdate = true;
           
            $insert = new Esticker();
            $insert->no = Esticker::GetID($domainId) ;
            $insert->created_at = $now ;
            $insert->car_number = $post['car_number'] ;
            $insert->created_by = Auth()->user()->id ;
        }

        $insert->room_id = $post['room_id'] ;
        $insert->domain_id = $domainId ;
        $insert->year = $post['year'] ;
      
        $insert->qrcode = url("public/".$filePath) ;
       
        $insert->save();
        $id = $insert->id ;

        $insert = new EstickerPrintLog();
        $insert->e_sticker_id = $id ;
        $insert->created_at = Carbon::now() ;
        $insert->created_by = $userId ;
        $insert->request_name = $post['user_name'] ;
        $insert->request_tel = $post['user_tel'] ;
        $insert->request_type = $post['reason'] ;
        $insert->request_remark = isset($post['remark']) ? $post['remark'] : null ;
        $insert->save();
        $printId = $insert->id ;


        if ($caseUpdate) {
            EstickerLicensePlate::where('e_sticker_id', $id)->delete();
        }

        foreach ($license as $key => $value) {
            $ins = new EstickerLicensePlate();
            if (is_array($value)) {
                $ins->license_plate = $value['license_plate'];
                $ins->license_plate_category = $value['license_plate_category'];
                $ins->province_id = $value['province_id'];
                $ins->car_owner_name = $value['car_owner_name'];
                $ins->car_owner_tel = $value['car_owner_tel'];
            } else {
                $ins->license_plate = $value->license_plate;
                $ins->license_plate_category = $value->license_plate_category;
                $ins->province_id = $value->province_id;
                $ins->car_owner_name = $value->car_owner_name;
                $ins->car_owner_tel = $value->car_owner_tel;
            }
            $ins->e_sticker_id =  $id;
            $ins->save();
        }

        


        
        $esticker =  Esticker::getData($domainId, $id);

      
      
       
        $qrcode = $this->generateQRCode($esticker, $domainId, $post['room_id']);
        $data['print_id'] = $printId;
        $data['e_sticker'] = $esticker;
        $data['e_sticker']['code'] = $qrcode['code'] ;
       
        return $this->respondWithItem($data);
    }

    public function edit(Request $request, $domainId, $id)
    {
        $query = Esticker::getData($domainId, $id);
        $data['e_sticker'] = $query ;
        return $this->respondWithItem($data);
    }

    public function show(Request $request, $domainId, $id)
    {
        $query = Esticker::getData($domainId, $id);
        $data['e_sticker'] = $query ;
        return $this->respondWithItem($data);
    }

    public function printView(Request $request, $domainId, $id)
    {
        $sql = "SELECT elp.*,u.first_name,u.last_name
                FROM e_sticker_print_log as elp
                JOIN users as u ON u.id=elp.created_by
                WHERE elp.id = $id
                ";
        $print = collect(DB::select(DB::raw($sql)))->first();
        $query = Esticker::getData($domainId, $print->e_sticker_id);



        $data['e_sticker'] = $query ;
        $data['e_sticker']['created_sticker_first_name'] = $print->first_name ;
        $data['e_sticker']['created_sticker_last_name'] = $print->last_name ;
        $data['e_sticker']['logo_sticker'] = Setting::getVal($domainId, 'LOGO_STICKER');
        $data['e_sticker']['name_sticker'] = Setting::getVal($domainId, 'NAME_STICKER');
        return $this->respondWithItem($data);
    }

    public function update(Request $request, $domainId, $id)
    {
        $userId = Auth::user()->id ;
        $post = $request->except('api_token', '_method');
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

        //--- ปีไม่ตรง เท่ากับว่า สร้างใหม่
        if ($insert->year != $post['year']) {
            $now = Carbon::now();
            $folderName = "upload/qrcode/".$domainId."/".$now->year."/" ;
            $fileName = $post['room_id'].".png" ;
            $filePath =  $folderName.$fileName ;

            $insert = new Esticker();
            $insert->room_id = $post['room_id'] ;
            $insert->domain_id = $domainId ;
            $insert->year = $post['year'] ;
            $insert->created_at = $now ;
            $insert->car_number = $post['car_number'] ;
            $insert->created_by = Auth()->user()->id ;
            $insert->qrcode = url("public/".$filePath) ;
            $insert->no = Esticker::GetID($domainId) ;
            $insert->save();
            $id = $insert->id ;
        }

       
       

        EstickerLicensePlate::where('e_sticker_id', $id)->delete();

        foreach ($license as $key => $value) {
            $ins = new EstickerLicensePlate();
            if (is_array($value)) {
                $ins->license_plate = $value['license_plate'];
                $ins->license_plate_category = $value['license_plate_category'];
                $ins->province_id = $value['province_id'];
                $ins->car_owner_name = $value['car_owner_name'];
                $ins->car_owner_tel = $value['car_owner_tel'];
            } else {
                $ins->license_plate = $value->license_plate;
                $ins->license_plate_category = $value->license_plate_category;
                $ins->province_id = $value->province_id;
                $ins->car_owner_name = $value->car_owner_name;
                $ins->car_owner_tel = $value->car_owner_tel;
            }
            $ins->e_sticker_id =  $id;
            $ins->save();
        }

      

        $insert = new EstickerPrintLog();
        $insert->e_sticker_id = $id ;
        $insert->created_at = Carbon::now() ;
        $insert->created_by = $userId ;
        $insert->request_name = $post['user_name'] ;
        $insert->request_tel = $post['user_tel'] ;
        $insert->request_type = $post['reason'] ;
        $insert->request_remark = isset($post['remark']) ? $post['remark'] : null ;
        $insert->save();
        $printId = $insert->id ;

        

        $esticker =  Esticker::getData($domainId, $id);

      
      
        $qrcode = $this->generateQRCode($esticker, $domainId, $post['room_id']);

        $data['print_id'] = $printId;
        $data['e_sticker'] = $esticker;
        $data['e_sticker']['code'] = $qrcode['code'] ;
        return $this->respondWithItem($data);
    }

    public function destroy(Request $request, $domainId, $id)
    {
        if (!Auth()->user()->hasRole('officer')&&!Auth()->user()->hasRole('admin')) {
            return $this->respondWithError(langMessage('คุณไม่สามารถใช้งานส่วนนี้ได้ค่ะ', 'Not permission'));
        }
        EstickerLicensePlate::where('e_sticker_id', $id)->delete();
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

    private function generateQRCode($esticker, $domainId, $roomId)
    {

        $text = $esticker['no']."|";
        $textlicense = "";
        $textOwnerName = "";
        $textOwnerTel = "";
        if (!empty($esticker['license_plate_list'])) {
            foreach ($esticker['license_plate_list'] as $key => $v) {
                $textlicense .= ",".$v['license_plate_category']." ".$v['license_plate']." ". $v['province_name'] ;

                $textOwnerName .= ",".$v['car_owner_name'];
                $textOwnerTel .= ",".$v['car_owner_tel'];
            }
            $textlicense = substr($textlicense, 1);
            $textOwnerName = substr($textOwnerName, 1);
            $textOwnerTel = substr($textOwnerTel, 1);
        }
       
        $text .=$textlicense."|".($esticker['year'])."|".$esticker['room_name']."|".$textOwnerName."|".$textOwnerTel;

        $now = Carbon::now();
        $folderName = "upload/qrcode/".$domainId."/".$now->year."/" ;
        $fileName = $roomId .".png" ;
        $filePath =  $folderName.$fileName ;
        if (!is_dir(public_path($folderName))) {
            File::makeDirectory(public_path($folderName), 0755, true);
        }

        $encrypt = setting::getVal($domainId, 'KEY_ENCRYPT') ;
        $hash = setting::getVal($domainId, 'KEY_HASH') ;

        $data['text'] = $text ;
        $textHash = $text."|".$hash ;
        $hashTxt =  substr(hash('sha256', $textHash), 0, 10) ;

        $code = $this->qrcodeEncrypt($text."|".$hashTxt, $encrypt);

        
        // $encodeHash = base64_encode(substr((md5($hash)), 0,10)) ;
        // // $token = substr( base64_encode(md5($hash).".".base64_encode(json_encode($text))), 0,10) ;
        // $base64Text = base64_encode(json_encode($text)) ;

        // $firstText = substr($base64Text, 0,19);
        // $secondText = substr($base64Text, 19);


        // $code = base64_encode($encodeHash.base64_encode($firstText.$encodeHash.$secondText)) ;


        $renderer = new \BaconQrCode\Renderer\Image\Png();
        $renderer->setHeight(256);
        $renderer->setWidth(256);



        $writer = new \BaconQrCode\Writer($renderer);
        $writer->writeFile($code, public_path($filePath));

        $data['filePath'] = $filePath ;
        $data['code'] = $code ;
       
        return $data ;
    }

    private function qrcodeEncrypt($data, $secret)
    {
   
   
    //Generate a key from a hash
    // $key = md5(utf8_encode($secret), true);

    // //Take first 8 bytes of $key and append them to the end of $key.
    // $key .= substr($key, 0, 8);

    //Pad for PKCS7
        $blockSize = mcrypt_get_block_size('tripledes', 'ecb');
        $len = strlen($data);
        $pad = $blockSize - ($len % $blockSize);
        $data .= str_repeat(chr($pad), $pad);
   
   
    //Encrypt data
        $encData = mcrypt_encrypt('tripledes', $secret, $data, 'ecb');

        return base64_encode($encData);
    }

    private function saveImage($domainId, $files)
    {

        try {
            $result = ['result'=>true,'error'=>''];
            if (!Images::validateImage($files)) {
                return ['result'=>false,'error'=> getLang()=='en' ? 'file size over than 500kb' : 'ไม่สามารถอัพไฟล์ขนาดเกิน 500kb' ];
            }
            foreach ($files as $key => $file) {
                if (gettype($file)=="array") {
                    $fileData = $file['data'];
                    $fileName = time().'_'.$file['name'];
                    $name = $file['name'];
                } else {
                    $fileData = $file->data ;
                    $fileName = time().'_'.$file->name;
                    $name = $file->name;
                }
                list($mime, $data)   = explode(';', $fileData);
                list(, $data)       = explode(',', $data);
                $data = base64_decode($data);
               
                $folderName = $domainId."/".date('Ym') ;
                if (!is_dir(public_path('storage/'.$folderName))) {
                    File::makeDirectory(public_path('storage/'.$folderName), 0755, true);
                }
                $savePath = public_path('storage/'.$folderName.'/').$fileName;
                file_put_contents($savePath, $data);
                $result['path'][$key] = $folderName;
                $result['filename'][$key] = $fileName;
                $result['name'][$key] = $name;
            }
        } catch (\Exception $e) {
            $result = ['result'=>false,'error'=>$e->getMessage()] ;
        }
        return $result ;
    }
}
