<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors','api.auth','permission']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('customers','App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@index');
        $api->post('customers','App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@store');
        $api->put('customers/{id}','App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@update');
        $api->get('customers/export','App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@export');
        $api->get('customers/lists','App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@lists');
        $api->get('customers/{id}','App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@show');
        $api->delete('customers/{id}','App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@destroy');
    });
});