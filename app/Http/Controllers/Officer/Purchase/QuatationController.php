<?php

namespace App\Http\Controllers\Officer\Purchase;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use App\Models\Setting;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;

class QuatationController extends Controller
{
    private $route = 'purchase/quotation' ;
    private $title = 'นิติ' ;
    private $view = 'officer.purchase.quatation' ;

    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $route = $domainId."/".$this->route.'?api_token='.Auth()->User()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route ;
        $response = $client->get($url);
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
        $quotations = $json['response']['quotations'] ;
        $status_history = $json['response']['status_history'] ;
        $taskDone = [];
        foreach ($quotations as $quotation) {
            if ($quotation['status']==7) {
                $taskDone[] = $quotation ;
            }
        }
        usort($taskDone, function ($a, $b) {
            return $b['doned_at']-$a['doned_at'];
        });

        $hasHeaduser = Auth()->user()->hasRole('head.user');


        return view($this->view.'.index', compact('title', 'route', 'domainId', 'quotations', 'status_history', 'taskDone', 'hasHeaduser', 'domainName'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($domainId)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($domainId)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $domainId, $quotationId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $route = $domainId."/".$this->route.'?api_token='.Auth()->User()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route ;
        $response = $client->get($url);
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
        $quotations = $json['response']['quotations'] ;
        $taskDone = [];
        foreach ($quotations as $quotation) {
            if ($quotation['status']==7) {
                $taskDone[] = $quotation ;
            }
        }
        usort($taskDone, function ($a, $b) {
            return $b['doned_at']-$a['doned_at'];
        });

        $hasHeaduser = Auth()->user()->hasRole('head.user');

        return view($this->view.'.index', compact('title', 'route', 'domainId', 'quotations', 'quotationId', 'taskDone', 'hasHeaduser', 'domainName'));
    }

    public function printQuotation(Request $request, $domainId, $quotationId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $route = $domainId."/".$this->route.'/data/'.$quotationId.'?api_token='.Auth()->User()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route ;
        $response = $client->get($url);
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
        $data = $json['response'] ;


        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/purchase/quotation/setting?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
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
        $setting = $json['response'] ;


        $logo['logo_domain'] = Setting::getVal($domainId, 'LOGO_DOMAIN');
        $logo['logo_officer'] = Setting::getVal($domainId, 'LOGO_OFFICER');

        // $setting = [];
        // $setting['header'] = "ใบเปรียบเทียบราคาชุด" ;
        // $setting['subject'] = "ขอเสนอเปรียบเทียบราคา" ;
        // $setting['inform'] = "คณะกรรมการนิติบุคคลอาคารชุดฟิวส์ สาธร-ตากสิน" ;
        // $setting['remark'] = "จากการเปรียบเทียบราคา เห็นสมควรอนุมัติจัดซื้อกับ" ;

        // $setting['sign_1'] = "หัวหน้าช่างอาคาร" ;
        // $setting['sign_2'] = "ผู้จัดการอาคาร" ;
        $col = 1 ;
       
        $hasHeaduser = Auth()->user()->hasRole('head.user');

        return view($this->view.'.print', compact('title', 'route', 'domainId', 'data', 'quotationId', 'taskDone', 'hasHeaduser', 'setting', 'col', 'domainName', 'logo'));
    }

    public function printPreview(Request $request, $domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
    
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/purchase/quotation/setting?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
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
        $setting = $json['response'] ;


        // $setting = [];
        // $setting['header'] = "ใบเปรียบเทียบราคาชุด" ;
        // $setting['subject'] = "ขอเสนอเปรียบเทียบราคา" ;
        // $setting['inform'] = "คณะกรรมการนิติบุคคลอาคารชุดฟิวส์ สาธร-ตากสิน" ;
        // $setting['remark'] = "จากการเปรียบเทียบราคา เห็นสมควรอนุมัติจัดซื้อกับ" ;

        // $setting['sign_1'] = "หัวหน้าช่างอาคาร" ;
        // $setting['sign_2'] = "ผู้จัดการอาคาร" ;
        $col = 1 ;
        $preview = true;
        $data['quotation']['title'] = "ทดสอบ";
        $data['quotation']['description'] = "เนื่องจากราคาถูกที่สุด";
        $data['quotation_companys'] = [];
        $data['quotation_items'] = [];
        

        return view($this->view.'.print', compact('title', 'route', 'domainId', 'domainName', 'data', 'setting', 'col', 'preview'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($domainId, $id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $domainId, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($domainId, $id)
    {
    }
    public function settingGet($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $route = $this->route ;

        $action =  url('').'/api/'.$domainId.'/'.$route.'/setting?api_token='.Auth()->User()->api_token ;

        
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/'.$route.'/setting/edit?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
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
        $data = $json['response'];


        return view('admin.quotation_setting.index', compact('title', 'route', 'domainId', 'domainName', 'data', 'action'));
    }
    public function voteSettingGet($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;
        $route = $this->route ;

        $action =  url('').'/api/'.$domainId.'/quotation-vote-setting?api_token='.Auth()->User()->api_token ;

        
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId.'/quotation-vote-setting?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
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
        $data = $json['response'];


        return view('admin.quotation_vote_setting.index', compact('title', 'route', 'domainId', 'domainName', 'data', 'action'));
    }
}
