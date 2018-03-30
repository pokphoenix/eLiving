<?php

namespace App\Http\Controllers\Officer;
use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;
use Validator;
class ImportantDayController extends Controller
{
    private $route = 'important_day_manage' ;
    private $title ;
    private $view = 'important_day' ;

    public function __construct(){
       $this->title = App::isLocale('en') ? 'Important Day' : 'จัดการวันสำคัญ' ;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name',$domainName)->first();
        $domainId = $query->id ;
        $title = $this->title;
        $baseRoute = $this->route ;
        $route = $domainName."/".$baseRoute ;

        //$route = $domainId."/".$this->route ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route."?api_token=".auth()->user()->api_token ;

        //$url = url('').'/api/'.'bill';
        //echo  $url;
        $response = $client->get($url);
        //var_dump($response->getBody()->getContents());die;
        $json = json_decode($response->getBody()->getContents(),true); 
       // var_dump($json);die;
        if(!isset($json['result'])){
             $json['errors'] = $response->getBody()->getContents() ;
               return redirect('error')
                ->withError($json['errors']);
        }
        if($json['result']=="false")
        {
            return redirect('error')
                ->withError($json['errors']);
        }
        $lists = $json['response']['important_days'] ;

        $action = url('/api/'.$route)."?api_token=".Auth()->user()->api_token ;

        $getRoleurl = url('').'/api/master/role?api_token='.Auth()->User()->api_token ;
        $responseRoles = $client->get($getRoleurl);

        $json = json_decode($responseRoles->getBody()->getContents(),true); 
       // var_dump($json);die;
        if(!isset($json['result'])){
             $json['errors'] = $response->getBody()->getContents() ;
               return redirect('error')
                ->withError($json['errors']);
        }
        if($json['result']=="false")
        {
            return redirect('error')
                ->withError($json['errors']);
        }
        $roles = $json['response']['roles'] ;


        return view($this->view.'.important_day',compact('domainName','title','lists','action','domainId','roles'));
    }

    public function update(Request $request,$domainId)
    {

    }
}
