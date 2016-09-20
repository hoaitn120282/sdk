<?php

namespace QSoftvn\Modules;
use App;
use Config;
use Lang;
use View;

/**
 * Create a service provider to register with the application to load QSDK classes
 * @package QSoftvn\Modules
 */
class CoreServiceProvider extends  \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        //Include all routes
        $directories = glob(__DIR__.'/*' , GLOB_ONLYDIR);
        foreach($directories as $dir){
            if(file_exists($dir.'/routes.php')) {
                include $dir.'/routes.php';
            }
        }
    }
    
    public function register(){

    }
    protected function registerNamespaces()
    {

    }
    public function provides()
    {

    }
}