<?php

namespace App\Providers;

use App\Tools\Domain;
use App\Tools\Permission;
use Illuminate\Support\ServiceProvider;

class ToolsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('Domain', function () {
            return new Domain;
        });
        $this->app->bind('Permission', function () {
            return new Permission;
        });

        // $this->app->bind('Role', function () {
        //     return new Permission;
        // });
    }
}
