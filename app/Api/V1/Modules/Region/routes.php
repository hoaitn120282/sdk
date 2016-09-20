<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors','api.auth','permission']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('regions','App\Api\V1\Modules\Region\Http\Controllers\RegionController@index');
        $api->post('regions','App\Api\V1\Modules\Region\Http\Controllers\RegionController@store');
        $api->put('regions/{id}','App\Api\V1\Modules\Region\Http\Controllers\RegionController@update');
        $api->get('regions/all','App\Api\V1\Modules\Region\Http\Controllers\RegionController@all');
        $api->get('regions/export','App\Api\V1\Modules\Region\Http\Controllers\RegionController@export');
        $api->get('regions/lists','App\Api\V1\Modules\Region\Http\Controllers\RegionController@lists');
        $api->post('regions/lists','App\Api\V1\Modules\Region\Http\Controllers\RegionController@store');
        $api->get('regions/{id}','App\Api\V1\Modules\Region\Http\Controllers\RegionController@show');
        $api->delete('regions/{id}','App\Api\V1\Modules\Region\Http\Controllers\RegionController@destroy');
    });
});