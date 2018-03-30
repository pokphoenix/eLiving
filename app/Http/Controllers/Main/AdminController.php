<?php

namespace App\Http\Controllers\Back;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller ;
use DB;
use Route;
use stdClass ;
use Auth;

class AdminController extends Controller
{
    public $route = 'admin/healthtip' ;
    public $controllerName = 'นิติ' ;
    public $view = 'backend.niti' ;
    public $resize ;

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = $this->controllerName ;
        $route = $this->route ;
        // $url = $request->fullUrl();
        // $sortBy = $request->input('sortby', 'created_at');
        // $sortType = $request->input('type', 'desc');
        // $search = $request->input('search');
        // $sortNextType = ($sortType=='desc') ? 'asc' : 'desc' ;
        // if(isset($search)){
        //     $tables = healthtip::where('title_th', 'like', '%'.$search.'%')
        //         ->orWhere('title_en', 'like', '%'.$search.'%')
        //         ->orderBy($sortBy,$sortType)
        //         ->paginate(PAGINATE);
        // }else{
        //     $tables =  healthtip::orderBy($sortBy,$sortType)->paginate(PAGINATE) ;
        // }
        // $data['tables'] = $tables;
        // $data['search'] = $search;
        // $data['sortBy'] = $sortBy;
        // $data['sortType'] = $sortType;
        // $data['sortNextType'] = $sortNextType;
        // $data['auth'] = Auth::user()->isAdmin() ;
        return view($this->view.'.index', compact('title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HealthTipValidate $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(healthtip $healthtip)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(HealthTipValidate $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
