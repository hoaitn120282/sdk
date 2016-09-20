<?php

namespace App\Api\V1\Modules\City\Entity;

use App\Exceptions\ErrorDefinition;
use QSoftvn\Models\WidgetBaseModel;

class City extends WidgetBaseModel
{
    
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'cities';
    /**
     * The name of the widget
     * @var string
     */
    public $widgetName = 'Cities';
    /**
     * The version of API
     * @var string
     */
    public $apiVersion = 'V1';
    /**
     * Tell the widget to eager load with the data of relationship for list
     * @var array
     */
    public $defaultEagerLoads = ['region','createdByUser','updatedByUser'];
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name','latitude','longitude','region_id','created_by','updated_by'];
    /**
     * Validation rules
     * @var array
     */
    public $rules = [
        "name"=>"required",
        "region_id"=>"required"
    ];
    public $errorMessages = [
        'required' => ErrorDefinition::DATA_REQUIRED_VALIDATION_MESSAGE,
    ];
    /**
     * The fields that you do not show on return data.
     * @var array
     */
    protected $hidden = array();
    
    public function region()
    {
        return $this->belongsTo('App\Api\V1\Modules\Region\Entity\Region', 'region_id','id');
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
