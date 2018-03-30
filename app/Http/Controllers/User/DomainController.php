<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DomainController extends Controller
{
    private $view = 'main.domain';
    private $title = 'โครงการ';
    private $route = 'domain';
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
    public function index()
    {

        $title = $this->title ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/domain?api_token='.Auth()->User()->api_token ;
        $res = $client->get($url);
        $json = json_decode($res->getBody()->getContents(), true);

        if (!isset($json['result'])) {
            $json['errors'] = $res->getBody()->getContents() ;
            return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']==false) {
            return redirect('error')
                ->withError($json['errors']);
        }
        $user = $json['response']['user'] ;

        //-- ถ้า มีข้อมูล domain ล่าสุดให้ แล้ว approve เข้า dashboard ของ domain นั้นๆเลย
       
        if ($user['approve_domain']==1) {
            return redirect(Auth()->User()->getDomainName().'/dashboard');
        }

        //--- version 0.1 หลังจาก auto join ให้เด้งไปหน้ารอ approve
        return redirect(Auth()->User()->getDomainName().'/dashboard');

        $domains = $json['response']['domain'] ;
        return view($this->view.'.index', compact('domains', 'title'));
    }
    public function listDomain()
    {

        $title = $this->title ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/domain?api_token='.Auth()->User()->api_token ;
        $res = $client->get($url);
        $json = json_decode($res->getBody()->getContents(), true);

        if (!isset($json['result'])) {
            $json['errors'] = $res->getBody()->getContents() ;
            return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']==false) {
            return redirect('error')
                ->withError($json['errors']);
        }
       
       
        $lists = $json['response']['domain'] ;
        return view($this->view.'.list', compact('lists', 'title'));
    }

    public function create()
    {
        $title = $this->title ;
        $route = $this->route;
        $units = Domain::unitslist();
        return view($this->view.'.create', compact('title', 'route', 'units'));
    }

    public function store(Request $request)
    {

        $post = $request->all();

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/domain?api_token='.Auth()->User()->api_token ;
        $response = $client->post($url, ['form_params'=>$post]);

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
        return redirect($json['response']['domain_name'].'/dashboard')->with('success', 'สร้างโครงการ สำเร็จ');
    }

    public function join()
    {
        $title = $this->title ;
        $route = $this->route;
        $domains = [];
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/domain/list?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(), true);
        if ($json['result']=="true") {
            $domains = $json['response']['domain'] ;
        }
        return view($this->view.'.join', compact('title', 'route', 'domains'));
    }
    public function search(Request $request)
    {
        $title = $this->title ;
        $route = $this->route;
        $domains = [];

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/domain/search?api_token='.Auth()->User()->api_token ;
        $response = $client->post($url, ['form_params'=>$request->all()]);
        $json = json_decode($response->getBody()->getContents(), true);

        if ($json['result']=="true") {
            $domains = $json['response'] ;
        }
        return $domains;
    }
    public function joinStore(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/domain/join?api_token='.Auth()->User()->api_token ;
        $response = $client->post($url, ['form_params'=>$request->all()]);
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
        if ($json['response']['approve']) {
            return redirect($json['response']['domain_id'].'dashboard')->with('success', 'เข้าร่วมโครงการ สำเร็จ');
        }

        return redirect('domain/join')->with('success', 'เข้าร่วมโครงการ สำเร็จ');
    }
}
