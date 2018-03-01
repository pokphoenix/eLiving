<?php

namespace App\Http\Controllers\API;
use App\Facades\Permission;
use App\Http\Controllers\ApiController;
use App\Http\Requests\SignupRequest;
use App\Models\Domain;
use App\Tools\GoogleDrive;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Route;
use stdClass ;
class HomeController extends ApiController
{
    

    public function __construct(){
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }


    public function sidebarMenu(Request $request)
    {
        $domainId = Auth()->user()->recent_domain ;
        $userId = Auth()->user()->id ;

        $sql = "SELECT (
                         SELECT count(u.id) FROM users u
                    INNER JOIN user_domains ud ON ud.id_card =  u.id_card
                    INNER JOIN user_rooms ur on u.id_card = ur.id_card
                    WHERE ur.approve = 0 AND ud.approve = 1 AND ud.domain_id=  $domainId
                ) as cnt_request_room
                
                ,(SELECT count(id_card) FROM user_domains WHERE domain_id=$domainId AND (approve!=1 AND approve!=4) AND id_card not in ( SELECT id_card FROM role_user WHERE role_id = 7 )) as cnt_wait_for_approve
                , (SELECT count(id) FROM tasks WHERE status=1 and type=2 and domain_id= $domainId) as cnt_task_new
                " ;

        if(Auth()->user()->hasRole('officers')){
            $sql .= " ,( SELECT count(id)  FROM quotations WHERE status=3 AND domain_id =  $domainId)  as cnt_quotation_voted ";
        } 
        if(Auth()->user()->hasRole('head.user')){
            $sql .= "  , (SELECT count(q1.id) FROM quotations as q1 
                    JOIN (
                    SELECT q.id,qv.company_id FROM quotations q LEFT JOIN quotation_vote qv ON qv.quotation_id = q.id AND user_id = $userId  WHERE q.status = 2 AND q.domain_id= $domainId
                    ) t2 
                    ON q1.id = t2.id 
                    WHERE t2.company_id is null 
                      ) as cnt_quotation_has_voting";
        }

        $query   =  collect(DB::select(DB::raw($sql)))->first();
        $data =  new \stdClass();
        $data->cnt_request_room = isset($query->cnt_request_room) ? $query->cnt_request_room : 0 ;
        $data->cnt_wait_for_approve = isset($query->cnt_wait_for_approve) ? $query->cnt_wait_for_approve : 0 ;
        $data->cnt_task_new = isset($query->cnt_task_new) ?  $query->cnt_task_new : 0 ;
        $data->cnt_quotation_voted = isset($query->cnt_quotation_voted) ?  $query->cnt_quotation_voted : 0;
        $data->cnt_quotation_has_voting = isset($query->cnt_quotation_has_voting) ?  $query->cnt_quotation_has_voting : 0 ;

        return $this->respondWithItem($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
    public function update(Request $request, $id)
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

    public function testPut(Request $request)
    {
        return $this->respondWithItem($request->all());
    } 

    public function testPost(Request $request)
    {
        return $this->respondWithItem($request->all());
    }  

    public function test(Request $request)
    {


        // $test = GoogleDrive::getFileList("1m8M4Mx1Tfke0eeN_UiWfWdZKcuDzfCreiNwzv4OrWCM");

        // $test = GoogleDrive::getFileList("1fxjSkXivFzXpBC9hI8J_nyRTYgcCkmvu");
        $test = GoogleDrive::getFile("1GOLiz6A4J8698ysyh_DhqW8gujgWgQQc");
        var_dump($test);die;
        return $this->respondWithItem($request->all());
    } 
    
    public function upload(Request $request)
    {
        $file = $request->file('fileToUpload');

        $mimeType = $file->getMimeType();
        $title = $file->getClientOriginalName();
        $fileextention = $file->getClientOriginalExtension();
        $filesize = $file->getSize();
        
        $filename = $file->getRealPath();

        echo "mimeType : $mimeType<BR>";
        echo "title : $title<BR>";
        echo "filename : $filename<BR>";
        echo "fileextention : $fileextention<BR>";
        echo "filesize : $filesize<BR>";

        
        $test = GoogleDrive::uploadfile($title, 'test-upload-desc', null, $mimeType, $filename);
        var_dump($test);die;
        return $this->respondWithItem($request->all());
    } 
   
}
