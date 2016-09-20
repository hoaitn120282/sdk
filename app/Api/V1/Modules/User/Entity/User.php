<?php

namespace App\Api\V1\Modules\User\Entity;

use App\Exceptions\ErrorDefinition;
use QSoftvn\Helper\Helper;
use QSoftvn\Models\WidgetBaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends WidgetBaseModel
{

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'users';
    /**
     * The name of the widget
     * @var string
     */
    public $widgetName = 'Users';
    /**
     * The version of API
     * @var string
     */
    public $apiVersion = 'V1';
    /**
     * Tell the widget to eager load with the data of relationship for list
     * @var array
     */
    public $defaultEagerLoads = ['createdByUser','updatedByUser','roles'];
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name','email','password','avatar','note','reset_token','reset_date','secret_key','permissions','permissions_custom','routes','routes_shared','routes_related','routes_custom','menu','active','created_by','updated_by'];
    /**
     * Validation rules
     * @var array
     */
    public $rules = [
        "name"=>"required",
        "email"=>"required",
        "password"=>"required"
    ];
    public $errorMessages = [
        'required' => ErrorDefinition::DATA_REQUIRED_VALIDATION_MESSAGE
    ];
    /**
     * The fields that you do not show on return data.
     * @var array
     */
    protected $hidden = array('password','reset_token','reset_date','secret_key');

    /**
     * Convert the data before update/insert
     * @param $data
     * @param string $action
     * @return mixed
     */
    public function convertUpdatableData($data, $action='add'){
        if($action == 'add' || $action == 'duplicate'){
            $data['password'] = Hash::make(Helper::getRandomString());
        }
        else if($action == 'edit' || $action == 'update'){
            if(isset($data['password'])){
                $data['password'] = Hash::make($data['password']);
            }
        }
        return $data;
    }
    public function createdByUser()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'created_by','id');
    }
    public function updatedByUser()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'updated_by','id');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Api\V1\Modules\Role\Entity\Role','user_roles')->wherePivot('from_date','<=', new \DateTime());
    }

    /**
     * Depending on design of database which may have user relate to bo, job title, job position we will merge all the account which a user can view/manipulate data from. Should always use simpleDecrypt to decrypt data.
     * @param $ableType
     * @param $permission
     * @return mixed
     */
    protected function mergeAbleAccounts($ableType, $permission){
        //$ableByRoles = json_decode($permission[$ableType.'_roles'],true);
        $ableByAccounts = json_decode($permission[$ableType.'_by_accounts']?Helper::simpleDecrypt($permission[$ableType.'_by_accounts']):"[]",true);
        return $ableByAccounts;
    }

    /**
     * This will read all permissions that a user got assigned through roles and serialize it to permissions and routes field in user account. You can customize it according to your business rule as long as it should return the same format as below.
     */
    public function normalizeUserPermission(){
        $roles = $this->roles()->get();
        $permissions = [];
        $accPermission = [];
        $accRoutes = [];
        $accSharedRoutes = [];
        $accRelatedRoutes = [];
        foreach($roles as $role){
            $permissions[] = $role->permissions()->get()->toArray();
        }
        foreach($permissions as $perm){
            foreach($perm as $permission){
                $routes = (json_decode(Helper::simpleDecrypt($permission['routes']),true));
                if(is_array($routes)){
                    if($permission['shared_by']){
                        $accSharedRoutes= array_merge($accSharedRoutes, $routes);
                    }
                    else if($permission['related_to']){
                        $accRelatedRoutes= array_merge($accRelatedRoutes, $routes);
                    }
                    else{
                        $accRoutes= array_merge($accRoutes, $routes);
                    }

                }
                $accPermission[$permission['widget']][]=array(
                    'validFrom' => $permission['valid_from'],
                    'validTo' => $permission['valid_to'],
                    'viewableFields' => json_decode($permission['viewable_fields']?Helper::simpleDecrypt($permission['viewable_fields']):"[]",true),
                    'viewableByAccounts' => $this->mergeAbleAccounts('viewable',$permission),
                    'viewableExceptAccounts' => json_decode($permission['viewable_except_accounts']?Helper::simpleDecrypt($permission['viewable_except_accounts']):"[]",true),
                    'viewableType' => $permission['viewable_type'],
                    'viewableConditions' =>json_decode($permission['viewable_conditions']?Helper::simpleDecrypt($permission['viewable_conditions']):"[]",true),
                    'viewableMaxRecord' => $permission['viewable_max_record'],
                    'editableFields' => json_decode($permission['editable_fields']?Helper::simpleDecrypt($permission['editable_fields']):"[]",true),
                    'editableByAccounts' => $this->mergeAbleAccounts('editable',$permission),
                    'editableExceptAccounts' => json_decode($permission['editable_except_accounts']?Helper::simpleDecrypt($permission['editable_except_accounts']):"[]",true),
                    'editableType' => $permission['editable_type'],
                    'editableConditions' =>json_decode($permission['editable_conditions']?Helper::simpleDecrypt($permission['editable_conditions']):"[]",true),
                    'deletableFields' => json_decode($permission['deletable_fields']?Helper::simpleDecrypt($permission['deletable_fields']):"[]",true),
                    'deletableByAccounts' => $this->mergeAbleAccounts('deletable',$permission),
                    'deletableExceptAccounts' => json_decode($permission['deletable_except_accounts']?Helper::simpleDecrypt($permission['deletable_except_accounts']):"[]",true),
                    'deletableType' => $permission['deletable_type'],
                    'deletableConditions' =>json_decode($permission['deletable_conditions']?Helper::simpleDecrypt($permission['deletable_conditions']):"[]",true),
                    'exportableFields' => json_decode($permission['exportable_fields']?Helper::simpleDecrypt($permission['exportable_fields']):"[]",true),
                    'exportableByAccounts' => $this->mergeAbleAccounts('exportable',$permission),
                    'exportableExceptAccounts' => json_decode($permission['exportable_except_accounts']?Helper::simpleDecrypt($permission['exportable_except_accounts']):"[]",true),
                    'exportableType' => $permission['exportable_type'],
                    'exportableConditions' =>json_decode($permission['exportable_conditions']?Helper::simpleDecrypt($permission['exportable_conditions']):"[]",true)
                );
            }
        }
        //Update the permissions to account.
        if(count($accPermission)>0){
            $this->update(array('permissions'=>Helper::simpleEncrypt(json_encode($accPermission))));
            //$this->update(array('permissions'=>json_encode($accPermission)));
        }
        else{
            $this->update(array('permissions'=>null));
        }
        if(count($accRoutes)>0){
            $this->update(array('routes'=>Helper::simpleEncrypt(json_encode($accRoutes))));
            //$this->update(array('routes'=>json_encode($accRoutes)));
        }
        else{
            $this->update(array('routes'=>null));
        }
        if(count($accSharedRoutes)>0){
            $this->update(array('routes_shared'=>Helper::simpleEncrypt(json_encode($accSharedRoutes))));
            //$this->update(array('routes_shared'=>json_encode($accSharedRoutes)));
        }
        else{
            $this->update(array('routes_shared'=>null));
        }
        if(count($accRelatedRoutes)>0){
            $this->update(array('routes_related'=>Helper::simpleEncrypt(json_encode($accRelatedRoutes))));
            //$this->update(array('routes_related'=>json_encode($accRelatedRoutes)));
        }
        else{
            $this->update(array('routes_related'=>null));
        }
    }
}