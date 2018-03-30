<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller ;
use App\Models\Domain;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Route;
use stdClass ;

class ChannelController extends Controller
{
    private $route = 'channel' ;
    private $title = 'Chat Room' ;
    private $view = 'main.channel' ;

    public function __construct()
    {
        $this->middleware('auth');
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

        $home = $domainId."/dashboard" ;
        $title = $this->title ;
        $route = $domainId."/".$this->route ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route."?api_token=".auth()->user()->api_token ;
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
        $lists = $json['response']['channel_list'] ;

        
        return view($this->view.'.index', compact('title', 'route', 'domainId', 'domainName', 'lists', 'status_history', 'home'));
    }
    public function contact($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $home = $domainId."/dashboard" ;
        $title = $this->title ;
        $route = $domainId."/".$this->route ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$route."/contact?api_token=".auth()->user()->api_token ;
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
        $lists = $json['response']['contact_list'] ;

        return view($this->view.'.contact', compact('title', 'route', 'domainId', 'domainName', 'lists', 'status_history', 'home'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($domainId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = (App::getLocale()=='th') ? 'สร้างห้องพูดคุยใหม่' : "Create chat room" ;
        $route = $domainId."/".$this->route ;
        $home = $domainId."/dashboard" ;
        $action = "api/".$domainId."/".$this->route."?api_token=".Auth()->User()->api_token ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/master/channeltype?api_token='.Auth()->User()->api_token ;
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
        $channelTypes = $json['response']['channel_type'] ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/master/channelicon?api_token='.Auth()->User()->api_token ;
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
        $channelIcons = $json['response']['channel_icon'] ;
        $urlBack = url($domainName.'/channel') ;

        return view($this->view.'.create', compact('title', 'home', 'route', 'domainId', 'domainName', 'action', 'channelTypes', 'channelIcons', 'urlBack'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $domainId)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($domainId, $channelId)
    {

        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

      
        $title = $this->title ;
        $route = $domainName."/".$this->route ;
        $home = $domainId."/dashboard" ;
        $action = "api/".$domainId."/".$this->route."/".$channelId."/invite?api_token=".auth()->user()->api_token ;
       
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/'.$domainId."/".$this->route.'/'.$channelId.'?api_token='.Auth()->User()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(), true);
        if (!isset($json['result'])) {
             $json['errors'] = $response->getBody()->getContents() ;
               return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']=="false") {
            if ($json['errors']=="คุณไม่มีสิทธิ์ในห้องนี้ค่ะ") {
                return redirect($route."/$channelId/member");
            } else {
                 return redirect('error')
                ->withError($json['errors']);
            }
        }

        $channels = $json['response']['channels'] ;

        $actionStatus = $json['response']['status'] ;
        $messages = $json['response']['messages'] ;

        $members = $json['response']['member_channel'] ;
        $requests = $json['response']['member_request_channel'] ;
        // $message_seen = $json['response']['message_seen'] ;
        return view($this->view.'.show', compact('title', 'route', 'home', 'domainId', 'domainName', 'channelId', 'channels', 'messages', 'action', 'members', 'actionStatus', 'requests'));
    }

    public function blacklist($domainId)
    {

        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

      
        $title = $this->title ;
        $route = $domainName."/".$this->route ;
        $home = $domainId."/dashboard" ;
      
        $client = new \GuzzleHttp\Client();
        $url = url('/api/')."/".$domainId."/channel/blacklist?api_token=".Auth()->User()->api_token ;

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
        
        $lists = $json['response']['channel_black_lists'] ;

        return view($this->view.'.blacklist', compact('title', 'route', 'home', 'domainId', 'domainName', 'channelId', 'lists', 'messages', 'action', 'members', 'actionStatus', 'requests'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($domainId, $id)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;


       

        $title = (App::getLocale()=='th') ? 'แก้ไขห้องพูดคุย' : "Edit chat room" ;

        $route = $domainName."/".$this->route."/".$id ;
        $home = $domainId."/dashboard" ;
        $action = "api/".$domainId."/".$this->route."/".$id."?api_token=".Auth()->User()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/".$domainId."/".$this->route."/".$id."/edit?api_token=".Auth()->User()->api_token ;
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
        $data = $json['response']['channel'] ;
        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/master/channeltype?api_token='.Auth()->User()->api_token ;
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
        $channelTypes = $json['response']['channel_type'] ;

        $client = new \GuzzleHttp\Client();
        $url = url('').'/api/master/channelicon?api_token='.Auth()->User()->api_token ;
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
        $channelIcons = $json['response']['channel_icon'] ;
        $urlBack = url($domainName.'/channel/'.$id);
        $edit=true;
        return view($this->view.'.create', compact('title', 'home', 'route', 'domainId', 'domainName', 'action', 'channelTypes', 'channelIcons', 'data', 'edit', 'urlBack'));
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

    public function member($domainId, $channelId)
    {
        $domainName = $domainId ;
        $query = Domain::where('url_name', $domainName)->first();
        $domainId = $query->id ;

        $title = $this->title ;
        $route = $domainName."/".$this->route ;
        $home = $domainId."/dashboard" ;
        $action = "api/".$domainId."/".$this->route."/".$channelId."/join?api_token=".auth()->user()->api_token ;
        $client = new \GuzzleHttp\Client();
        $url = url('')."/api/".$domainId."/".$this->route."/".$channelId."/member?api_token=".Auth()->User()->api_token ;
        $response = $client->get($url);
        $json = json_decode($response->getBody()->getContents(), true);

        if (!isset($json['result'])) {
             $json['errors'] = $response->getBody()->getContents() ;
               return redirect('error')
                ->withError($json['errors']);
        }
        if ($json['result']=="false") {
            if ($json['errors']=="คุณไม่มีสิทธิ์ในห้องนี้ค่ะ") {
                return redirect('error')
                ->withError($json['errors']);
            }
        }
        $channel = $json['response']['channel'] ;
        $members = $json['response']['channel_members'] ;
        // $owners = $json['response']['channel_owners'] ;
        $memberRequests = $json['response']['channel_member_requests'] ;
        $actionStatus = $json['response']['action_status'] ;
      
        return view($this->view.'.member', compact('title', 'route', 'home', 'domainId', 'domainName', 'channelId', 'channel', 'members', 'action', 'actionStatus', 'memberRequests'));
    }
}
