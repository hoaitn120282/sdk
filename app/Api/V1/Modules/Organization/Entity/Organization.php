<?php

namespace App\Api\V1\Modules\Organization\Entity;

use QSoftvn\Models\TreeBaseModel;

class Organization extends TreeBaseModel
{
    /**
     * The field that distinct trees when storing multiple trees in the same table
     * @var null
     */
    public $treeScopeField = 'type';
    /**
     * The default value for the scopeField that the tree should be filtered by default.
     * @var null
     */
    public $defaultTreeScopeValue = 'organization';
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'roles';
    /**
     * The name of the widget
     * @var string
     */
    public $widgetName = 'Organizations';
    /**
     * The version of API
     * @var string
     */
    public $apiVersion = 'V1';
    /**
     * Tell the widget to eager load with the data of relationship for list
     * @var array
     */
    public $defaultEagerLoads = [];
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name','description','is_active','type','created_by','updated_by'];
    /**
     * Validation rules
     * @var array
     */
    public $rules = [
        "name"=>"required",
        "description"=>"required"
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
