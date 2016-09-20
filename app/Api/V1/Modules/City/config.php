<?php
return [
    'Cities'=>array(
        'description'=>'List of cities',
        'public'=>true,
        'isStandaloneWidget'=>true,
        'allowAdd'=>true,
        'viewableFields'=>array('cities.id','cities.name','cities.latitude','cities.longitude','cities.region_id','cities.created_at','cities.created_by','cities.updated_at','cities.updated_by'),
        'controlRecordAccess'=>false,
        'hasPrivateRecords'=>false,
        'sharable'=>false,
        'isSpecial'=>false,
        'routes'=>array(
            'App\Api\V1\Modules\City\Http\Controllers\CityController@index'=>array('method'=>'GET','name'=>'view'),
            'App\Api\V1\Modules\City\Http\Controllers\CityController@store'=>array('method'=>'POST','name'=>'add'),
            'App\Api\V1\Modules\City\Http\Controllers\CityController@export'=>array('method'=>'GET','name'=>'export'),
            'App\Api\V1\Modules\City\Http\Controllers\CityController@update'=>array('method'=>'PUT','name'=>'update'),
            'App\Api\V1\Modules\City\Http\Controllers\CityController@lists'=>array('method'=>'GET','name'=>'list'),
            'App\Api\V1\Modules\City\Http\Controllers\CityController@show'=>array('method'=>'GET','name'=>'detail'),
            'App\Api\V1\Modules\City\Http\Controllers\CityController@destroy'=>array('method'=>'DELETE','name'=>'delete'),
        )
    )
];