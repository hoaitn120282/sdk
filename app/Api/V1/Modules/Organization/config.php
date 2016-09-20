<?php
    return [
        'Organizations'=>array(
            'description'=>'Company Structure',
            'public'=>false,
            'isStandaloneWidget'=>true,
            'allowAdd'=>true,
            'viewableFields'=>array('roles.id','roles.name','roles.description','roles.is_active', 'roles.created_at', 'roles.updated_at', 'roles.created_by', 'roles.updated_by','roles.parent_id'),
            'controlRecordAccess'=>false,
            'hasPrivateRecords'=>false,
            'sharable'=>false,
            'isSpecial'=>true,
            'routes'=>array(
                'App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@index'=>array('method'=>'GET','name'=>'view'),
                'App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@store'=>array('method'=>'POST','name'=>'add'),
                'App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@export'=>array('method'=>'GET','name'=>'export'),
                'App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@update'=>array('method'=>'PUT','name'=>'update'),
                'App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@lists'=>array('method'=>'GET','name'=>'list'),
                'App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@show'=>array('method'=>'GET','name'=>'detail'),
                'App\Api\V1\Modules\Organization\Http\Controllers\OrganizationController@destroy'=>array('method'=>'DELETE','name'=>'delete'),
            )
    )
];