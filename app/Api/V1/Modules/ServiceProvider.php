<?php

namespace App\Api\V1\Modules;
use App;
use Config;
use Lang;
use View;

class ServiceProvider extends  \Illuminate\Support\ServiceProvider
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

    /**
     * Register the module resource namespaces.
     *
     * @return void
     */
    protected function registerNamespaces()
    {

    }
    
    public function provides()
    {

    }
}