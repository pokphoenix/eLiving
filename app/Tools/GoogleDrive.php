<?php
namespace App\Tools;

use Google_Service_Drive;
use Google_Client;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Google_AssertionCredentials;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class GoogleDrive
{
    protected static $instance ;
    protected static $_GOOGLE_DRIVE = "https://www.googleapis.com/auth/drive" ;
    // protected static $_GOOGLE_FILE = "file" ;
    // protected static $_GOOGLE_APP_DATA ="appdata" ;
    protected static $_GOOGLE_APP_READONLY = ".apps.readonly" ;

    // https://drive.google.com/open?id=

    public function __construct(){
        define('APPLICATION_NAME', 'rmdriveapi');
        // define('CREDENTIALS_PATH', '~/.credentials/drive-php-quickstart.json');
        define('CREDENTIALS_PATH',  'drive-php-quickstart.json');
        define('CLIENT_SECRET_PATH', 'client_secret.json');
        // If modifying these scopes, delete your previously saved credentials
        // at ~/.credentials/drive-php-quickstart.json
        // define('SCOPES', implode(' ', array(
        //   Google_Service_Drive::DRIVE_READONLY,Google_Service_Drive::DRIVE_FILE,Google_Service_Drive::DRIVE_READONLY)
        // ));

        define('SCOPES',implode(' ', array(
              Google_Service_Drive::DRIVE
              ,Google_Service_Drive::DRIVE_FILE
              ,Google_Service_Drive::DRIVE_APPDATA
              ,Google_Service_Drive::DRIVE_READONLY
              ,Google_Service_Drive::DRIVE_PHOTOS_READONLY
              ,Google_Service_Drive::DRIVE_METADATA_READONLY
            )));

    }

    public static function getInstance() {
        if (!isset(self::$instance))
        {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }

     public static function getFileList(){
        $google = GoogleDrive::getInstance();
        $client = $google->getClient();
        $service = new Google_Service_Drive($client);

        // Print the names and IDs for up to 10 files.
        $optParams = array(
          'pageSize' => 10,
          'fields' => 'nextPageToken, files(id, name)'
        );
        $results = $service->files->listFiles($optParams);
        if (count($results->getFiles()) == 0) {
          print "No files found.\n";
        } else {
          print "Files:\n";
          foreach ($results->getFiles() as $file) {
            printf("%s (%s)\n", $file->getName(), $file->getId());
          }
        }
    }

    public static function getFile($fileId){

        // $url = "https://www.googleapis.com/drive/v2/files/$fileId?alt=json" ;
        // $response = self::curl_send($url) ;
        // var_dump($response);die;
        $google = GoogleDrive::getInstance();
        $client = $google->getClient();
        $service = new Google_Service_Drive($client);

        try {
            // $file = $service->files->get($fileId);
            $fileId = '1GOLiz6A4J8698ysyh_DhqW8gujgWgQQc';

            $response = $service->files->export($fileId, 'application/pdf', array(
    'alt' => 'media'));
$content = $response->getBody()->getContents();
            die();

            $response = $service->files->get($fileId, array(
    'alt' => 'media'));
            $content = $response->getBody()->getContents();

            // $response = $service->files->get($fileId, array('alt' => 'media'));
            // $content = $response->getBody()->getContents();
            // var_dump($content);die;
            // echo "<pre>";
            // var_dump($file);
            // echo "</pre>";
            // die;
            echo "ID : ".$file->getId()."<BR>" ;
            echo "Name : ".$file->getName()."<BR>" ;
            echo "mimeType : ".$file->getMimeType()."<BR>" ;
            echo "getThumbnailLink : ".$file->getThumbnailLink()."<BR>" ;
            echo "getImageMediaMetadata : ".$file->getImageMediaMetadata()."<BR>" ;
            echo "getWebViewLink : ".$file->getWebViewLink()."<BR>" ;
            echo "getWebContentLink : ".$file->getWebContentLink()."<BR>" ;
            echo "getViewersCanCopyContent : ".$file->getViewersCanCopyContent()."<BR>" ;
            // die;

            // $fileId = '1ZdR3L3qP4Bkq8noWLJHSr_iBau0DNT4Kli4SxNc2YEo';
            // $response = $service->files->export($fileId, $file->getMimeType(), array(
            //     'alt' => 'media'));
            // $content = $response->getBody()->getContents();
            // var_dump($content);die;
            //$downloadUrl = $file->getDownloadUrl();

           // echo "downloadUrl : ".$downloadUrl."<BR>" ;
            die;
              // if ($downloadUrl) {
              //   $request = new Google_Http_Request($downloadUrl, 'GET', null, null);
              //   $httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
              //   if ($httpRequest->getResponseHttpCode() == 200) {
              //     return $httpRequest->getResponseBody();
              //   } else {
              //     // An error occurred.
              //     return null;
              //   }
              // } else {
              //   // The file doesn't have any content stored on Drive.
              //   return null;
              // }


            die;

            print "Title: " . $file->getTitle();
            print "Description: " . $file->getDescription();
            print "MIME type: " . $file->getMimeType();
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }

    }

    public static function uploadfile($title, $description, $parentId, $mimeType, $filename){
        



        // $google = new GoogleDrive;
        //  $credentialsPath =  $google->expandHomeDirectory(CREDENTIALS_PATH);
        //   if (file_exists($credentialsPath)) {
        //     $accessToken = json_decode(file_get_contents($credentialsPath), true);
        //   } else {
        //     // Request authorization from the user.
        //     $authUrl = $client->createAuthUrl();
        //     printf("Open the following link in your browser:\n%s\n", $authUrl);
        //     print 'Enter verification code: ';
        //     $authCode = trim(fgets(STDIN));

        //     // Exchange authorization code for an access token.
        //     $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        //     // Store the credentials to disk.
        //     if(!file_exists(dirname($credentialsPath))) {
        //       mkdir(dirname($credentialsPath), 0700, true);
        //     }
        //     file_put_contents($credentialsPath, json_encode($accessToken));
        //     printf("Credentials saved to %s\n", $credentialsPath);
        //   }
        // // var_dump($accessToken['access_token']);die;




        // $client = new \GuzzleHttp\Client();
        // $url = "https://www.googleapis.com/upload/drive/v2?uploadType=media" ;

        
        // $res =  $client->post($url, ['headers'=> [  'Content-Type'=>$mimeType 
        //                 ,'Content-Length'=>strlen($content)
        //                 ,'Authorization'=>'Bearer '.$accessToken['access_token']
        //     ]
           
        //     ,'multipart' => [
        //         [
        //             'name'     => $title,
        //             'filename' => $title,
        //             'contents' => $content ,
        //             'headers'=>  [  'Content-Type'=>$mimeType 
        //                 ,'Content-Length'=>strlen($content)
        //             ]
        //         ],
        //     ] 
        //     ] );

        // var_dump($res->getBody()->getContents());die;

            

        // $json = json_decode($res->getBody()->getContents(),true); 
        // var_dump($res->getBody()->getContents());die;


        $google = new GoogleDrive ;

        $scopes = [Google_Service_Drive::DRIVE
                    , Google_Service_Drive::DRIVE_FILE
                    , Google_Service_Drive::DRIVE_APPDATA
                    // , self::$_GOOGLE_DRIVE.self::$_GOOGLE_APP_READONLY
                    ] ;
      
        $client = $google->getClient($scopes);

      
        //V3 


        $service = new Google_Service_Drive($client);
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => 'Project plan',
            'mimeType' => 'application/vnd.google-apps.drive-sdk'));
        $file = $service->files->create($fileMetadata, array(
            'fields' => 'id'));
        printf("File ID: %s\n", $file->id);



       
        // $fileMetadata = new Google_Service_Drive_DriveFile(array(
        //     'name' => $title));
        // $content = file_get_contents($filename);
        // $file = $service->files->create($fileMetadata, array(
        //     'data' => $content,
        //     'mimeType' => $mimeType,
        //     'uploadType' => 'multipart',
        //     'fields' => 'id'));
        // printf("File ID: %s\n", $file->id);

        die;

        // $service = $google->buildService() ;
        $content = file_get_contents($filename);
        // $fileMetadata = new Google_Service_Drive_DriveFile(array(
        //     'title' => $title));
        $file = new Google_Service_Drive_DriveFile();

        $file->setName(uniqid().$title);
          // $file->setDescription($description);
        $file->setMimeType($mimeType);

          // var_dump($file);die;
       
        // $createdFile = $service->files->create($file, array(
        //     'data' => $content,
        //     'mimeType' => $mimeType,
        //     'uploadType' => 'multipart',
        //     'fields' => 'id'));
        // $client->setDefer(true);
        $service = new Google_Service_Drive($client);
        // var_dump($service);die;
        $createdFile = $service->files->create($file, array(
            'data' => $content,
            'mimeType' => $mimeType,
            'fields' => 'id'
        ));
        die;
        // var_dump($createdFile->id);die;


        // $permission = new Google_Service_Drive_Permission();
        // $permission->setRole( 'writer' );
        // $permission->setType( 'anyone' );
        // // var_dump($createdFile->getId());die;
        // // $permission->setValue( 'me' );
        // $service->permissions->create( $createdFile->getId(), $permission );

        // printf("File ID: %s\n", $createdFile->getId());
        // die;
            // Get your credentials from the console
        // $client->setClientId('529852459124-c4kfca8covlbd11qdte6uv3s8gkvhu0u.apps.googleusercontent.com');
        // $client->setClientSecret('R8DZ_rkrKaf7gGA7JBTEHgdO');
        // $client->setRedirectUri('<YOUR_REGISTERED_REDIRECT_URI>');
        // $client->setScopes(array('https://www.googleapis.com/auth/drive.file'));
        // $client->setAuthConfig('client_secrets.json');
        // $client->setAccessType("offline");        // offline access
       

        $file = new Google_Service_Drive_DriveFile();

        $file->setName(uniqid().$title);
          // $file->setDescription($description);
        $file->setMimeType($mimeType);

          // Set the parent folder.
          if ($parentId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
          }

        try {
            $data = file_get_contents($filename);
            $createdFile = $service->files->create($file, array(
              'data' => $data,
              'mimeType' => $mimeType,
                'uploadType' => 'multipart'
            ));

            // Uncomment the following line to print the File ID
            // print 'File ID: %s' % $createdFile->getId();

            return $createdFile;
          } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
          }
    }

   

    private function buildService() {//function for first build up service


      $key = file_get_contents(CREDENTIALS_PATH);
      $auth = new Google_AssertionCredentials(
          'pokphoenix@gmail.com',
          array('https://www.googleapis.com/auth/drive'),
          $key);
      $client = new Google_Client();
      $client->setUseObjects(true);
      $client->setAssertionCredentials($auth);
      return new Google_DriveService($client);
    }


    private function getClient($scopes=null) {

        if (is_null($scopes)){
            $scopes = SCOPES ;
        }

          $client = new Google_Client();
          $client->setApplicationName(APPLICATION_NAME);
          $client->setScopes($scopes);
          $client->setAuthConfig(CLIENT_SECRET_PATH);
          $client->setAccessType('offline');
          // $client->setApprovalPrompt("force");

          // Load previously authorized credentials from a file.
          $credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);
          if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
          } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if(!file_exists(dirname($credentialsPath))) {
              mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
          }
          $client->setAccessToken($accessToken);

          // Refresh the token if it's expired.
          if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
          }
          return $client;
    }

    private function expandHomeDirectory($path) {
      $homeDirectory = getenv('HOME');
      if (empty($homeDirectory)) {
        $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
      }
      return str_replace('~', realpath($homeDirectory), $path);
    }


    private static function curl_send($url,$type=NULL,$fields=NULL){
        $google = new GoogleDrive ;
        $credentialsPath =  $google->expandHomeDirectory(CREDENTIALS_PATH);
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Bearer '.$accessToken['access_token']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        $return = json_decode($response, true);
        return $return ;
    }
  
}