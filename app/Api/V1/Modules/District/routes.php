<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors','api.auth','permission']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('districts','App\Api\V1\Modules\District\Http\Controllers\DistrictController@index');
        $api->post('districts','App\Api\V1\Modules\District\Http\Controllers\DistrictController@store');
        $api->put('districts/{id}','App\Api\V1\Modules\District\Http\Controllers\DistrictController@update');
        $api->get('districts/lists','App\Api\V1\Modules\District\Http\Controllers\DistrictController@lists');
        $api->post('districts/lists','App\Api\V1\Modules\District\Http\Controllers\DistrictController@store');
        $api->get('districts/{id}','App\Api\V1\Modules\District\Http\Controllers\DistrictController@show');
        $api->delete('districts/{id}','App\Api\V1\Modules\District\Http\Controllers\DistrictController@destroy');
    });
});