<?php

namespace App\Api\V1\Modules\Customer\Entity;

use QSoftvn\Models\WidgetBaseModel;
use App\Exceptions\ErrorDefinition;

class Customer extends WidgetBaseModel
{
    
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'customers';
    /**
     * The name of the widget
     * @var string
     */
    public $widgetName = 'Customers';
    /**
     * The version of API
     * @var string
     */
    public $apiVersion = 'V1';
    /**
     * Tell the widget to eager load with the data of relationship for list
     * @var array
     */
    public $defaultEagerLoads = ['country','customer_source','business_domain'];
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name','short_name','reg_number','reg_date','tax_number','reg_address','email','phone','fax','website','logo','billing_to','billing_address','billing_email','country_id','customer_source_id','business_domain_id','verified','created_by','updated_by'];
    /**
     * Validation rules
     * @var array
     */
    public $rules = [
        "name"=>"required",
        "short_name"=>"required",
        "email"=>"required",
        "phone"=>"required",
        "country_id"=>"required",
        "customer_source_id"=>"required",
        "business_domain_id"=>"required",
        "verified"=>"required"
    ];
    /**
     * Validation messages
     * @var array
     */
    public $errorMessages = [
        'required' => ErrorDefinition::DATA_REQUIRED_VALIDATION_MESSAGE,
    ];
    /**
     * The fields that you do not show on return data.
     * @var array
     */
    protected $hidden = array();
    /**
     * Convert the data before update/insert
     * @param $data
     * @param string $action
     * @return mixed
     */
    public function convertUpdatableData($data, $action='add'){
        return $data;
    }
    
    public function country()
    {
        return $this->belongsTo('App\Api\V1\Modules\Country\Entity\Country', 'country_id','id');
    }
    public function customer_source()
    {
        return $this->belongsTo('App\Api\V1\Modules\CustomerSource\Entity\CustomerSource', 'customer_source_id','id');
    }
    public function business_domain()
    {
        return $this->belongsTo('App\Api\V1\Modules\BusinessDomain\Entity\BusinessDomain', 'business_domain_id','id');
    }
    public function createdByUser()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'created_by','id');
    }
    public function updatedByUser()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'updated_by','id');
    }
    
}
