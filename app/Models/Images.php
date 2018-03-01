<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
// use Intervention\Image\Image;

class Images extends Model
{
    protected $table = 'user_images';
     public $timestamps = false;
    protected $fillable = ['id','id_card','domain_id','type', 'path','image','created_at','file_name','file_description','file_code','file_extension'];
    protected $dates = ['created_at'];

    public static function uploadImage($request,$domainId,$saveRealImage=true,$profile=false){
    	$result = ['result'=>true,'error'=>''];
        $post = $request->all();
        if(!isset($post['file_upload'])||$post['file_upload']=="[]"){
           return $result;
        }

        if($saveRealImage){
        	$fileId = self::saveRealImage($request,$domainId);
	        if(!$fileId['result']){
	            return $fileId;
	        }
	        foreach($fileId['fileID'] as $key=>$id){
	            $result['file'][$key]['fileID'] = $id;
	        }
        }
       
       
       
        $files = (gettype($post['file_upload'])=="string") ?  (array)json_decode($post['file_upload']) : $post['file_upload'] ;
        $uploadPath =   ($profile) ? "profile/" :  "upload/"  ;
        foreach($files as $key=>$file){
        	 if(gettype($file)=="string"){
                $f = (array)json_decode($file);
                $sourceData = $f['data'];
                $sourceName = $f['name'];
                $sourceSize = $f['size'];
                
            }elseif(gettype($file)=="array"){
                $sourceData = $file['data'];
                $sourceName = $file['name'];
                $sourceSize = $file['size'];
            }else{
                $sourceData = $file->data;
                $sourceName = $file->name;
                $sourceSize = $file->size;
            }


            list($mime, $data)   = explode(';', $sourceData);
            list(, $data)        = explode(',', $data);
            $data = base64_decode($data);
            $fileName = uniqid().'_'.$sourceName;
            $folderName = date('Ym')."/" ;
            if($profile){
                $fileName = Auth()->user()->id_card.".png";
                $folderName = "" ;
            }
            
            if (!is_dir(public_path($uploadPath.$folderName))) {
                File::makeDirectory(public_path($uploadPath.$folderName),0755,true);  
            }
            $savePath = public_path($uploadPath.$folderName).$fileName;
    
            list(, $mimeType) = explode(':', $mime);
            list($fileType,) = explode('/', $mimeType);



            //--- create Thumbnail 200 x 200
            if($fileType=="image"){
                Image::make($data)->resize(200,200)->save($savePath);
               
            }



            $result['file'][$key]['filePath'] = $folderName ;
            $result['file'][$key]['fileName'] = $fileName ;
            $result['file'][$key]['fileDisplayName'] = $sourceName ;
            $result['file'][$key]['fileExtension'] = $mimeType ;
            $result['file'][$key]['fileSize'] = $sourceSize ;
        }
        return $result;
    }

    private static function saveRealImage($request,$domainId){
        $result = ['result'=>true,'error'=>''];
        
        $post = $request->all();
        $data['file_upload'] = (gettype($post['file_upload'])=="string") ?  (array)json_decode($post['file_upload']) : $post['file_upload'] ;

        $url = env('APP_URL_SAVE_IMAGE')."/api/file?api_token=33ae2f309f127ec78e051ba3075602fc"  ; 
       

        $client = new \GuzzleHttp\Client();

        $response = $client->post($url,['form_params'=>$data]); 
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            $result['result'] = false;
            $result['errors'] = $json['errors'];
            return $result ;
        }


        $result['fileID'] =  $json['response']['file_id'] ;

        return $result;
    }


    public static function deleteRealImage($fileId){
        $result = ['result'=>true,'error'=>''];
        $url = env('APP_URL_SAVE_IMAGE')."/api/file/".$fileId."?api_token=33ae2f309f127ec78e051ba3075602fc"  ; 
        $client = new \GuzzleHttp\Client();
        $response = $client->delete($url); 
        $json = json_decode($response->getBody()->getContents(),true); 
        if(!isset($json['result'])){
            return $response->getBody()->getContents() ;
        }
        if($json['result']=="false")
        {
            $result['result'] = false;
            $result['errors'] = $json['errors'];
            return $result ;
        }
        return $result;
    }

    public static function upload($imageData,$resize=null){
        $result = ['result'=>true,'error'=>''];

        $files = (gettype($imageData)=="string") ?  (array)json_decode($imageData) : $imageData ;
        $uploadPath = "upload/"  ;
        foreach($files as $key=>$file){
 
            if(gettype($file)=="string"){
                $f = (array)json_decode($file);
                $sourceData = $f['data'];
                $sourceName = $f['name'];
                
            }elseif(gettype($file)=="array"){
                $sourceData = $file['data'];
                $sourceName = $file['name'];
            }else{
                $sourceData = $file->data;
                $sourceName = $file->name;
            }
            list($mime, $data)   = explode(';', $sourceData);
            list(, $data)        = explode(',', $data);
            $data = base64_decode($data);
            $fileName = uniqid().'_'.$sourceName;
            $folderName = date('Ym') ;
           
            
            if (!is_dir(public_path($uploadPath.$folderName))) {
                File::makeDirectory(public_path($uploadPath.$folderName),0755,true);  
            }
            $savePath = public_path($uploadPath.$folderName.'/').$fileName;
    
            list(, $mimeType) = explode(':', $mime);
            list($fileType,) = explode('/', $mimeType);

            //--- create Thumbnail 200 x 200
            if($fileType=="image"){
                if(isset($resize)){
                    Image::make($data)->resize($resize['W'],$resize['H'])->save($savePath);
                }else{
                    Image::make($data)->save($savePath);
                }
                
            }else{
            
                file_put_contents($savePath, $data);
            }
            $result['file'][$key]['filePath'] = $folderName ;
            $result['file'][$key]['fileName'] = $fileName ;
            $result['file'][$key]['fileDisplayName'] = $sourceName ;
            $result['file'][$key]['fileExtension'] = $mimeType ;
        }
        return $result;
    }
}
