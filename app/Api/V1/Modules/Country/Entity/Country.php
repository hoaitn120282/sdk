<?php

namespace App\Api\V1\Modules\Country\Entity;

use QSoftvn\Models\WidgetBaseModel;

class Country extends WidgetBaseModel
{
    
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'countries';
    /**
     * The name of the widget
     * @var string
     */
    public $widgetName = 'Countries';
    /**
     * The version of API
     * @var string
     */
    public $apiVersion = 'V1';
    /**
     * Tell the widget to eager load with the data of relationship for list
     * @var array
     */
    public $defaultEagerLoads = ['createdByUser','updatedByUser'];
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name','alpha2','alpha3','latitude','longitude','created_by','updated_by'];
    /**
     * Validation rules
     * @var array
     */
    public $rules = [
        "name"=>"required",
        "alpha2"=>"required"
    ];
    public $errorMessages = [
        'required' => '":attribute" is not allowed to be blank',
    ];
    /**
     * The fields that you do not show on return data.
     * @var array
     */
    protected $hidden = array();
    
    public function createdByUser()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'created_by','id');
    }
    public function updatedByUser()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'updated_by','id');
    }
    
}
