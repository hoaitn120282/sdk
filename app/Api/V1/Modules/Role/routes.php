<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors','api.auth','permission']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('roles','App\Api\V1\Modules\Role\Http\Controllers\RoleController@paginate');
        $api->post('roles','App\Api\V1\Modules\Role\Http\Controllers\RoleController@store');
        $api->put('roles/{id}','App\Api\V1\Modules\Role\Http\Controllers\RoleController@update');
        $api->get('roles/all','App\Api\V1\Modules\Role\Http\Controllers\RoleController@all');
        $api->get('roles/export','App\Api\V1\Modules\Role\Http\Controllers\RoleController@export');
        $api->get('roles/lists','App\Api\V1\Modules\Role\Http\Controllers\RoleController@lists');
        $api->post('roles/lists','App\Api\V1\Modules\Role\Http\Controllers\RoleController@store');
        $api->get('roles/{id}','App\Api\V1\Modules\Role\Http\Controllers\RoleController@show');
        $api->delete('roles/{id}','App\Api\V1\Modules\Role\Http\Controllers\RoleController@destroy');
        /** For permissions */
        $api->get('roles/{id}/permissions','App\Api\V1\Modules\Role\Http\Controllers\RoleController@permission');
        $api->put('roles/{id}/permissions/{widget}','App\Api\V1\Modules\Role\Http\Controllers\RoleController@updatePermission');
    });
});