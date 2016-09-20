<?php
return [
    'Users'=>array(
        'description'=>'List of users',
        'public'=>false,
        'isStandaloneWidget'=>true,
        'allowAdd'=>true,
        'viewableFields'=>array('users.id','users.name','users.email','users.avatar','users.note','users.active','users.created_at','users.created_by','users.updated_at','users.updated_by'),
        'controlRecordAccess'=>false,
        'hasPrivateRecords'=>false,
        'sharable'=>true,
        'isSpecial'=>true,
        'routes'=>array(
            'App\Api\V1\Modules\User\Http\Controllers\UserController@index'=>array('method'=>'GET','name'=>'view'),
            'App\Api\V1\Modules\User\Http\Controllers\UserController@store'=>array('method'=>'POST','name'=>'add'),
            'App\Api\V1\Modules\User\Http\Controllers\UserController@export'=>array('method'=>'GET','name'=>'export'),
            'App\Api\V1\Modules\User\Http\Controllers\UserController@update'=>array('method'=>'PUT','name'=>'update'),
            'App\Api\V1\Modules\User\Http\Controllers\UserController@lists'=>array('method'=>'GET','name'=>'list'),
            'App\Api\V1\Modules\User\Http\Controllers\UserController@show'=>array('method'=>'GET','name'=>'detail'),
            'App\Api\V1\Modules\User\Http\Controllers\UserController@destroy'=>array('method'=>'DELETE','name'=>'delete'),
        )
    )
];