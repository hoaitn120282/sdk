<?php

namespace QSoftvn\Modules\UserRole\Entity;

use QSoftvn\Models\BaseModel;
use QSoftvn\Models\WidgetBaseModel;
use App\Exceptions\ErrorDefinition;

/**
 * This class provide a model base for user role
 * @package QSoftvn\Modules\UserRole\Entity
 */
class UserRole extends BaseModel
{
    
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'user_roles';
    /**
     * The name of the widget
     * @var string
     */
    public $widgetName = 'UserRoles';
    /**
     * The version of API
     * @var string
     */
    public $apiVersion = 'V1';
    /**
     * Tell the widget to eager load with the data of relationship for list
     * @var array
     */
    public $defaultEagerLoads = ['user','role'];
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['user_id','role_id','from_date','to_date','note','created_by','updated_by'];
    /**
     * Validation rules
     * @var array
     */
    public $rules = [
        "user_id"=>"required",
        "role_id"=>"required",
        "from_date"=>"required"
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
    
    public function user()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'user_id','id');
    }
    public function role()
    {
        return $this->belongsTo('App\Api\V1\Modules\Role\Entity\Role', 'role_id','id');
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
