<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * API Routes Configuration
 */
$api = app('Dingo\Api\Routing\Router');
//Menu
$api->version('v1', ['middleware' => ['cors','api.auth']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('users/menu','App\Api\V1\Modules\User\Http\Controllers\UserController@menu');
    });
});
$api->version('v1', ['middleware' => ['cors','api.auth','permission']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('users','App\Api\V1\Modules\User\Http\Controllers\UserController@index');
        $api->post('users','App\Api\V1\Modules\User\Http\Controllers\UserController@store');
        $api->get('users/all','App\Api\V1\Modules\User\Http\Controllers\UserController@all');
        $api->get('users/lists','App\Api\V1\Modules\User\Http\Controllers\UserController@list');
        $api->post('users/lists','App\Api\V1\Modules\User\Http\Controllers\UserController@store');
        $api->put('users/{id}','App\Api\V1\Modules\User\Http\Controllers\UserController@update');
        $api->get('users/{id}','App\Api\V1\Modules\User\Http\Controllers\UserController@show');
        $api->delete('users/{id}','App\Api\V1\Modules\User\Http\Controllers\UserController@destroy');
    });
});