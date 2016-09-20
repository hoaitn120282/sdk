<?php
    return [
        'Customers'=>array(
            'description'=>'List of customers',
            'public'=>false,
            'allowAdd'=>true,
            'viewableFields'=>array('customers.id','customers.name','customers.short_name','customers.reg_number','customers.reg_date','customers.tax_number','customers.reg_address','customers.email','customers.phone','customers.fax','customers.website','customers.logo','customers.billing_to','customers.billing_address','customers.billing_email','customers.country_id','customers.customer_source_id','customers.business_domain_id','customers.verified','customers.created_at','customers.created_by','customers.updated_at','customers.updated_by'),
            'controlRecordAccess'=>true,
            'hasPrivateRecords'=>false,
            'sharable'=>true,
            'isSpecial'=>false,
            'routes'=>array(
                'App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@index'=>array('method'=>'GET','name'=>'view'),
                'App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@store'=>array('method'=>'POST','name'=>'add'),
                'App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@update'=>array('method'=>'PUT','name'=>'update'),
                'App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@export'=>array('method'=>'GET','name'=>'export'),
                'App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@lists'=>array('method'=>'GET','name'=>'list'),
                'App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@show'=>array('method'=>'GET','name'=>'detail'),
                'App\Api\V1\Modules\Customer\Http\Controllers\CustomerController@destroy'=>array('method'=>'DELETE','name'=>'delete')
            )
    )
];