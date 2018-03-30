<?php

namespace App\Http\Controllers;

use App\Models\Log\LogError;
use Illuminate\Support\Facades\App;

class ApiController extends Controller
{

    // try {

    // }catch (\Exception $e) {
    //         $errors = $this->catchError($e);
    //         return $this->respondWithError($errors);
    // }


    protected function respondWithError($error, $headers = [])
    {

        LogError::SetLogError(json_encode(['result'=>'false','errors' => $error]), 0);

        return response()->json(['result'=>'false','errors' => $error], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    protected function langMessage($msgThai, $msgEng)
    {

        return getLang()=='en' ? $msgEng : $msgThai ;
    }


    /**
     * @param Model $item
     * @param TransformerAbstract $transformer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function respondWithItem($data)
    {
        $response = ['result'=>'true','errors'=>'','response'=>$data] ;
         LogError::SetLogError(json_encode($response), 1);
        return response()->json($response, 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    protected function catchError($e)
    {
        if ($e->getCode()=="23000") {
            $errors = "ข้อมูลซ้ำ" ;
        } else {
            $errors = $e->getMessage();
        }
        return $errors ;
    }
}
