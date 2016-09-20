<?php

namespace QSoftvn\Models;


use QSoftvn\Helper\Helper;

/**
 * This class provide the implementation of the permission in QSDK Laravel
 *
 * QSDK Laravel can protect the resources from accessing the controller via routing and records as well. This class provide the convenient way for setting up the user permission.
 *
 * @package QSoftvn\Models
 */
class Permission extends BaseModel
{
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The name of the widget
     * @var string
     */
    public $widgetName = 'Permissions';

    /**
     * The version of API
     * @var string
     */
    public $apiVersion = 'V1';

    /**
     * Tell the widget to eager load with the data of relationship for list
     * @var array
     */
    public $eagerLoads = ['role','user'];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['valid_from','valid_to','routes','viewable_fields','viewable_roles','viewable_by_accounts','viewable_except_accounts','viewable_type','viewable_conditions','viewable_max_record','editable_fields','editable_roles','editable_by_accounts','editable_except_accounts','editable_type','editable_conditions','deletable_fields','deletable_roles','deletable_by_accounts','deletable_except_accounts','deletable_type','deletable_conditions','exportable_fields','exportable_roles','exportable_by_accounts','exportable_except_accounts','exportable_type','exportable_conditions','role_id','widget','shared_by','shared_at','created_by','updated_by','related_to'];

    /**
     * Validation rules
     * @var array
     */
    public static $rules = [
        "valid_from"=>"required",
        "viewable_type"=>"required",
        "editable_type"=>"required",
        "deletable_type"=>"required",
        "exportable_type"=>"required",
        "role_id"=>"required",
        "widget"=>"required"
    ];
    /**
     * The fields that you do not show on return data.
     * @var array
     */
    protected $hidden = array();


    public function role()
    {
        return $this->belongsTo('App\Api\V1\Modules\Role\Entity\Role', 'role_id','id');
    }
    public function user()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'shared_by','id');
    }
    public function createdByUser()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'created_by','id');
    }
    public function updatedByUser()
    {
        return $this->belongsTo('App\Api\V1\Modules\User\Entity\User', 'updated_by','id');
    }

    /**
     * Convert submitted permission data to database compliant
     *
     * See the Documentation included in QSDK Laravel Template project for data format.
     *
     * @param array $data
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    public static function normalizeData($data, $model, $roleId){
        $relatedPermissions = array();
        $modelName = $model->widgetName;
        $widgetsConfig = Helper::getWidgetList($model->apiVersion);
        $ableTypeDefaultValue = 1;

        //If module by pass the control record access then return the model builder itself.
        if(!isset($widgetsConfig[$modelName]['controlRecordAccess']) || (isset($widgetsConfig[$modelName]['controlRecordAccess']) && $widgetsConfig[$modelName]['controlRecordAccess']!==true)){
            $ableTypeDefaultValue = 3;
        }

        $fillable = ['valid_from','valid_to','routes','viewable_fields','viewable_roles','viewable_by_accounts','viewable_except_accounts','viewable_type','viewable_conditions','viewable_max_record','editable_fields','editable_roles','editable_by_accounts','editable_except_accounts','editable_type','editable_conditions','deletable_fields','deletable_roles','deletable_by_accounts','deletable_except_accounts','deletable_type','deletable_conditions','exportable_fields','exportable_roles','exportable_by_accounts','exportable_except_accounts','exportable_type','exportable_conditions','role_id','widget','shared_by','shared_at','created_by','updated_by','related_to'];
        $myAccount = app('Dingo\Api\Auth\Auth')->user();
        $myAccountId = $myAccount->id;

        if(isset($data['routes'])){
            $routes = $data['routes'];
            $allRoutes = Helper::getAllRoutes();
            foreach($routes as $route){
                $myRoute = $allRoutes[$route];
                foreach($myRoute['related'] as $w=>$d){
                    if(!isset($relatedPermissions[$w])){
                        $relatedPermissions[$w] = array();
                    }
                    array_push($relatedPermissions[$w], $d);
                }
            }
            $data['routes'] = Helper::simpleEncrypt(json_encode($data['routes']));
        }
        else{
            $relatedPermissions = -1;
        }

        $tags = array('viewable','editable','deletable','exportable');
        foreach($tags as $tag){
            if(isset($data[$tag.'Fields'])){
                $data[$tag.'Fields'] = Helper::simpleEncrypt(json_encode($data[$tag.'Fields']));
            }
            if(isset($data[$tag.'Roles'])){
                $data[$tag.'Roles'] = Helper::simpleEncrypt(json_encode($data[$tag.'Roles']));
            }
            if(isset($data[$tag.'ByAccounts'])){
                $data[$tag.'ByAccounts'] = Helper::simpleEncrypt(json_encode($data[$tag.'ByAccounts']));
            }
            if(isset($data[$tag.'ExceptAccounts'])){
                $data[$tag.'ExceptAccounts'] = Helper::simpleEncrypt(json_encode($data[$tag.'ExceptAccounts']));
            }
            if(isset($data[$tag.'Conditions'])){
                $data[$tag.'Conditions'] = Helper::simpleEncrypt(json_encode($data[$tag.'Conditions']));
            }
        }

        //Transform data to underscore to update
        $postObj = [];
        foreach($data as $key=>$value){
            $newKey = \Helper::camelCaseToUnderscore($key);
            if(in_array($newKey,$fillable)){
                $postObj[$newKey] = $value;
            }
        }
        $data = $postObj;

        $data['created_by']=$myAccountId;
        $data['updated_by']=$myAccountId;
        $data['valid_from'] = new \DateTime();
        $data['viewable_type'] = $ableTypeDefaultValue;
        $data['editable_type'] = $ableTypeDefaultValue;
        $data['deletable_type'] = $ableTypeDefaultValue;
        $data['exportable_type'] = $ableTypeDefaultValue;

        if($relatedPermissions!==-1){
            self::setupRelatedPermissions($data['widget'], $roleId, $relatedPermissions, $ableTypeDefaultValue);
        }

        return $data;
    }
    protected static function setupRelatedPermissions($originWidget, $roleId, $relatedPermissions, $ableTypeDefaultValue){
        $tags = array('viewable','editable','deletable','exportable');
        $widgetList = Helper::getWidgetList();
        $myAccount = app('Dingo\Api\Auth\Auth')->user();
        $myAccountId = $myAccount->id;
        //Delete all related permissions
        Permission::where('role_id','=',$roleId)->whereNull('shared_by')->where('related_to','=',$originWidget)->delete();
        foreach($relatedPermissions as $widget=>$dArray){
            $myData = [];
            $myData['widget'] = $widget;
            $myData['routes'] = [];
            $widgetConfig = $widgetList[$widget];
            foreach($dArray as $rD){
                foreach($rD as $r=>$d){
                    array_push($myData['routes'],str_replace('\\','__',$r));
                    foreach($tags as $tag){
                        if(!isset($myData[$tag.'_fields'])){
                            $myData[$tag.'_fields'] = [];
                        }
                        if(isset($d['fields']) && is_array($d['fields'])){
                            if($d['fields'][0]!=='*'){
                                $myData[$tag.'_fields'] = array_unique(array_merge($myData[$tag.'_fields'],$d['fields']));
                            }
                        }
                    }
                }
            }
            foreach($tags as $tag){
                if(!isset($myData[$tag.'_fields']) || count($myData[$tag.'_fields'])==0){
                    $myData[$tag.'_fields'] = $widgetConfig['viewableFields'];
                }
                $myData[$tag.'_fields'] = Helper::simpleEncrypt(json_encode($myData[$tag.'_fields']));
            }
            $myData['routes'] = Helper::simpleEncrypt(json_encode(array_unique($myData['routes'])));
            $myData['created_by']=$myAccountId;
            $myData['updated_by']=$myAccountId;
            $myData['valid_from'] = new \DateTime();
            $myData['viewable_type'] = $ableTypeDefaultValue;
            $myData['editable_type'] = $ableTypeDefaultValue;
            $myData['deletable_type'] = $ableTypeDefaultValue;
            $myData['exportable_type'] = $ableTypeDefaultValue;
            $myData['related_to'] = $originWidget;

            Permission::updateOrCreate(array('widget'=>$widget,'role_id'=>$roleId, 'shared_by'=>null, 'related_to'=>$originWidget),$myData);
        }
    }
}