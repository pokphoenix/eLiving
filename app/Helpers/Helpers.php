<?php

use Carbon\Carbon;

if (! function_exists('created_date_format')) {
    function created_date_format($date)
    {
        return date('Y-m-d H:i', strtotime($date))  ;
    }
}
if (! function_exists('getLang')) {
    function getLang()
    {

        $user = Auth()->User();
        if (isset($user)) {
            $lang = Auth()->User()->current_lang ;
        } else {
            $lang = App::getLocale();
        }

        // var_dump(Auth()->user()->getDomainName()) ;die;
        return trim($lang) ;
        // return Auth::user()->current_lang;
        // return App::isLocale('en');
    }
}

if (! function_exists('cutStrlen')) {
    function cutStrlen($text, $len)
    {
        if (mb_strlen($text, 'utf-8') > $len) {
            $text = iconv_substr($text, 0, $len, "UTF-8")."..." ;
        }
        return $text ;
    }
}



if (! function_exists('getChannelTypeName')) {
    function getChannelTypeName($type)
    {
        switch ($type) {
            case 1:
                $text = "Public";
                break;
            case 2:
                $text = "Close";
                break;
            case 3:
                $text = "Secret";
                break;
        }


        return $text ;
    }
}
if (! function_exists('getNotificationType')) {
    function getNotificationType($type)
    {
        switch ($type) {
            case 1:
                $text = "red";
                break;
            case 2:
                $text = "yellow";
                break;
            case 3:
                $text = "green";
                break;
            case 4:
                $text = "aqua";
                break;
            default:
                $text = "aqua";
                break;
        }


        return $text ;
    }
}
if (! function_exists('getNotificationIcon')) {
    function getNotificationIcon($type)
    {
        
        switch ($type) {
            case 1:
                $text = "fa-user";
                break;
            case 2:
                $text = "fa-flag-o";
                break;
            case 3:
                $text = "fa-commenting-o";
                break;
            case 4:
                $text = "fa-dollar";
                break;
            default:
                $text = "fa-circle-o";
                break;
        }


        return $text ;
    }
}
if (! function_exists('month_date')) {
    function month_date($month)
    {
        
        $lang = getLang();

        switch (intval($month)) {
            case 1:
                $txt = $lang=='en' ? 'January' : 'มกราคม' ;
                break;
            case 2:
                $txt = $lang=='en' ? 'February' : 'กุมภาพันธ์' ;
                break;
            case 3:
                $txt = $lang=='en' ? 'March' : 'มีนาคม' ;
                break;
            case 4:
                $txt = $lang=='en' ? 'April' : 'เมษายน' ;
                break;
            case 5:
                $txt = $lang=='en' ?  'May' :'พฤษภาคม' ;
                break;
            case 6:
                $txt = $lang=='en' ? 'June' : 'มิถุนายน' ;
                break;
            case 7:
                $txt = $lang=='en' ? 'July' : 'กรกฎาคม' ;
                break;
            case 8:
                $txt = $lang=='en' ? 'August' : 'สิงหาคม' ;
                break;
            case 9:
                $txt = $lang=='en' ? 'September' : 'กันยายน' ;
                break;
            case 10:
                $txt = $lang=='en' ? 'October' : 'ตุลาคม' ;
                break;
            case 11:
                $txt = $lang=='en' ? 'November' : 'พฤษจิกายน' ;
                break;
            case 12:
                $txt = $lang=='en' ? 'December' : 'ธันวาคม' ;
                break;
        }

        return $txt ;
    }
}
if (! function_exists('month_date_short')) {
    function month_date_short($month)
    {
        $lang = getLang();
        switch (intval($month)) {
            case 1:
                $txt = $lang=='en' ? 'Jan' : 'ม.ค.' ;
                break;
            case 2:
                $txt = $lang=='en' ? 'Feb' : 'ก.พ.' ;
                break;
            case 3:
                $txt = $lang=='en' ? 'Mar' : 'มี.ค.' ;
                break;
            case 4:
                $txt = $lang=='en' ? 'Apr' : 'เม.ษ.' ;
                break;
            case 5:
                $txt = $lang=='en' ?  'May' :'พ.ค.' ;
                break;
            case 6:
                $txt = $lang=='en' ? 'Jun' : 'มิ.ย.' ;
                break;
            case 7:
                $txt = $lang=='en' ? 'Jul' : 'ก.ค.' ;
                break;
            case 8:
                $txt = $lang=='en' ? 'Aug' : 'ส.ค.' ;
                break;
            case 9:
                $txt = $lang=='en' ? 'Sep' : 'ก.ย.' ;
                break;
            case 10:
                $txt = $lang=='en' ? 'Oct' : 'ต.ค.' ;
                break;
            case 11:
                $txt = $lang=='en' ? 'Nov' : 'พ.ย.' ;
                break;
            case 12:
                $txt = $lang=='en' ? 'Dec' : 'ธ.ค.' ;
                break;
        }

        return $txt ;
    }
}
if (! function_exists('getNotificationUrl')) {
    function getNotificationUrl($data)
    {
        $dataType = (gettype($data)=="array") ? $data['type'] : $data->type ;
        $dataRefId = (gettype($data)=="array") ? $data['ref_id'] : $data->ref_id ;

        switch ($dataType) {
            case 0:
                $url = url(Auth()->user()->getDomainName().'/create-user/'.$dataRefId.'/edit') ;
                break;
            case 1:
                $url = url('/profile/show') ;
                break;
            case 2:
                $url =  (Auth()->user()->hasRole('user')) ? null :  url(Auth()->user()->getDomainName().'/task/'.$dataRefId) ;
                break;
            case 3:
                $url = url(Auth()->user()->getDomainName().'/channel/'.$dataRefId) ;
                break;
            case 4:
                $url = url(Auth()->user()->getDomainName().'/purchase/quotation/'.$dataRefId) ;
                break;
            default:
                $url = url('');
                break;
        }


        return $url ;
    }
}

if (! function_exists('getStatusText')) {
    function getStatusText($type, $lang = 'EN')
    {
        switch ($type) {
            case 0:
                $text = "Wait for Approve";
                $textTH = "รอตรวจสอบ";
                break;
            case 1:
                $text = "Approved";
                $textTH = "ยืนยันแล้ว";
                break;
            case 2:
                $text = "Reviewed";
                $textTH = "แอดมินขอข้อมูลเพิ่ม";
                break;
            case 3:
                $text = "Re submit";
                $textTH = "รอตรวจสอบอีกครั้ง";
                break;
            case 4:
                $text = "Baned";
                $textTH = "ถูกแบน";
                break;
            case 5:
                $text = "Wait For Active Email";
                $textTH = "รอการยืนยันอีเมล์";
                break;
        }
        return  ($lang=='EN') ? $text : $textTH ;
    }
}

if (! function_exists('getStatusColor')) {
    function getStatusColor($type)
    {
        switch ($type) {
            case 0:
                $color = "#f39c12";
                break;
            case 1:
                $color = "#00a65a";
                break;
            case 2:
                $color = "#3c8dbc";
                break;
            case 3:
                $color = "#f56954";
                break;
            case 4:
                $color = "#dd4b39";
                break;
            case 5:
                $color = "#00a65a";
                break;
        }
        return  $color ;
    }
}

if (! function_exists('strDotted')) {
    function strDotted($text, $length = 10)
    {
        return  (strlen($text) > $length ) ? substr($text, 0, $length)."..." :  $text ;
    }
}

if (! function_exists('diffByNow')) {
    function diffByNow($date)
    {
        $carbonated_date = Carbon::createFromTimestamp($date);
        return $carbonated_date->diffForHumans(Carbon::now());
    }
}

if (! function_exists('labelClass')) {
    function labelClass($task)
    {
        if (isset($task['due_date_complete_at']) &&  strtotime($task['due_date_complete_at'])<=strtotime($task['due_dated_at'])) {
            $res = "label-success" ;
        } elseif (strtotime($task['due_dated_at'])<=time()) {
            $res = "label-danger" ;
        } else {
            $res = "label-warning" ;
        }
        return $res ;
    }
}

if (! function_exists('dueDateTime')) {
    function dueDateTime($task)
    {
        return date('d/m/Y h:i ', strtotime($task['due_dated_at']));
    }
}


if (! function_exists('getUserName')) {
    function getUserName()
    {
        return Auth::user()->first_name." ".Auth::user()->last_name  ;
    }
}

if (! function_exists('getQRCode')) {
    function getQRCode($hash = null)
    {
        if (is_null($hash)) {
            $hash = time()."".str_random(10);
        }
        $base64Hash = base64_encode($hash);
        $return = substr($hash, 0, 20);
        // echo " hash :  $return<BR>";

        $renderer = new \BaconQrCode\Renderer\Image\Png();
        $renderer->setHeight(256);
        $renderer->setWidth(256);
        $writer = new \BaconQrCode\Writer($renderer);
        $writer->writeString($return);
        $fileName = "temp.png";
        $writer->writeFile($return, $fileName);
            
        $img = Image::make($fileName);
        $mime = $img->mime() ;
        $img->encode('png');
        // var_dump($mime);
        // $type = 'png';
        $base64 = 'data:' . $mime . ';base64,' . base64_encode($img);
        unset($fileName);
        return $base64 ;
    }
}




if (! function_exists('getBase64Img')) {
    function getBase64Img($image)
    {
        $defaultImg =  'data:image/png;base64,' . base64_encode(Image::make(url('/public/img/error-image.jpg'))->encode('png'));

        if (is_null($image)) {
            return  null ;
        }
        //--- check file exits
        $file_headers = @get_headers(htmlspecialchars($image));
        if ($file_headers[0]!="HTTP/1.1 200 OK") {
            return  $defaultImg ;
        }

        // $img = Image::make($image)->mime();
        // $mime = $img->mime() ;
        $type = pathinfo($image, PATHINFO_EXTENSION);
        if (strtolower($type)=="jpg"|| strtolower($type)=="png") {
            $image = $image ;
        } elseif ($type=="txt") {
            $image = url('/public/img/file_format/txt.png') ;
        } elseif ($type=="pdf") {
            $image = url('/public/img/file_format/pdf.png') ;
        } elseif ($type=="xls") {
            $image = url('/public/img/file_format/xls.png') ;
        } else {
            $image = url('/public/img/file_format/etc.png') ;
        }

        return $image;
        // $type = Image::make($image)->mime();
        // $type = mime_content_type($image);
        //   var_dump($type);
        // $data = file_get_contents($image);
        // $base64 = 'data:image/'.$type.';base64,' . base64_encode($data);
        // return $base64  ;


        // $img = Image::make($image);
        // $mime = $img->mime() ;
        // if($mime=="image/png"){
        //     $img->encode('png');
        // }elseif($mime=="image/jpeg"){
        //     $img->encode('jpg');
        // }
        // // var_dump($mime);
        // // $type = 'png';
        // $base64 = 'data:' . $mime . ';base64,' . base64_encode($img);
    }
}

if (! function_exists('uploadfile')) {
    function uploadfile($request, $name, $domainId, $resize = null)
    {
        $result = ['result'=>true,'error'=>''];
        if ($request->hasFile($name)) {
            $file = $request->file($name);
            if (is_array($file)) {
                foreach ($request->file($name) as $key => $file) {
                    $fileArray = array('image' => $file);
                    $rules = array(
                      // 'image' => 'mimes:jpeg,jpg,png|max:2048' // max 10000kb
                      'image' => 'max:2048' // max 10000kb
                    );
                    $validator = Validator::make($fileArray, $rules);
                    if ($validator->fails()) {
                        $result['result'] = false;
                        $result['error'] = $validator->errors()->getMessages() ;
                    } else {
                        $folderName = UPLOAD_PATH.$domainId."/".date('Ym') ;
                        $fileName = uniqid().'_'.$file->getClientOriginalName();
                        if (!is_dir(public_path('storage/'.$folderName))) {
                            File::makeDirectory(public_path('storage/'.$folderName), 0777, true);
                        }
                        $type = pathinfo($file, PATHINFO_EXTENSION);
                        $destinationPath = public_path('storage/'.$folderName);
                        if ($type=="jpg"||$type=="png") {
                            $image_resize = Image::make($file->getRealPath());
                            if (isset($resize)) {
                                $image_resize->resize($resize['w'], $resize['h']);
                            }
                            $image_resize->save($destinationPath.'/'.$fileName);
                        } else {
                            $file->move($destinationPath, $fileName);
                        }
                        $imagePath = str_replace(UPLOAD_PATH, '', $folderName.'/') ;
                        $result['imagePath'][$key] = $imagePath ;
                        $result['imageName'][$key] = $fileName ;
                    }
                }
            } else {
                $fileArray = array('image' => $file);
                $rules = array(
                  'image' => 'max:2048' // max 10000kb
                );
                $validator = Validator::make($fileArray, $rules);
                if ($validator->fails()) {
                    $result['result'] = false;
                    $result['error'] = $validator->errors()->getMessages() ;
                } else {
                    $folderName = UPLOAD_PATH.$domainId."/".date('Ym') ;
                    $fileName = uniqid().'_'.$request->$name->getClientOriginalName();
                    if (!is_dir(public_path('storage/'.$folderName))) {
                        File::makeDirectory(public_path('storage/'.$folderName), 0777, true);
                    }

                    $type = pathinfo($file, PATHINFO_EXTENSION);
                    $destinationPath = public_path('storage/'.$folderName);
                    if ($type=="jpg"||$type=="png") {
                        $image_resize = Image::make($file->getRealPath());
                        if (isset($resize)) {
                            $image_resize->resize($resize['w'], $resize['h']);
                        }
                        $image_resize->save($destinationPath.'/'.$fileName);
                    } else {
                        $file->move($destinationPath, $fileName);
                    }
                    $imagePath = str_replace(UPLOAD_PATH, '', $folderName.'/') ;
                    $result['imagePath'] = $imagePath ;
                    $result['imageName'] = $fileName ;
                }
            }
        }
        return $result;
    }
}
