<?php
    return [
        'Regions'=>array(
            'description'=>'List of regions',
            'public'=>false,
            'isStandaloneWidget'=>true,
            'allowAdd'=>true,
            'viewableFields'=>array('regions.id','regions.name','regions.latitude','regions.longitude','regions.country_id', 'regions.created_at', 'regions.updated_at', 'regions.created_by', 'regions.updated_by'),
            'controlRecordAccess'=>false,
            'hasPrivateRecords'=>false,
            'sharable'=>false,
            'isSpecial'=>false,
            'routes'=>array(
                'App\Api\V1\Modules\Region\Http\Controllers\RegionController@index'=>array('method'=>'GET','name'=>'view'),
                'App\Api\V1\Modules\Region\Http\Controllers\RegionController@store'=>array('method'=>'POST','name'=>'add'),
                'App\Api\V1\Modules\Region\Http\Controllers\RegionController@export'=>array('method'=>'GET','name'=>'export'),
                'App\Api\V1\Modules\Region\Http\Controllers\RegionController@update'=>array('method'=>'PUT','name'=>'update'),
                'App\Api\V1\Modules\Region\Http\Controllers\RegionController@lists'=>array('method'=>'GET','name'=>'list'),
                'App\Api\V1\Modules\Region\Http\Controllers\RegionController@show'=>array('method'=>'GET','name'=>'detail'),
                'App\Api\V1\Modules\Region\Http\Controllers\RegionController@destroy'=>array('method'=>'DELETE','name'=>'delete'),
            )
    )
];