<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Setting;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    private $view = 'main.dashboard';
    private $title = 'Dashboard';
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

    //use AuthenticatesUsers;

   

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('auth');
    }
    public function index($domainId)
    {
       

       
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        if (empty($query)) {
             return redirect('error')
                ->withError('ไม่พบข้อมูลโครงการที่คุณเรียก');
        }
        $domainId = $query->id ;

        $title = $this->title ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/dashboard?api_token='.Auth()->User()->api_token ;
        $response = $client->request('GET', $url);
        $json = json_decode($response->getBody()->getContents(), true);

        if (!isset($json['result'])) {
                $json['errors'] = $response->getBody()->getContents() ;
                return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']=="false") {
            return redirect('error')
                ->withError($json['errors']);
        }
        $domains = $json['response']['domain'] ;
        $notifications = $json['response']['notification'] ;
        // $quotations = $json['response']['quotation'] ;
        $preWelcom = $json['response']['pre_welcom'] ;
        $lists = $json['response']['posts'] ;
        $tasksUser = $json['response']['tasks_user'] ;
        $tasksOfficer = $json['response']['tasks_officer'] ;
        $parcels = $json['response']['parcels'] ;
        $quotations = $json['response']['quotations'] ;
        $canPost  =true;
       
        return view($this->view.'.index', compact('domains', 'title', 'domainId', 'domainName', 'notifications', 'quotations', 'preWelcom', 'lists', 'canPost', 'tasksUser', 'tasksOfficer', 'parcels', 'quotations'));
    }
}
