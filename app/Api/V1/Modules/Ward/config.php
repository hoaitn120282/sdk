<?php
    return [
        'Wards'=>array(
            'description'=>'List of wards',
            'public'=>false,
            'isStandaloneWidget'=>true,
            'allowAdd'=>true,
            'viewableFields'=>array('wards.name','wards.latitude','wards.longitude','wards.district_id', 'wards.created_at', 'wards.updated_at', 'wards.created_by', 'wards.updated_by'),
            'controlRecordAccess'=>false,
            'hasPrivateRecords'=>false,
            'sharable'=>false,
            'isSpecial'=>false,
            'routes'=>array(
                'App\Api\V1\Modules\Ward\Http\Controllers\WardController@index'=>array('method'=>'GET','name'=>'view'),
                'App\Api\V1\Modules\Ward\Http\Controllers\WardController@store'=>array('method'=>'POST','name'=>'add'),
                'App\Api\V1\Modules\Ward\Http\Controllers\WardController@export'=>array('method'=>'GET','name'=>'export'),
                'App\Api\V1\Modules\Ward\Http\Controllers\WardController@update'=>array('method'=>'PUT','name'=>'update'),
                'App\Api\V1\Modules\Ward\Http\Controllers\WardController@lists'=>array('method'=>'GET','name'=>'list'),
                'App\Api\V1\Modules\Ward\Http\Controllers\WardController@show'=>array('method'=>'GET','name'=>'detail'),
                'App\Api\V1\Modules\Ward\Http\Controllers\WardController@destroy'=>array('method'=>'DELETE','name'=>'delete'),
            )
    )
];