<?php
/**
 * This is the trait for controller that wants the predefined action of listing permission for a role and updatePermission for a role
 *  In order to route the permission, add following to route
    $api->get('roles/{id}/permissions','App\Api\V1\Modules\Role\Http\Controllers\RoleController@permission');
    $api->put('roles/{id}/permissions/{widget}','App\Api\V1\Modules\Role\Http\Controllers\RoleController@updatePermission');
 */
namespace QSoftvn\Controllers;

use QSoftvn\Helper\Helper;
use QSoftvn\Models\Permission;
use QSoftvn\Modules\UserRole\Entity\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * This trait provides convenient way to turn a controller into a permission controller. When use this trait, a controller will have permission action, updatePermission action ready made
 *
 * If update a role, all users have that role will be updated automatically
 *
 * @package QSoftvn\Controllers
 */
trait PermissionTrait{
    /**
     * The permission action that list permissions of a role
     * @param $id
     * @return mixed
     */
    public function permission($id){
        $model = $this->repository->getModel();
        $widgetList = Helper::getWidgetList($model->apiVersion);
        $data = array('data'=>[]);
        $existingPermissions = [];
        if($id > 0){
            $permissions = DB::table('permissions')->where('role_id','=',$id)->whereNull('shared_by')->whereNull('related_to')->get();
            foreach($permissions as $permission){
                $existingPermissions[$permission->widget] = $permission;
            }
        }
        foreach($widgetList as $widget=>$widgetConfig){
            if($widgetConfig['public']===false && $widgetConfig['isStandaloneWidget']===true){
                $data['data'][]=array(
                    'widget'=>$widget,
                    'description'=>$widgetConfig['description'],
                    'routes'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->routes?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->routes),true):array()):array(),
                    'validFrom'=>isset($existingPermissions[$widget])?$existingPermissions[$widget]->valid_from:new \DateTime(),
                    'validTo'=>isset($existingPermissions[$widget])?$existingPermissions[$widget]->valid_to:null,
                    'viewableFields'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->viewable_fields?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->viewable_fields),true):array()):array(),
                    'viewableRoles'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->viewable_roles?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->viewable_roles),true):array()):array(),
                    'viewableByAccounts'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->viewable_by_accounts?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->viewable_by_accounts),true):array()):array(),
                    'viewableExceptAccounts'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->viewable_except_accounts?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->viewable_except_accounts),true):array()):array(),
                    'viewableType'=>isset($existingPermissions[$widget])?$existingPermissions[$widget]->viewable_type:1,
                    'viewableConditions'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->viewable_conditions?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->viewable_conditions),true):array()):array(),
                    'viewableMaxRecord'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->viewable_max_record?$existingPermissions[$widget]->viewable_max_record:0):0,
                    'editableFields'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->editable_fields?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->editable_fields),true):array()):array(),
                    'editableRoles'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->editable_roles?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->editable_roles),true):array()):array(),
                    'editableByAccounts'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->editable_by_accounts?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->editable_by_accounts),true):array()):array(),
                    'editableExceptAccounts'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->editable_except_accounts?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->editable_except_accounts),true):array()):array(),
                    'editableType'=>isset($existingPermissions[$widget])?$existingPermissions[$widget]->editable_type:1,
                    'editableConditions'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->editable_conditions?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->editable_conditions),true):array()):array(),
                    'deletableFields'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->deletable_fields?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->deletable_fields),true):array()):array(),
                    'deletableRoles'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->deletable_roles?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->deletable_roles),true):array()):array(),
                    'deletableByAccounts'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->deletable_by_accounts?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->deletable_by_accounts),true):array()):array(),
                    'deletableExceptAccounts'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->deletable_except_accounts?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->deletable_except_accounts),true):array()):array(),
                    'deletableType'=>isset($existingPermissions[$widget])?$existingPermissions[$widget]->deletable_type:1,
                    'deletableConditions'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->deletable_conditions?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->deletable_conditions),true):array()):array(),
                    'exportableFields'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->exportable_fields?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->exportable_fields),true):array()):array(),
                    'exportableRoles'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->exportable_roles?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->exportable_roles),true):array()):array(),
                    'exportableByAccounts'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->exportable_by_accounts?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->exportable_by_accounts),true):array()):array(),
                    'exportableExceptAccounts'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->exportable_except_accounts?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->exportable_except_accounts),true):array()):array(),
                    'exportableType'=>isset($existingPermissions[$widget])?$existingPermissions[$widget]->exportable_type:1,
                    'exportableConditions'=>isset($existingPermissions[$widget])?($existingPermissions[$widget]->exportable_conditions?json_decode(Helper::simpleDecrypt($existingPermissions[$widget]->exportable_conditions),true):array()):array(),
                    'isSpecial'=>$widgetConfig['isSpecial'],
                    'controlRecordAccess'=>(isset($widgetConfig['controlRecordAccess'])&&$widgetConfig['controlRecordAccess']===true?true:false)
                );
            }
        }
        return $this->response->array($data);
    }

    /**
     * Update the permission lists of a role. This is a controller's action
     * @param $roleId
     * @param $widget
     * @param Request $request
     * @return mixed
     */
    public function updatePermission($roleId, $widget, Request $request){
        $allRequest = $request->all();
        $model = $this->repository->getModel();
        //Process multiple updates
        if(isset($allRequest[0])){
            foreach($allRequest as $myRequest){
                if(!is_array($myRequest)){
                    $myRequest = json_decode($myRequest, true);
                }
                if(isset($myRequest['widget'])){
                    DB::transaction(function() use ($myRequest, $model, $roleId){
                        $myRequest = Permission::normalizeData($myRequest,$model, $roleId);
                        Permission::updateOrCreate(array('widget'=>$myRequest['widget'],'role_id'=>$roleId, 'shared_by'=>null),$myRequest);
                    });
                }
            }
        }
        else{
            if(!is_array($allRequest)){
                $allRequest = json_decode($allRequest, true);
            }
            DB::transaction(function() use ($allRequest, $model, $roleId, $widget){
                $allRequest = Permission::normalizeData($allRequest,$model, $roleId);
                Permission::updateOrCreate(array('widget'=>$widget,'role_id'=>$roleId),$allRequest);
            });

        }
        $this->updateUpdateAccountPermissions($roleId);

        return $this->response->array(array())->setStatusCode(200);
    }

    /**
     * Update all accounts that has this role.
     * @param $roleId
     */
    protected function updateUpdateAccountPermissions($roleId){
        $userRoles = UserRole::where('role_id','=',$roleId)->get();
        foreach($userRoles as $userRole){
            $accounts = $userRole->user()->get();
            foreach($accounts as $user){
                $user->normalizeUserPermission();
            }
        }
    }
}