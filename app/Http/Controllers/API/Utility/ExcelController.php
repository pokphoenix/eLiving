<?php
namespace App\Http\Controllers\api\Utility;

use App\Http\Requests;
use Illuminate\Http\Request;
use Input;
use App\Post;
use DB;
use Session;
use Excel;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;

class ExcelController extends ApiController
{
    public function importExport()
    {
        return view('importExport');
    }
    public function downloadExcel($type)
    {
        $data = Post::get()->toArray();
        return Excel::create('laravelcode', function ($excel) use ($data) {
            $excel->sheet('mySheet', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->download($type);
    }
    public function importExcel(Request $request, $domainId)
    {
        if ($request->hasFile('import_file')) {
            Excel::load($request->file('import_file')->getRealPath(), function ($reader) {
                $dataHead ;
                foreach ($reader->toArray() as $key => $row) {
                    if (!empty($row['bill_no'])) {
                        $dataHead['bill_no'] = $row['bill_no'];
                        //$dataHead['date'] = date('Y-m-d H:i:s',strtotime($row['date']));
                        $dataHead['date'] = Carbon::parse($row['date']);
                        $dataHead['room'] = $row['room'];
                        $dataHead['cust'] = $row['cust'];
                        $dataHead['name'] = $row['name'];
                        $dataHead['domain_id'] = $domainId;
                        DB::table('bill_header')->where('bill_no', $dataHead['bill_no'])->delete();
                        DB::table('bill_detail')->where('bill_no', $dataHead['bill_no'])->delete();
                        DB::table('bill_header')->insert($dataHead);
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
                    }
                }
            });
        }
        Session::put('success', 'Youe file successfully import in database!!!');
        return back();
    }
}
