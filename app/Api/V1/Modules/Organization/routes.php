<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors','api.auth','permission']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('organizations','App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@index');
        $api->post('organizations','App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@store');
        $api->get('organizations/all','App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@all');
        $api->put('organizations/{id}','App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@update');
        $api->get('organizations/lists','App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@lists');
        $api->get('organizations/export','App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@export');
        $api->post('organizations/lists','App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@store');
        $api->get('organizations/{id}','App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@show');
        $api->delete('organizations/{id}','App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@destroy');
    });
});