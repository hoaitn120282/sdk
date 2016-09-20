<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        //$api->post('login', 'QSoftvn\Controllers\Auth\LoginController@login');
        $api->post('login', 'App\Http\Controllers\Auth\LoginController@login');
    });
});