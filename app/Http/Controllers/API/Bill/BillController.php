<?php

namespace App\Http\Controllers\API\Bill;

use App;
use App\Http\Controllers\ApiController;
use App\Models\Notification;
use App\Models\Setting;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\api\Utility;
use Excel;

class BillController extends ApiController
{

    public function __construct()
    {
    }

    
    public function index(Request $request, $domainId)
    {
        //$data['esticker_reason']  = EstickerReason::getData();
        //return $this->respondWithItem($data);
        $data['bill'] = DB::table('bill_header as h')
        ->join('bill_detail as d', 'h.bill_no', '=', 'd.bill_no')
            ->select('h.*', 'd.*')
            ->orderBy('d.id', 'desc')
            ->limit(1000)
            ->get();
        return $this->respondWithItem($data);
    }

    public function store(Request $request, $domainId)
    {
        $sentNoti =  $request->input('sent_noti');
        if ($request->hasFile('import_file')) {
            $result['result'] = "Success";
            Excel::load($request->file('import_file')->getRealPath(), function ($reader) use ($domainId, $sentNoti) {
                $dataHead ;
                foreach ($reader->toArray() as $key => $row) {
                    if (!empty($row['bill_no'])) {
                        $dataHead['bill_no'] = $row['bill_no'];
                        //$dataHead['date'] = date('Y-m-d H:i:s',strtotime($row['date']));
                        $dataHead['date'] = Carbon::parse($row['date']);

                        $dataHead['room'] = BillController::format_room_name($row['room']);
                        $dataHead['cust'] = $row['cust'];
                        $dataHead['name'] = $row['name'];
                        $dataHead['domain_id'] = $domainId;
                        DB::table('bill_header')->where('bill_no', $dataHead['bill_no'])->delete();
                        DB::table('bill_detail')->where('bill_no', $dataHead['bill_no'])->delete();
                        DB::table('bill_header')->insert($dataHead);
                        $result['result'] = "นำเข้า Header สำเร็จ";
                    }
                    $data['bill_no'] = $dataHead['bill_no'];
                    $data['total'] = $row['total'];
                    $data['bf'] = $row['bf'];
                    $data['net'] = $row['net'];
                    $data['code'] = $row['code'];
                    $data['desc_'] = $row['desc'];
                    $data['rate'] = $row['rate'];
                    $data['qty'] = $row['qty'];
                    $data['amount'] = $row['amount'];

                    if (!empty($data)) {
                        DB::table('bill_detail')->insert($data);
                        $result['result'] = "นำเข้าอย่างเดียวสำเร็จ";
                    }
                    if ($sentNoti == 1 && $data['net'] >0) {
                        $sql2 = "SELECT ur.id_card
                                ,ud.noti_player_id
                                ,ud.noti_player_id_mobile 
                                FROM user_rooms  ur
                                JOIN user_domains ud 
                                ON ud.id_card = ur.id_card
                                AND ud.domain_id = $domainId
                                JOIN rooms ro on ro.id  = ur.room_id
                                WHERE ur.approve = 1 AND CONCAT(IFNULL( ro.name_prefix,''),IFNULL(ro.name,'')) = '".$dataHead['room']."'";

                        $query = DB::select(DB::raw($sql2));
                        if (!empty($query)) {
                            DB::table('bill_notification')->where('bill_no', $dataHead['bill_no'])->delete();
                            $dataNoti['bill_no'] = $dataHead['bill_no'];
                            $dataNoti['room_no'] = $dataHead['room'];
                            $total_text = number_format($data['net'], 2, '.', ',');
                            $date_text = date('d/m/Y', strtotime($dataHead['date']));
                            $dataNoti['message'] =  $dataHead['room'] .' มียอดชำระ ณ วันที่ '.$date_text .' จำนวน '.$total_text .' บาท';
                            $dataNoti['is_sent'] = 1;
                            DB::table('bill_notification')->insert($dataNoti);

                            Notification::addNotificationMulti($query, $domainId, $dataNoti['message'], 3, 6, null, true);
                        }
                        $result['result'] = "นำเข้าและส่ง Noti สำเร็จ";
                    }
                }
            });
        } else {
            $result['result'] = "หาไฟล์ไม่เจอ";
        }
        return $this->respondWithItem($result);
    }

    public static function format_room_name($room_name)
    {
        $seperate = '/';
        $room_name_split = explode($seperate, (string)$room_name);
        
        if (!isset($room_name_split[1])) {
            $room_name_split[1] = '';
            $seperate = '';
        } else {
            $room_name_split[1] = intval($room_name_split[1]);
        }

        $room_full_no = $room_name_split[0]. $seperate.$room_name_split[1] ;
        return $room_full_no;
    }

    public function get_data(Request $request, $domainId)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $room_no = BillController::format_room_name($request->input('room_no'));
        $room_query = '1=1';
        if (!empty($room_no)) {
            $room_query = "h.room = '".$room_no."'";
        }
        $data['bill'] = DB::table('bill_header as h')
        ->join('bill_detail as d', 'h.bill_no', '=', 'd.bill_no')
            ->select('h.*', 'd.*')
            ->whereBetween('h.date', array($start_date,$end_date))
            ->whereRaw($room_query)
            ->where('h.domain_id', $domainId)
            ->get();
        return $this->respondWithItem($data);
    }

    public function edit($domainId, $id)
    {
    }

    public function update(Request $request, $domainId, $id)
    {
    }
   
    public function destroy(Request $request, $domainId, $id)
    {
    }
}
