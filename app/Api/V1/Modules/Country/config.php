<?php
    return [
        'Countries'=>array(
            'description'=>'List of countries',
            'public'=>false,
            'isStandaloneWidget'=>true,
            'allowAdd'=>true,
            'viewableFields'=>array('countries.id','countries.name','countries.alpha2','countries.alpha3','countries.latitude','countries.longitude', 'countries.created_at', 'countries.updated_at', 'countries.created_by', 'countries.updated_by'),
            'controlRecordAccess'=>false,
            'hasPrivateRecords'=>false,
            'sharable'=>false,
            'isSpecial'=>false,
            'routes'=>array(
                'App\Api\V1\Modules\Country\Http\Controllers\CountryController@index'=>array('method'=>'GET','name'=>'view'),
                'App\Api\V1\Modules\Country\Http\Controllers\CountryController@store'=>array('method'=>'POST','name'=>'add'),
                'App\Api\V1\Modules\Country\Http\Controllers\CountryController@export'=>array('method'=>'GET','name'=>'export'),
                'App\Api\V1\Modules\Country\Http\Controllers\CountryController@update'=>array('method'=>'PUT','name'=>'update'),
                'App\Api\V1\Modules\Country\Http\Controllers\CountryController@lists'=>array('method'=>'GET','name'=>'list'),
                'App\Api\V1\Modules\Country\Http\Controllers\CountryController@show'=>array('method'=>'GET','name'=>'detail'),
                'App\Api\V1\Modules\Country\Http\Controllers\CountryController@destroy'=>array('method'=>'DELETE','name'=>'delete'),
            )
    )
];