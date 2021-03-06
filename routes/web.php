<?php

use Illuminate\Support\Facades\Input;
use Intervention\Image\Image;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['prefix'=>'{domain}','middleware'=>['webserver','auth','domain']], function () {


    Route::resource('/pea', 'Main\PeaController');

    Route::get('/create-user/{id_card}/approve', 'Admin\CreateUserController@approve');

    Route::get('/remove-user', 'Admin\RemoveUserController@index');
    
    Route::group(['prefix'=>'/master','namespace'=>'Master'], function () {
        Route::resource('/contact', 'ContactController') ;
        Route::resource('/title-names', 'TitleNameController') ;
        Route::resource('/debt-types', 'DebtTypeController') ;
        Route::resource('/esticker-reason', 'EstickerReasonController') ;
        Route::resource('/parcel-type', 'ParcelTypeController') ;
        Route::resource('/supply-type', 'SupplyTypeController');
        Route::resource('/work-area-type', 'WorkAreaTypeController');
        Route::resource('/work-job-type', 'WorkJobTypeController');
        Route::resource('/work-pioritize', 'WorkPioritizeController');
        Route::resource('/work-system-type', 'WorkSystemTypeController');
        Route::resource('/word-blacklist', 'WordBlacklistController');
        Route::resource('/word-whitelist', 'WordWhitelistController');
        Route::resource('/pioritize', 'PioritizeController') ;
        Route::resource('/suggest-category', 'SuggestCategoryController') ;
        Route::resource('channel-type', 'ChannelTypeController');
        Route::resource('task-category', 'TaskCategoryController');
    });




    Route::resource('/phone', 'Officer\PhoneDirectoryController');
    Route::resource('/contact', 'Main\ContactController');
    
    Route::resource('/pre-welcome', 'Admin\PreWelcomeController');

    Route::resource('/create-admin', 'Admin\CreateAdminController');

    Route::resource('/create-user', 'Admin\CreateUserController');
    Route::resource('/wait-user', 'Admin\WaitUserController');
    Route::resource('/request-room', 'Admin\RequestRoomController');

    
    Route::get('/purchase/quotation/{quotation_id}/print', 'Officer\Purchase\QuatationController@printQuotation');
    Route::get('/purchase/quotation/print-preview', 'Officer\Purchase\QuatationController@printPreview');

    Route::get('quotation-setting', 'Officer\Purchase\QuatationController@settingGet');
    Route::get('quotation-vote-setting', 'Officer\Purchase\QuatationController@voteSettingGet');
    


    Route::resource('/log-activity', 'Admin\LogActivityController');

    Route::resource('/purchase/quotation', 'Officer\Purchase\QuatationController');
    
    Route::resource('resolution', 'Officer\ResolutionController');

    Route::get('routine/view', 'Officer\RoutineController@view');
    Route::resource('routine', 'Officer\RoutineController');

    Route::get('/dashboard', 'Main\DashboardController@index')->name('dashboard.index');

    Route::get('/wait-approve', 'Main\DashboardController@waitApprove');


    Route::resource('task', 'Main\TaskController');
    Route::get('/channel/{channel_id}/member', 'Main\ChannelController@member');
    Route::get('/channel/contact', 'Main\ChannelController@contact');
    Route::get('/channel/blacklist', 'Main\ChannelController@blacklist');
    Route::resource('channel', 'Main\ChannelController');

    Route::resource('rooms', 'User\RoomController');
    
    Route::resource('user/suggest/system', 'User\SuggestController');
    Route::resource('suggest/system', 'Admin\SuggestController');


    Route::get('officer/work/{id}/print', 'Officer\WorkController@printView');

    Route::resource('work/{room_id}/user', 'User\WorkController');
    Route::resource('officer/work', 'Officer\WorkController');

    Route::resource('user/{room_id}/task', 'User\TaskController');
    Route::resource('officer/task', 'Officer\TaskController');

    Route::resource('guard/parking-in', 'Parking\GuardInController');
    Route::resource('parking/cancel', 'Parking\CancelController');
    Route::resource('parking/guard', 'Parking\GuardController');
    Route::resource('parking/package', 'Parking\PackageController');
    Route::resource('parking/buy', 'Parking\BuyController');
    Route::resource('parking/report', 'Parking\ReportController');
    Route::resource('parking/debt', 'Parking\DebtController');
    Route::resource('manual/parking/in', 'Parking\ManualInController');
    Route::resource('manual/parking/out', 'Parking\ManualOutController');

    Route::resource('parking/{room_id}/use', 'Parking\UseController');
    
    Route::resource('parcel/{room_id}/user', 'Parcel\UserController');

    Route::resource('parcel/print-list', 'Parcel\PrintController');
    Route::get('parcel/print-gift', 'Parcel\PrintController@getGift');
    Route::get('parcel/print-gift/view', 'Parcel\PrintController@getGiftView');
    Route::get('parcel/print-mail', 'Parcel\PrintController@getMail');
    Route::get('parcel/print-mail/view', 'Parcel\PrintController@getMailView');
    Route::resource('parcel/officer', 'Parcel\OfficerController');

    Route::resource('parcel/backdate', 'Parcel\BackDateController');
    


    Route::resource('post', 'Main\PostController');
    Route::resource('notice', 'Officer\NoticeController');


    Route::get('e-sticker/{id}/print', 'Officer\EstickerController@printView');
    Route::get('report-e-sticker', 'Officer\EstickerController@report');


    Route::resource('e-sticker', 'Officer\EstickerController');


    Route::group(['prefix'=>'/backend','namespace'=>'Backend'], function () {
        Route::get('/move-server', 'MoveServerController@index') ;
        Route::put('/move-server', 'MoveServerController@update') ;
    });

    Route::get('/bill', 'Bill\BillController@create');
	Route::get('/important_day', 'Officer\ImportantDayController@index');
    Route::group(['prefix'=>'/setting','namespace'=>'Setting'], function () {
        Route::get('/domain', 'DomainController@index') ;
        Route::get('/officer', 'OfficerController@index') ;
    });
});




Route::group(['middleware'=>['webserver','web']], function () {
    // Route::get('/', function () {
    //     return view('front.redirect');
    // })->name('home');
    // Route::get('/', function () {   //--- index page
    //     return view('front.home');
    // })->name('home');
    Route::get('/', 'User\LoginController@show');

    Route::get('/profile/changepass', 'User\ProfileController@changepass');
    Route::get('/resetpassword', 'User\RegistorController@resetPassword');

    Route::get('/active', 'User\RegistorController@activeCode');

    Route::get('/profile/room', 'User\ProfileController@room');
    Route::get('/profile/username', 'User\ProfileController@username');
    Route::get('/profile', 'User\ProfileController@show');
    Route::get('/profile/edit', 'User\ProfileController@edit');
    Route::resource('/profile/address', 'User\AddressController');
    Route::resource('/profile/attach', 'User\AttachmentController');

    Route::group(['prefix'=>'/profile','namespace'=>'User'], function () {
        Route::get('/show', 'ProfileController@show') ;
        Route::get('/edit', 'ProfileController@edit') ;
        Route::put('/update', 'ProfileController@update') ;
    });

    Route::get('/signup', function () {
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/province' ;
        $res =  $client->post($url, ['form_params'=>['name'=>'']]);
        $json = json_decode($res->getBody()->getContents(), true);
        $province = $json ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/search/domain' ;
        $res =  $client->get($url);
        $json = json_decode($res->getBody()->getContents(), true);
        $domains = $json['response']['domain'] ;

        $domainId = 1 ;
        return view('front.signup', compact('province', 'domainId', 'domains'));
    });
    Route::post('/signup', 'User\RegistorController@signup')->name('signup');
    Route::get('/signup_facebook', 'User\RegistorController@facebook');
    Route::post('/signup_facebook', 'User\RegistorController@facebookSignUp');
    Route::post('/signin_facebook', 'User\LoginController@facebookSignIn');

    Route::get('/signin', 'User\LoginController@show')->name('signin.show');
    Route::get('/main', 'User\LoginController@main');
    Route::post('/signin', 'User\LoginController@signin')->name('signin');

    Route::get('/domain', 'User\DomainController@index')->name('domain.index');
    Route::get('/domain/list', 'User\DomainController@listDomain')->name('domain.list');
    Route::get('/domain/join', 'User\DomainController@join')->name('domain.join');
    Route::post('/domain/join', 'User\DomainController@joinStore')->name('domain.joinstore');
    Route::get('/domain/create', 'User\DomainController@create')->name('domain.create');
    Route::post('/domain', 'User\DomainController@store')->name('domain.store');
    Route::post('/domain/search', 'User\DomainController@search')->name('domain.search');

    


    // Route::get('/backend', function () {
    //     return view('backend.layouts.app');
    // });

    Route::resource('niti', 'Main\NitiController');
    Auth::routes();

    // Route::get('/home', 'HomeController@index')->name('home');

    // Route::resource('admin', 'Main\AdminController');
});




Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('config:clear');
});

Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
});

Route::get('/cache-clear', function () {
    $exitCode = Artisan::call('cache:clear');
});


Route::get('/logout', function () {
    // var_dump("expression");die;
    // User::find(Auth()->user()->id)->update(['onlined_at'=>null]) ;
    Auth::logout();
    redirect('/');
});
Route::get('error', function () {
    $domainId = Auth()->user()->recent_domain;
    $domainName = Auth()->user()->getDomainName();
    return view('main.widgets.error', compact('domainId', 'domainName'));
});
Route::get('notfound', function () {
    return view('front.error.error');
});
Route::get('maintanance', function () {
    return view('front.error.maintanance');
});

Route::get('main', function () {
    return view('front.home');
});


Route::get('excel', function () {
    Excel::create('Export data', function ($excel) {
        $excel->sheet('Sheet 1', function ($sheet) {
            $users=DB::table('users')->get();
            $data[] = ['username','recent_domain','first_name','last_name'];
            foreach ($users as $user) {
                $data[] = array(
                    $user->username,
                    $user->recent_domain,
                    $user->first_name,
                    $user->last_name,
                );
            }
            $sheet->fromArray($data, null, 'A1', false, false);
        });
    })->export('xls');
});


Route::get('/test2', function () {

    $uniq = md5(uniqid(rand(), true)) ;
    echo "del".substr($uniq, 0, 10);

    // for($i=0;$i<5;$i++){
    //  $base64 = getQRCode ("tes");


    //     echo "<img src=\"".$base64."\" > ";
    // }
});


Route::get('decode', function () {

    $code = Input::get('code');
    // var_dump($code);die;

    // $code = "TURFd05URmpNV1JtT0E9PVNXeDRNVTFIVlRCTlJuZ3hUVWRWZVU1V2VFMUVSWGRPVkVacVRWZFNiVTlCUFQweFRVZFZkMDFzZURGTlIxVjRUakY0TVUxSFZYcE5Sbmd4VFVkVk1FMUdlREZOUjFWNFdWWjRNVTFIVlhwT1ZuZ3hUVWRWZVUxc2VERk5SMVY0VDFaNE1VMUhWWGxOTVhneFRVZFZlRTVwUW1Oa1ZFSnNUVVJHWTJSVVFteE5WMVZuVGtSRk1FMVRRbU5rVkVKc1RXcFdZMlJVUW14TlYxWmpaRlJDYkUxWFJtTmtWRUpzVFhwb1kyUlVRbXhOYWs1alpGUkNiRTE2VldkWVNGVjNXbFJHYVZoSVZYZGFWRTB4V0VoVmQxcFVSVE5ZU0ZWM1dsUk5NVmhJVlhkYVZGRTBXRWhWZDFwVVVYcFlTRlYzV2xSQ2FGaElWWGRhVkZFMVdFaFZkMXBVUVROWVNGVjNXbFJOZVZoSVZYZGFWRVUxU1VSSk1VNXFSV2RZU0ZWM1dsUkpNMWhJVlhkYVZFMTRXRWhWZDFwVVJUVllTRlYzV2xSRk0xaElWWGRhVkUweFdFaFZkMXBVVVRSWVNGVjNXbFJLYTFoSVZYZGFWRXByV0VoVmQxcFVRWGhZU0ZWM1dsUkthRmhJVlhkYVZFVXhXRWhWZDFwVVRUQllTRlYzV2xSQmVGaElWWGRhVkZGM1dFaFZkMXBVUVhoWVNGVjNXbFJLYTFoSVZYZGFWRWw2V0VoVmQxcFVVbXBKUkVGNlRGUkJlRXhVU1RGT2FrVnA=" ;


    $tokentext = base64_decode($code);
    

    $hash = "A(GJ$" ;
    $encodeHash = base64_encode(substr((md5($hash)), 0, 10)) ;
    $base64Hash = str_replace($encodeHash, "", $tokentext);
    $base64Text = base64_decode($base64Hash);

    $base64Text = str_replace($encodeHash, "", $base64Text);


    $text = json_decode(base64_decode($base64Text)) ;
    echo "code : $code<BR>" ;
    
    echo "encodeHash : $encodeHash<BR>" ;
    echo "base64Text : $base64Text<BR>" ;
    echo "text : $text<BR>" ;

    
    die;
});

Route::get('str', function () {

    
    function testencrypt($data, $secret)
    {
        //Generate a key from a hash
        $key = md5(utf8_encode($secret), true);

        //Take first 8 bytes of $key and append them to the end of $key.
        $key .= substr($key, 0, 8);

        //Pad for PKCS7
        $blockSize = mcrypt_get_block_size('tripledes', 'ecb');
        $len = strlen($data);
        $pad = $blockSize - ($len % $blockSize);
        $data .= str_repeat(chr($pad), $pad);

        //Encrypt data
        $encData = mcrypt_encrypt('tripledes', $key, $data, 'ecb');

        return base64_encode($encData);
    }


    $renderer = new \BaconQrCode\Renderer\Image\Png();
    $renderer->setHeight(256);
    $renderer->setWidth(256);

    $text= '001|กก1234กทม,ขข1234พิษณุโลก|2018|88/123|EEEEDFSDEWE54D' ;
    echo strlen($text) ;


    $hash = "A(GJ$" ;

    $data['text'] = $text ;
    $encodeHash = substr((hash('sha256', $hash)), 0, 10) ;
    $code = testencrypt($text, $encodeHash);

// $code = base64_encode($encodeHash.base64_encode($firstText.$encodeHash.$secondText)) ;

    $writer = new \BaconQrCode\Writer($renderer);
    $writer->writeFile($code, 'qrcode.png');
// echo "token : $token<BR>" ;
    echo "encodeHash : $encodeHash<BR>" ;

    echo "code : $code<BR>";
    echo strlen($code) ;
    echo "<img src=\"qrcode.png\">" ;
});




Route::get('noti', function () {
    
    $url = "https://onesignal.com/api/v1/players?app_id=" . "2da81194-c514-48e2-8123-ffbe122194a0" ;

      $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.self::$APP_KEY));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
    if (isset($fields)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $return = json_decode($response, true);
        return $return ;
});

Route::get('/whitelist', function () {

    // $text = "หีบใบนี้หนักมาก";
    // $text = "หีบาน";
    $text = "หีบของมึงหนักมาก ควยมึงใหญ่มาก หีแมร่งอย่างบาน อย่างกับดอกไม้ พ่องตายสัส     ";
    echo $text."<BR>" ;


    $whitelist = ['หีบ','ดอกไม้'] ;
    $blacklist = ['หี','ดอก','พ่องตาย','ควย'] ;


    $arrayWhitelist = [];

    foreach ($whitelist as $key => $w) {
        $symbol = str_pad("", mb_strlen($w), "*");
        // echo "$w -> $symbol<BR>";
        $arrayWhitelist[$key][] = mb_strpos($text, $w);
        $text = preg_replace("/($w)/", $symbol, $text);
    }
    // echo "af whitelist : $text <BR>";


    foreach ($blacklist as $key => $b) {
        $symbol = str_pad("", mb_strlen($b), "*");
        // echo "$b -> $symbol<BR>";
        $text = preg_replace("/($b)/", $symbol, $text);
    }

    // echo "af blacklist : $text <BR>";


    function mb_substr_replace($output, $replace, $posOpen, $posClose)
    {
        return mb_substr($output, 0, $posOpen).$replace.mb_substr($output, $posClose);
    }


    foreach ($arrayWhitelist as $key => $aw) {
        //var_dump($aw);
        foreach ($aw as $k => $pos) {
            //echo $whitelist[$key]." $pos : $text<BR>" ;
            $text = mb_substr_replace(
                $text,
                $whitelist[$key],
                $pos,
                ($pos+mb_strlen($whitelist[$key]))
            );
            //echo "[$key],$text<BR>";
        }
    }


    



    echo $text ;
});

Route::post('/test/upload', function (Request $request) {
    var_dump($request);
    die;
});


Route::get('/test', function () {
        


        $totalVoter = 3 ;

        $vote = [2,2,0] ;


        var_dump(max($vote));
        var_dump(min($vote));
        die;

        $minPercent = 0 ;
        $maxPercent = 0 ;
        $currentVotedCompanyId = 0;
        $summaryVoter = 0 ;
        $checkDraw = true ;
    foreach ($vote as $key => $v) {
        //---  ถ้า vote % มากกว่าค่าที่เก็บไว้ แสดงว่า มากสุด
        $currentPercent = round((($v*100)/$totalVoter), 2) ;

        if ($currentPercent > $maxPercent) {
            $currentVotedCompanyId = $key+1 ;
            $maxPercent = $currentPercent ;
        } elseif ($currentPercent == $maxPercent) {
            $checkDraw = true ;
            $minPercent = $currentPercent ;
        }
        $summaryVoter++ ;
    }

        echo "totalVoter : $totalVoter<BR>";
        echo "summaryVoter : $summaryVoter<BR>";
        echo "maxPercent : $maxPercent<BR>";
        echo "minPercent : $minPercent<BR>";
        echo "currentVotedCompanyId : $currentVotedCompanyId<BR>";
        var_dump($checkDraw);
    echo "<BR>";
    if (($summaryVoter==$totalVoter) && ($maxPercent > 50)) {
        echo "save ".$currentVotedCompanyId."<BR>";
    } elseif (($summaryVoter==$totalVoter) && ($maxPercent > $minPercent)) {
        echo "min ".$currentVotedCompanyId."<BR>";
    }
});
