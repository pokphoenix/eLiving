<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\Search;
use App\Models\StatusHistory;
use App\Models\Task\Task;
use App\Models\Task\TaskAttach;
use App\Models\Task\TaskCategory;
use App\Models\Task\TaskChecklist;
use App\Models\Task\TaskChecklistItem;
use App\Models\Task\TaskComment;
use App\Models\Task\TaskHistory;
use App\Models\Task\TaskMember;
use App\Models\Task\TaskViewer;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class RoomController extends ApiController
{
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

    public function __construct()
    {
        // $this->middleware('auth:api');
    }

  

    public function index($domainId)
    {
        $url = url('');
        $sql = "select r.*
                ,IFNULL(t2.cnt,0) as room_cnt
                from rooms as r
                left join ( 
                    SELECT room_id,count(room_id) as cnt
                    FROM user_rooms 
                    GROUP BY room_id
                ) t2
                ON t2.room_id = r.id
                WHERE r.domain_id=$domainId
                ORDER BY r.id DESC" ;
        $query  =  DB::select(DB::raw($sql));
           
        $data['rooms'] = $query;
       
        return $this->respondWithItem($data);
    }
   
    public function show($domainId, $roomId, $taskId)
    {
        $data = Task::getTaskData($domainId, $taskId, 2);
        return $this->respondWithItem($data);
    }

    public function create($domainId, $roomId)
    {
        $data = [];
        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId)
    {
        $userId = Auth::user()->id ;
        $post = $request->except('api_token', '_method');
        if (isset($post['is_run'])) {
            $validator = $this->validatorMultiRoom($post);
        } else {
            $validator = $this->validator($post);
        }
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $roomLimit = 500 ;

        $txtErrorOverLimit ='ไม่สามารถกำหนดเกิน '.$roomLimit.' ห้องในครั้งเดียวได้ค่ะ';
        $txtErrorRepeat = "ชื่อห้องซ้ำค่ะ";
        
        if (getLang()=='en') {
            $txtErrorOverLimit ='room over limit, please create '.$roomLimit.' room per times';
            $txtErrorRepeat = "room name already exits";
        }

        if (isset($post['is_run'])) {
            if (($post['number_end']-$post['number_start']) > $roomLimit) {
                return $this->respondWithError($txtErrorOverLimit);
            }
            $findRoomNumber = "";
            for ($i = $post['number_start']; $i <= $post['number_end']; $i++) {
                $room['domain_id'] =  $domainId;
                $room['name_prefix'] =  $post['name_prefix'] ;
                $room['name'] =  $i ;
                $room['name_surfix'] =  $post['name_surfix'] ;
                $save[] =  $room;
                $findRoomNumber .=",'$i'";
            }

            $findRoomNumber = substr($findRoomNumber, 1);

            $sql = "SELECT * FROM rooms WHERE name_prefix = '".$post['name_prefix']."' AND (name_surfix like '%".$post['name_prefix']."%' OR name_surfix is null ) AND name in ($findRoomNumber) " ;

            $repeatRoom = DB::select(DB::raw($sql));
            if (!empty($repeatRoom)) {
                return $this->respondWithError($txtErrorRepeat);
            }

            Room::insert($save);
        } else {
            $repeat = Room::where('name_prefix', $post['name_prefix'])->where('name', $post['name'])->first();
            if (!empty($repeat)) {
                return $this->respondWithError($txtErrorRepeat);
            }



            $room = new Room();
            $post['domain_id'] =  $domainId;
            $room->fill($post)->save();
        }

        

        $data['text'] = "success" ;
        return $this->respondWithItem($data);
    }
    public function edit($domainId, $roomId)
    {
        $data = [];
        $room = Room::find($roomId);
        $roomUser = RoomUser::from('user_rooms as ru')
                    ->join('users as u', 'u.id_card', '=', 'ru.id_card')
                    ->where('ru.room_id', $roomId)
                    ->select(DB::raw('ru.*,CONCAT( first_name," ",last_name) as text_name'))
                    ->get();
        $data['room'] = $room;
        $data['room_user'] = $roomUser;
        return $this->respondWithItem($data);
    }
    public function update(Request $request, $domainId, $roomId)
    {

        $userId = Auth::user()->id ;
        $post = $request->except('api_token', '_method');
        $validator = $this->validator($post);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

      

        $repeat = Room::where('name_prefix', $post['name_prefix'])->where('name', $post['name'])->where('id', '<>', $roomId)->first();
        if (!empty($repeat)) {
            return $this->respondWithError('room name already exits');
        }


        $room = Room::find($roomId);
        $post['domain_id'] =  $domainId;
        $room->update($post);

        if (isset($post['user-room'])) {
            User::userAddRoom($post, Auth()->user()->idcard, $roomId);
        }


        $data['text'] = "success" ;
        return $this->respondWithItem($data);
    }

    public function removeUser(Request $request, $domainId, $roomId, $idCard)
    {
        RoomUser::where('id_card', $idCard)
        ->where('room_id', $roomId)
        ->delete();
        $data['text'] = "success" ;
        return $this->respondWithItem($data);
    }

    private function validator($data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
        ]);
    }
    private function validatorMultiRoom($data)
    {
        return Validator::make($data, [
            'name_prefix' => 'required|string|max:255',
            'number_start' => 'required|string|max:255',
            'number_start' => 'required|string|max:255',
        ]);
    }
}
