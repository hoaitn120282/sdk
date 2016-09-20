<?php

namespace QSoftvn\Providers;
use QSoftvn\Exceptions\ApiHandler;
use Dingo\Api\Provider\LaravelServiceProvider;

/**
 * This class extend LaravelServiceProvider to route the error exception to QSDK's one to capture error thrown by DingoAPI
 * @package QSoftvn\Providers
 */
class ApiServiceProvider extends LaravelServiceProvider{

    protected function registerExceptionHandler(){
        $this->app->singleton('api.exception', function ($app) {
            $config = $this->app['config']['api'];
            return new ApiHandler($app['Illuminate\Contracts\Debug\ExceptionHandler'], $config['errorFormat'], $config['debug']);
        });
    }
}