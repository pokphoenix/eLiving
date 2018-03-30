<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Domain extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Domain';
    }
}
