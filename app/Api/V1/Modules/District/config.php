<?php
    return [
        'Districts'=>array(
            'description'=>'List of districts',
            'public'=>false,
            'isStandaloneWidget'=>true,
            'allowAdd'=>true,
            'viewableFields'=>array('districts.id','districts.name','districts.latitude','districts.longitude','districts.region_id', 'districts.created_at', 'districts.updated_at', 'districts.created_by', 'districts.updated_by'),
            'controlRecordAccess'=>false,
            'hasPrivateRecords'=>false,
            'sharable'=>false,
            'isSpecial'=>false,
            'routes'=>array(
                'App\Api\V1\Modules\District\Http\Controllers\DistrictController@index'=>array('method'=>'GET','name'=>'view'),
                'App\Api\V1\Modules\District\Http\Controllers\DistrictController@store'=>array('method'=>'POST','name'=>'add'),
                'App\Api\V1\Modules\District\Http\Controllers\DistrictController@export'=>array('method'=>'GET','name'=>'export'),
                'App\Api\V1\Modules\District\Http\Controllers\DistrictController@update'=>array('method'=>'PUT','name'=>'update'),
                'App\Api\V1\Modules\District\Http\Controllers\DistrictController@lists'=>array('method'=>'GET','name'=>'list'),
                'App\Api\V1\Modules\District\Http\Controllers\DistrictController@show'=>array('method'=>'GET','name'=>'detail'),
                'App\Api\V1\Modules\District\Http\Controllers\DistrictController@destroy'=>array('method'=>'DELETE','name'=>'delete'),
            )
        )
    ];