<?php
    return [
        'Roles'=>array(
            'description'=>'System roles',
            'public'=>false,
            'isStandaloneWidget'=>true,
            'allowAdd'=>true,
            'viewableFields'=>array('roles.id','roles.name','roles.description','roles.is_active', 'roles.created_at', 'roles.updated_at', 'roles.created_by', 'roles.updated_by'),
            'controlRecordAccess'=>false,
            'hasPrivateRecords'=>false,
            'sharable'=>true,
            'isSpecial'=>true,
            'routes'=>array(
                'App\Api\V1\Modules\Role\Http\Controllers\RoleController@index'=>array('method'=>'GET','name'=>'view'),
                'App\Api\V1\Modules\Role\Http\Controllers\RoleController@store'=>array('method'=>'POST','name'=>'add'),
                'App\Api\V1\Modules\Role\Http\Controllers\RoleController@export'=>array('method'=>'GET','name'=>'export'),
                'App\Api\V1\Modules\Role\Http\Controllers\RoleController@update'=>array('method'=>'PUT','name'=>'update'),
                'App\Api\V1\Modules\Role\Http\Controllers\RoleController@lists'=>array('method'=>'GET','name'=>'list'),
                'App\Api\V1\Modules\Role\Http\Controllers\RoleController@show'=>array('method'=>'GET','name'=>'detail'),
                'App\Api\V1\Modules\Role\Http\Controllers\RoleController@destroy'=>array('method'=>'DELETE','name'=>'delete'),
                'App\Api\V1\Modules\Role\Http\Controllers\RoleController@permission'=>array('method'=>'GET','name'=>'list permission'),
                'App\Api\V1\Modules\Role\Http\Controllers\RoleController@updatePermission'=>array('method'=>'PUT','name'=>'update permission'),
            )
    )
];