<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors','api.auth','permission']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('cities','App\Api\V1\Modules\City\Http\Controllers\CityController@index');
        $api->post('cities','App\Api\V1\Modules\City\Http\Controllers\CityController@store');
        $api->get('cities/export','App\Api\V1\Modules\City\Http\Controllers\CityController@export');
        $api->put('cities/{id}','App\Api\V1\Modules\City\Http\Controllers\CityController@update');
        $api->get('cities/lists','App\Api\V1\Modules\City\Http\Controllers\CityController@lists');
        $api->post('cities/lists','App\Api\V1\Modules\City\Http\Controllers\CityController@store');
        $api->get('cities/{id}','App\Api\V1\Modules\City\Http\Controllers\CityController@show');
        $api->delete('cities/{id}','App\Api\V1\Modules\City\Http\Controllers\CityController@destroy');
    });
});