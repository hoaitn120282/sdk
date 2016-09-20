<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors','api.auth','permission']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('wards','App\Api\V1\Modules\Ward\Http\Controllers\WardController@index');
        $api->post('wards','App\Api\V1\Modules\Ward\Http\Controllers\WardController@store');
        $api->put('wards/{id}','App\Api\V1\Modules\Ward\Http\Controllers\WardController@update');
        $api->get('wards/lists','App\Api\V1\Modules\Ward\Http\Controllers\WardController@lists');
        $api->post('wards/lists','App\Api\V1\Modules\Ward\Http\Controllers\WardController@store');
        $api->get('wards/{id}','App\Api\V1\Modules\Ward\Http\Controllers\WardController@show');
        $api->delete('wards/{id}','App\Api\V1\Modules\Ward\Http\Controllers\WardController@destroy');
    });
});