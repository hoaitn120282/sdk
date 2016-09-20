<?php
namespace QSoftvn\Models;

use QSoftvn\Helper\Helper;
use Symfony\Component\Process\Exception\LogicException;

/**
 * This class provide abstraction for a widget model
 * @package QSoftvn\Models
 */
abstract class WidgetBaseModel extends BaseModel
{
    /**
     * @ignore
     * @var array
     */
    protected $appends = ['editable','deletable','exportable'];

    /**
     * Config whether the model result will be loaded with editable, deletable, exportable or not
     * @var bool
     */
    public static $withoutAppends = false;
    /**
     * @ignore
     * Add the constructor to force the property widgetName for every Model extended from this class so that we know which widget it is for rights checking
     * @param array $attributes
     */
    public function __construct(array $attributes = []) {
        if(!isset($this->widgetName)){
            throw new LogicException(get_class($this) . ' must have widgetName property');
        }
        if(!isset($this->apiVersion)){
            throw new LogicException(get_class($this) . ' must have apiVersion property');
        }
        parent::__construct($attributes);
    }

    /**
     * @ignore
     * @return array
     */
    protected function getArrayableAppends()
    {
        if(self::$withoutAppends){
            return [];
        }
        return parent::getArrayableAppends();
    }

    /**
     * @ignore
     * @return bool
     */
    public function getEditableAttribute()
    {
        if(isset($this->attributes['editable'])){
            return $this->attributes['editable'];
        }
        return true;
    }

    /**
     * @ignore
     * @return bool
     */
    public function getDeletableAttribute()
    {
        if(isset($this->attributes['deletable'])){
            return $this->attributes['deletable'];
        }
        return true;
    }

    /**
     * @ignore
     * @return bool
     */
    public function getExportableAttribute()
    {
        if(isset($this->attributes['exportable'])){
            return $this->attributes['exportable'];
        }
        return true;
    }

    /**
     * Query scope for viewing with permissions
     * @param $query
     * @return mixed
     */
    public function scopeViewPermissions($query){
        return $query = $this->applyWidgetQueryConditions($query);
    }

    /**
     * Query scope for exporting with permissions
     * @param $query
     * @return mixed
     */
    public function scopeExportPermissions($query){
        return $query = $this->applyWidgetQueryConditions($query,'export');
    }

    /**
     * Query scope for updating with permissions
     * @param $query
     * @return mixed
     */
    public function scopeUpdatePermissions($query){
        return $query = $this->applyWidgetQueryConditions($query, 'edit');
    }

    /**
     * Query scope for deleting with permissions
     * @param $query
     * @return mixed
     */
    public function scopeDeletePermissions($query){
        return $query = $this->applyWidgetQueryConditions($query, 'delete');
    }

    /**
     * Build where for query. By default user can only view his created record unless the entity is public or no control on record is set on the config file
     * @param \Illuminate\Database\Eloquent\Model $modelBuilder
     * @param string $action
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function applyWidgetQueryConditions($modelBuilder, $action='view'){
        if(Helper::isSuperAdmin()==true){
            return $modelBuilder;
        }
        else {
            $model = $this;
            $modelName = $model->widgetName;
            $tableName = $model->getTable();
            $myAccount = app('Dingo\Api\Auth\Auth')->user();
            $myAccountId = $myAccount->id;
            $rightsConfig = Helper::getRightsConfig($modelName);
            $widgetsConfig = Helper::getWidgetList($model->apiVersion);
            $keyToCheck = 'viewable';
            $exceptWheres = [];

            //If module by pass the control record access then return the model builder itself.
            if(!isset($widgetsConfig[$modelName]['controlRecordAccess']) || (isset($widgetsConfig[$modelName]['controlRecordAccess']) && $widgetsConfig[$modelName]['controlRecordAccess']!==true)){
                return $modelBuilder;
            }

            if($action=='delete'){
                $keyToCheck = 'deletable';
            }
            else if($action=='edit'){
                $keyToCheck = 'editable';
            }
            else if($action=='export'){
                $keyToCheck = 'exportable';
            }

            //By default user can edit/delete records created by himself
            $modelBuilder->whereNested(function($theBuilder) use ($rightsConfig, $widgetsConfig, $myAccountId, $modelName, &$exceptWheres,$keyToCheck,$tableName){
                $theBuilder->where($tableName.'.created_by','=',$myAccountId);
                foreach($rightsConfig as $rights){
                    $wheres = [];
                    //Check rights without conditions
                    if ($rights[$keyToCheck.'Type'] == 1 || $rights[$keyToCheck.'Type'] == 2) {
                        //He cannot view records created by excepted accounts
                        if (count($rights[$keyToCheck.'ExceptAccounts']) > 0) {
                            array_push($exceptWheres, array('whereNotIn', $tableName.'.created_by', $rights[$keyToCheck.'ExceptAccounts']));
                        }

                        //If he can view all non-private records
                        if ($rights[$keyToCheck.'Type'] == 2) {
                            if ($widgetsConfig[$modelName]['hasPrivateRecords'] === true) {
                                //Allow only view non-private record
                                array_push($wheres, array('where', 'is_private', '=', false));
                            }
                            else{
                                //If not, he can view everything
                                array_push($wheres, array('whereRaw', '1=1'));
                            }
                        }
                        else if ($rights[$keyToCheck.'Type'] == 1) {
                            //He can view records created by specified accounts
                            if (count($rights[$keyToCheck.'ByAccounts']) > 0) {
                                array_push($wheres, array('whereIn', $tableName.'.created_by', $rights[$keyToCheck.'ByAccounts']));
                            }
                        }
                    }
                    else{
                        //He can view everything
                        array_push($wheres, array('whereRaw', '1=1'));
                    }
                    $theBuilder->whereNested(function ($outsideQuery) use ($wheres, $rights, $keyToCheck){
                        //Add where for included case
                        $outsideQuery->whereNested(function ($query) use ($wheres) {
                            foreach ($wheres as $where) {
                                if ($where[0] == 'whereNotIn') {
                                    $query->orWhere(function ($q) use ($where) {
                                        $q->whereNotIn($where[1], $where[2]);
                                    });
                                } else if ($where[0] == 'whereIn') {
                                    $query->orWhere(function ($q) use ($where) {
                                        $q->whereIn($where[1], $where[2]);
                                    });
                                }
                                else if($where[0]=='whereRaw'){
                                    $query->orWhere(function ($q) use ($where) {
                                        $q->whereRaw($where[1]);
                                    });
                                }
                                else {
                                    $query->orWhere($where[1], $where[2], $where[3]);
                                }
                            }

                        });

                        //Check conditions
                        if (count($rights[$keyToCheck.'Conditions']) > 0) {
                            $whereConditions = $rights[$keyToCheck.'Conditions'];
                            $outsideQuery->whereNested(function ($query) use ($whereConditions) {
                                if(isset($whereConditions[1]) && is_array($whereConditions[1])){
                                    foreach ($whereConditions as $condition){
                                        $query = $this->applyCondition($query, $condition);
                                    }
                                }
                                else{
                                    $query = $this->applyCondition($query, $whereConditions);
                                }

                            });

                        }
                    },'or');
                }
            });
            //Add where for excluded case
            $modelBuilder->whereNested(function ($query) use ($exceptWheres) {
                foreach ($exceptWheres as $where) {
                    if ($where[0] == 'whereNotIn') {
                        $query->orWhere(function ($q) use ($where) {
                            $q->whereNotIn($where[1], $where[2]);
                        });
                    } else if ($where[0] == 'whereIn') {
                        $query->orWhere(function ($q) use ($where) {
                            $q->whereIn($where[1], $where[2]);
                        });
                    } else {
                        $query->orWhere($where[1], $where[2], $where[3]);
                    }
                }
            });
        }
        return $modelBuilder;
    }

    /**
     * Convert condition array to the query conditions for viewable, editable, deletable
     * @param $modelBuilder
     * @param $condition
     * @return mixed
     */
    protected function applyCondition($modelBuilder, $condition)
    {
        $config = app('config');
        $dbDriver = $config['database']['default'];
        if (is_array($condition) && count($condition)>0) {
            $whereType = $condition[0];
            $whereDefinition = $condition[1];
            if (is_array($whereDefinition)) {
                $modelBuilder->whereNested(function ($query) use ($modelBuilder, $whereDefinition) {
                    if(isset($whereDefinition[1]) && is_array($whereDefinition[1])){
                        foreach ($whereDefinition as $subWhere) {
                            $query = $this->applyCondition($query, $subWhere);
                        }
                    }
                    else{
                        $query = $this->applyCondition($query, $whereDefinition);
                    }
                });
            } else {
                switch ($whereType) {
                    case 'where': {
                        $field = $condition[1];
                        $operator = $condition[2];
                        $value = $condition[3];
                        if ($operator == 'like' || $operator == 'ilike') {
                            $value = '%' . strtolower($value) . '%';
                            if($dbDriver == 'pgsql'){
                                $operator = 'ilike';
                            }
                            else{
                                $operator = 'like';
                            }

                        }
                        $modelBuilder->where($field, $operator, $value);
                        break;
                    }
                    case 'orWhere': {
                        $field = $condition[1];
                        $operator = $condition[2];
                        $value = $condition[3];
                        if ($operator == 'like') {
                            $value = '%' . strtolower($value) . '%';
                            if($dbDriver == 'pgsql'){
                                $operator = 'ilike';
                            }
                            else{
                                $operator = 'like';
                            }

                        }
                        $modelBuilder->orWhere($field, $operator, $value);
                        break;
                    }
                    case 'whereBetween': {
                        $field = $condition[1];
                        $value1 = $condition[2];
                        $value2 = $condition[3];
                        $modelBuilder->whereBetween($field, $value1, $value2);
                        break;
                    }
                    case 'whereNotBetween': {
                        $field = $condition[1];
                        $value1 = $condition[2];
                        $value2 = $condition[3];
                        $modelBuilder->whereNotBetween($field, $value1, $value2);
                        break;
                    }
                    case 'whereIn': {
                        $field = $condition[1];
                        $value = (array)$condition[2];
                        $modelBuilder->whereIn($field, $value);
                        break;
                    }
                    case 'whereNotIn': {
                        $field = $condition[1];
                        $value = (array)$condition[2];
                        $modelBuilder->whereNotIn($field, $value);
                        break;
                    }
                    case 'whereNull': {
                        $field = $condition[1];
                        $modelBuilder->whereNull($field);
                        break;
                    }
                    case 'whereNotNull': {
                        $field = $condition[1];
                        $modelBuilder->whereNotNull($field);
                        break;
                    }
                    //By pass the case whereExists because it is too complex.
                    default: {
                        break;
                    }
                }
            }
        }
        return $modelBuilder;
    }
}