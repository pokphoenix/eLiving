<?php

define('CREATE_DATE_FORMAT', 'd-M-Y H:i');
define('UPLOAD_PATH', 'upload/');
define('HASH_PROJECT', '$R)OH%');
define('HASH_ACTIVE_MAIL', 'YN$)FIR');
define('CHECK_ONLINE_MINUTE', 5);
define('SUB_STR_MESSAGE', 10);


$envPath = "environment.txt" ;
if (file_exists($envPath)) {
    $myfile = fopen($envPath, "r") or die("Unable to open file!");
    $file = fread($myfile, filesize($envPath));
    $APP_ENV = $file ;
    fclose($myfile);
} else {
    $APP_ENV = "production" ;
}

if ($APP_ENV=="productionnew") {
    define('CONST_ONESIGNAL_APP_ID', '6e008a10-2ccc-459f-8671-ac8af9bb899f');
    define('CONST_ONESIGNAL_APP_KEY', 'ZjI5M2MzMzEtODM1Yi00YzhiLWE0OTQtNWY5YzkzMmNmN2Jm');
    define('CONST_SOCKET_URL', 'http://fush-rm-socket-fush-rm-socket.a3c1.starter-us-west-1.openshiftapps.com/');
    define('CONST_FACEBOOK_APP_ID', '1579780878768426');
    define('CONST_APP_URL_SAVE_IMAGE', "http://restapiscan.eliving.in.th");
} elseif ($APP_ENV=="devnew") {
    define('CONST_ONESIGNAL_APP_ID', '6e008a10-2ccc-459f-8671-ac8af9bb899f');
    define('CONST_ONESIGNAL_APP_KEY', 'ZjI5M2MzMzEtODM1Yi00YzhiLWE0OTQtNWY5YzkzMmNmN2Jm');
    define('CONST_SOCKET_URL', 'http://fush-rm-socket-fush-rm-socket.a3c1.starter-us-west-1.openshiftapps.com/');
    define('CONST_FACEBOOK_APP_ID', '1579780878768426');
    define('CONST_APP_URL_SAVE_IMAGE', "http://restapiscan.eliving.in.th");
} elseif ($APP_ENV=="production") {
      define('CONST_ONESIGNAL_APP_ID', '6e008a10-2ccc-459f-8671-ac8af9bb899f');
    define('CONST_ONESIGNAL_APP_KEY', 'ZjI5M2MzMzEtODM1Yi00YzhiLWE0OTQtNWY5YzkzMmNmN2Jm');
    define('CONST_SOCKET_URL', 'http://fush-rm-socket-fush-rm-socket.a3c1.starter-us-west-1.openshiftapps.com/');
    define('CONST_FACEBOOK_APP_ID', '1579780878768426');
    define('CONST_APP_URL_SAVE_IMAGE', "http://fusescan.ferretking.com");
} elseif ($APP_ENV=="dev") {
      define('CONST_ONESIGNAL_APP_ID', '2da81194-c514-48e2-8123-ffbe122194a0');
    define('CONST_ONESIGNAL_APP_KEY', 'YjAwMTlmMWYtMmJhMi00Mzk2LTkyNzItMDMzOTE4YjY4NzBl');
    define('CONST_SOCKET_URL', 'http://rm-residence.193b.starter-ca-central-1.openshiftapps.com');
    // define('CONST_SOCKET_URL',  'http://soc.eliving.in.th/' );
    define('CONST_FACEBOOK_APP_ID', '2081296605485827');
    define('CONST_APP_URL_SAVE_IMAGE', "http://rmscan.ferretking.com");
} else {
      define('CONST_ONESIGNAL_APP_ID', 'test');
    define('CONST_ONESIGNAL_APP_KEY', 'test');
    define('CONST_SOCKET_URL', 'localhost:8080');
    define('CONST_FACEBOOK_APP_ID', '771379236397309');
    define('CONST_APP_URL_SAVE_IMAGE', "http://localhost/laravel/scandoc");
}
