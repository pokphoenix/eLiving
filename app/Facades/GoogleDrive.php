<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GoogleDrive extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GoogleDrive';
    }
}