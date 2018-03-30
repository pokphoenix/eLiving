<?php
namespace App\Tools;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Utility extends Model
{
   

    public function __construct()
    {
    }

    public static function strMax($string)
    {
        $char = strlen($string) ;
        if ($char > 1000) {
            $string = substr($string, 0, 1000) ;
        }
        return $string ;
    }
}
