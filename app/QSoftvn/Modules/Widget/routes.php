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
 * Routing for widget list is defined here in this file
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['cors','api.auth']], function ($api) {
    $api->group(['prefix' => 'v1'], function ($api) {
        $api->get('widget/{id}/fields','QSoftvn\Modules\Widget\Http\Controllers\WidgetController@fields');
        $api->get('widget/routes','QSoftvn\Modules\Widget\Http\Controllers\WidgetController@allRoutes');
        $api->get('widget/{id}/routes','QSoftvn\Modules\Widget\Http\Controllers\WidgetController@route');
    });
});