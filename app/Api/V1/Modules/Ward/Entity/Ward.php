<?php

namespace App\Api\V1\Modules\Ward\Entity;

use QSoftvn\Models\WidgetBaseModel;

class Ward extends WidgetBaseModel
{
    
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'wards';
    /**
     * The name of the widget
     * @var string
     */
    public $widgetName = 'Wards';
    /**
     * The version of API
     * @var string
     */
    public $apiVersion = 'V1';
    /**
     * Tell the widget to eager load with the data of relationship for list
     * @var array
     */
    public $defaultEagerLoads = ['district'];
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name','latitude','longitude','district_id','created_by','updated_by'];
    /**
     * Validation rules
     * @var array
     */
    public $rules = [
        "name"=>"required",
        "district_id"=>"required"
    ];
    public $errorMessages = [
        'required' => '":attribute" is not allowed to be blank',
    ];
    /**
     * The fields that you do not show on return data.
     * @var array
     */
    protected $hidden = array();
    
    public function district()
    {
        return $this->belongsTo('App\Api\V1\Modules\District\Entity\District', 'district_id','id');
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
