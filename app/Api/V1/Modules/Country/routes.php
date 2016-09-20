<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors','api.auth','permission']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('countries','App\Api\V1\Modules\Country\Http\Controllers\CountryController@index');
        $api->post('countries','App\Api\V1\Modules\Country\Http\Controllers\CountryController@store');
        $api->put('countries/{id}','App\Api\V1\Modules\Country\Http\Controllers\CountryController@update');
        $api->get('countries/lists','App\Api\V1\Modules\Country\Http\Controllers\CountryController@lists');
        $api->get('countries/export','App\Api\V1\Modules\Country\Http\Controllers\CountryController@export');
        $api->get('countries/download','App\Api\V1\Modules\Country\Http\Controllers\CountryController@download');
        $api->post('countries/lists','App\Api\V1\Modules\Country\Http\Controllers\CountryController@store');
        $api->get('countries/{id}','App\Api\V1\Modules\Country\Http\Controllers\CountryController@show');
        $api->delete('countries/{id}','App\Api\V1\Modules\Country\Http\Controllers\CountryController@destroy');
    });
});