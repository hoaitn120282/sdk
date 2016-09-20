<?php
namespace QSoftvn\Repositories;

use App\Exceptions\ErrorDefinition;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use QSoftvn\Helper\Helper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Process\Exception\LogicException;

/**
 * Class WidgetBaseRepository
 *
 * The concept of widget is that each entity which wrap a table will be treated as a widget. Within a widget, user can view, edit, delete, add new data.
 *
 * Viewing widget data will depend on the access rights of a user. By default, user can only view data they created, unless the entity is public or there is no control of record access set in the entity config file
 *
 * This class will check the access rights of the user and try to output or manipulate the accessible data. Developers do not have to worry about checking access rights of the user when working with this class.
 *
 * @package QSoftvn\Repositories
 */
abstract class WidgetBaseRepository extends BaseRepository
{
    /**
     * @ignore
     */
    protected $relFilters = [];

    /**
     * @ignore
     */
    protected $relSorters = [];

    /**
     * @ignore
     */
    protected $modelAfterQueryCustomBuilt = null;

    /**
     * @ignore
     */
    protected $_setScopes = [];

    /**
     * Get all viewable fields from rightsConfig.
     *
     * $rightConfig can be retrieved from:
     *
     *     $rightsConfig = Helper::getRightsConfig($widgetName);
     *
     * @param $rightsConfig
     * @return array The list of fields that the current user can view.
     */
    public function getViewableFields($rightsConfig){
        $model = $this->model;
        $widgetName = $model->widgetName;
        $widgetList = Helper::getWidgetList($model->apiVersion);
        $widgetFields = $widgetList[$widgetName]['viewableFields'];
        $viewableFields = [];
        if(Helper::isSuperAdmin()===true){
            return $widgetFields;
        }
        if(!isset($widgetList[$widgetName]['controlRecordAccess']) || (isset($widgetList[$widgetName]['controlRecordAccess']) && $widgetList[$widgetName]['controlRecordAccess']!==true)){
            return $widgetFields;
        }
        foreach($rightsConfig as $config){
            $fields = $config['viewableFields'];
            if($fields == array('*')){
                return $widgetFields;
            }
            else{
                $viewableFields = array_unique(array_merge($viewableFields, $fields));
            }
        }
        foreach($viewableFields as $key=>$field){
            if(!in_array($field,$widgetFields)){
                unset($viewableFields[$key]);
            }
        }
        return $viewableFields;
    }

    /**
     * Get all editable fields from rightsConfig.
     *
     * $rightConfig can be retrieved from:
     *
     *     $rightsConfig = Helper::getRightsConfig($widgetName);
     *
     * @param $rightsConfig
     * @return array The list of fields that the current user can edit.
     */
    public function getEditableFields($rightsConfig){
        $model = $this->model;
        $tableName = $model->getTable();
        $widgetName = $model->widgetName;
        $widgetList = Helper::getWidgetList($model->apiVersion);
        $editableFields = [];
        if(Helper::isSuperAdmin()===true){
            return array('*');
        }
        if(!isset($widgetList[$widgetName]['controlRecordAccess']) || (isset($widgetList[$widgetName]['controlRecordAccess']) && $widgetList[$widgetName]['controlRecordAccess']!==true)){
            return array('*');
        }
        foreach($rightsConfig as $config){
            $fields = $config['editableFields'];
            if($fields == array('*')){
                return array('*');
            }
            else{
                $fields = str_replace($tableName.'.','',$fields);
                $editableFields = array_unique(array_merge($editableFields, $fields));
            }
        }
        return $editableFields;
    }

    /**
     * Get all editable fields from rightsConfig in camel case format.
     *
     * $rightConfig can be retrieved from:
     *
     *     $rightsConfig = Helper::getRightsConfig($widgetName);
     *
     * @param $rightsConfig
     * @return array The list of fields that the current user can edit.
     */
    public function getEditableFieldsInCamelCase($rightsConfig){
        $model = $this->model;
        $tableName = $model->getTable();
        $widgetName = $model->widgetName;
        $widgetList = Helper::getWidgetList($model->apiVersion);
        $editableFields = [];
        if(Helper::isSuperAdmin()===true){
            return array('*');
        }
        if(!isset($widgetList[$widgetName]['controlRecordAccess']) || (isset($widgetList[$widgetName]['controlRecordAccess']) && $widgetList[$widgetName]['controlRecordAccess']!==true)){
            return array('*');
        }
        foreach($rightsConfig as $config){
            $fields = $config['editableFields'];
            if($fields == array('*')){
                return array('*');
            }
            else{
                foreach($fields as $field){
                    if (preg_match('/_id$/',$field)){
                        $myField = str_replace($tableName.'.','',$field);
                    }
                    else{
                        $myField = Helper::underscoreToCamelCase(str_replace($tableName.'.','',$field));
                    }
                    array_push($editableFields, $myField);
                }
            }
        }
        return array_unique($editableFields);
    }
    /**
     * Get all exportable fields from rightsConfig.
     *
     * $rightConfig can be retrieved from:
     *
     *     $rightsConfig = Helper::getRightsConfig($widgetName);
     *
     * @param $rightsConfig
     * @return array The list of fields that the current user can export.
     */
    public function getExportableFields($rightsConfig){
        $model = $this->model;
        $widgetName = $model->widgetName;
        $widgetList = Helper::getWidgetList($model->apiVersion);
        $widgetFields = $widgetList[$widgetName]['viewableFields'];
        $exportableFields = [];
        if(Helper::isSuperAdmin()===true){
            return $widgetFields;
        }
        if(!isset($widgetList[$widgetName]['controlRecordAccess']) || (isset($widgetList[$widgetName]['controlRecordAccess']) && $widgetList[$widgetName]['controlRecordAccess']!==true)){
            return $widgetFields;
        }
        foreach($rightsConfig as $config){
            $fields = $config['exportableFields'];
            if($fields == array('*')){
                return $widgetFields;
            }
            else{
                $exportableFields = array_unique(array_merge($exportableFields, $fields));
            }
        }
        foreach($exportableFields as $key=>$field){
            if(!in_array($field,$widgetFields)){
                unset($exportableFields[$key]);
            }
        }
        return $exportableFields;
    }

    /**
     * @ignore
     * @param $modelBuilder \Illuminate\Database\Eloquent\Model
     * @param $columns Array The array of columns. Each column must be in format of table.column
     * @return bool|mixed
     */
    protected function applyViewPermissions($modelBuilder, $columns = array('*'))
    {
        $model = $this->model;
        $widgetName = $model->widgetName;
        $rightsConfig = Helper::getRightsConfig($widgetName);
        $widgetsConfig = Helper::getWidgetList($model->apiVersion);

        //If no widget config found, we raise the error.
        if (!isset($widgetsConfig[$widgetName])) {
            throw new LogicException(get_class($this->model) . ' configurations must be declared in config.php file');
        }
        //If no right config found, we raise the error.
        if (!isset($rightsConfig) || count($rightsConfig) == 0) {
            throw new UnauthorizedHttpException(ErrorDefinition::UNAUTHORIZED_MESSAGE);
        }
        //Apply the selected fields after checking permissions
        $modelBuilder = $this->applyAbleStatement($modelBuilder, $rightsConfig, $widgetsConfig, $columns);

        //If not a public access then we have to process viewable.
        if ($widgetsConfig[$widgetName]['public'] !== true) {
            //Call scope to viewPermissions
            $modelBuilder = $modelBuilder->viewPermissions();
        }

        return $modelBuilder;
    }

    /**
     * @ignore
     * Apply default select columns
     * @param $modelBuilder \Illuminate\Database\Eloquent\Model
     * @param string $action
     * @param array $columns
     * @return mixed
     */
    protected function applySelectColumns($modelBuilder, $action='view', $columns=array('*')){
        $model = $this->model;
        $widgetName = $model->widgetName;
        $rightsConfig = Helper::getRightsConfig($widgetName);
        if($action=='edit'){
            $fields = $this->getEditableFields($rightsConfig);
        }
        else if($action =='delete'){
            $fields = array('*');
        }
        else if($action == 'export'){
            $fields = $this->getExportableFields($rightsConfig);
        }
        else{
            $fields = $this->getViewableFields($rightsConfig);
        }

        if ($columns == array('*')) {
            $modelBuilder = $modelBuilder->select($fields);
        }
        else if ($fields == array('*')) {
            $modelBuilder = $modelBuilder->select($columns);
        }
        else {
            $colArray = [];
            foreach ($columns as $col) {
                if (in_array($col, $fields)) {
                    array_push($colArray, $col);
                }
            }
            $modelBuilder = $modelBuilder->select($colArray);
        }
        return $modelBuilder;
    }
    /**
     * @ignore
     * Apply the select fields after checking the permissions
     * @param $modelBuilder \Illuminate\Database\Eloquent\Model
     * @param $rightsConfig
     * @param $widgetsConfig
     * @return mixed
     */
    protected function applyAbleStatement($modelBuilder, $rightsConfig, $widgetsConfig, $columns)
    {
        $model = $this->model;
        $modelName = $model->widgetName;
        $tableName = $model->getTable();
        $myAccount = app('Dingo\Api\Auth\Auth')->user();
        $myAccountId = $myAccount->id;
        $ableStatement['editable'] = ' WHEN 1=1 THEN true ';
        $ableStatement['deletable'] = ' WHEN 1=1 THEN true ';
        $ableStatement['exportable'] = ' WHEN 1=1 THEN true ';

        $editableField = "'".implode(',',$this->getEditableFieldsInCamelCase($rightsConfig))."' as editableFields";

        //Select the allow fields base on rights config and required columns
        if (Helper::isSuperAdmin() === true) {
            $modelBuilder = $this->applySelectColumns($modelBuilder,'view',$columns);
        }
        //If module by pass the control record access then default editable to all
        else if(!isset($widgetsConfig[$modelName]['controlRecordAccess']) || (isset($widgetsConfig[$modelName]['controlRecordAccess']) && $widgetsConfig[$modelName]['controlRecordAccess']!==true)){
            $modelBuilder = $this->applySelectColumns($modelBuilder,'view',$columns);
        }
        else {
            $modelBuilder = $this->applySelectColumns($modelBuilder,'view',$columns);
            //************************ HANDLE EDITABLE, DELETABLE, EXPORTABLE *************************/
            $typePrefix = ['editable', 'deletable', 'exportable'];
            foreach ($typePrefix as $type) {
                //By default user can handle records created by himself
                $ableStatement[$type] = ' WHEN '.$tableName.'.created_by = ' . $myAccountId . ' THEN true ';
                foreach ($rightsConfig as $rights){
                    //When he cannot handle all records
                    if ($rights[$type . 'Type'] == 1 || $rights[$type . 'Type'] == 2) {
                        //He cannot handle records created by excepted accounts
                        if (count($rights[$type . 'ExceptAccounts']) > 0) {
                            $ableStatement[$type] .= ' WHEN '.$tableName.'.created_by IN (' . implode(',', $rights[$type . 'ExceptAccounts']) . ') THEN false ';
                        }

                        //If he can handle all non-private records
                        if ($rights[$type . 'Type'] == 2) {
                            if ($widgetsConfig[$modelName]['hasPrivateRecords'] === true) {
                                //Allow only handle non-private record
                                $ableStatement[$type] .= ' WHEN is_private = FALSE THEN true ';
                            }
                            else{
                                //If not, he can handle everything
                                $ableStatement[$type] .= ' WHEN 1 = 1 THEN true ';
                            }
                        }
                        else if ($rights[$type . 'Type'] == 1) {
                            //He can handle records created by specified accounts
                            if (count($rights[$type . 'ByAccounts']) > 0) {
                                $ableStatement[$type] .= ' WHEN '.$tableName.'.created_by IN (' . implode(',', $rights[$type . 'ByAccounts']) . ') THEN true ';
                            }
                        }
                    }
                    else{
                        //If Type == 3 he can handle everything.
                        $ableStatement[$type] .= ' WHEN 1 = 1 THEN true ';
                    }
                    //Process conditions
                }
            }
        }
        //Add case end statement
        $ableStatement['editable'] = ' (CASE ' . $ableStatement['editable'] . ' ELSE false END) as editable ';
        $ableStatement['deletable'] = ' (CASE ' . $ableStatement['deletable'] . ' ELSE false END) as deletable ';
        $ableStatement['exportable'] = ' (CASE ' . $ableStatement['exportable'] . ' ELSE false END) as exportable ';

        //Add editable, deletable and exportable fields
        $ablesSelect = implode(', ', $ableStatement);
        if ($ablesSelect) {
            $modelBuilder = $modelBuilder->addSelect(DB::raw($ablesSelect));
        }
        $modelBuilder = $modelBuilder->addSelect(DB::raw($editableField));
        return $modelBuilder;
    }


    /**
     * @ignore
     *
     * Apply the filter from the url.
     *
     * Filter format:
     *
     *     filter=[{"operator":"like","value":"79","property":"name"}]
     *
     * Operator: ==, gt, ge, lt, le, eq, ne, like, in, notin
     *
     * @param $modelBuilder \Illuminate\Database\Eloquent\Model
     * @return mixed
     */
    protected function applyFilter($modelBuilder)
    {
        $model = $this->model;
        $table = $model->getTable();
        $filtersJson = Input::get('filter', '');
        if ($filtersJson) {
            $filters = json_decode($filtersJson, true);
            $opDef = ['==' => '=', 'gt' => '>', 'ge' => '>=', 'lt' => '<', 'le' => '<=', 'eq' => '=', 'ne' => '!=', 'like' => 'like', 'ilike' => 'like', 'in'=>'in','notin'=>'notin'];
            $wheres = [];
            foreach ($filters as $filter) {
                $field = \Helper::camelCaseToUnderScore($filter['property']);
                $operator = $opDef[$filter['operator']];
                $fullField = $table.'.'.$field;
                if ($operator == 'like') {
                    if(isset($filter['value'])){
                        $value = '%' . strtolower($filter['value']) . '%';
                    }
                    else{
                        $value = '%%';
                    }
                    $fullField = new Expression('LOWER('.$fullField.')');
                } else {
                    $value = $filter['value'];
                }

                if (strpos($filter['property'], '_') !== false) {
                    $entityDef = explode('_', $filter['property']);
                    $entity = strtolower($entityDef[0]);
                    $field = Helper::camelCaseToUnderScore($entityDef[1]);
                    $fullField = $entity.'.'.$field;
                    if ($operator == 'like') {
                        $fullField = new Expression('LOWER('.$fullField.')');
                    }
                }

                array_push($wheres, array($fullField, $operator, $value));
            }
            if(count($wheres) > 0){
                $modelBuilder->whereNested(function ($query) use ($filters, $opDef, $wheres) {
                    foreach($wheres as $w){
                        if($w[1]=='in'){
                            if(is_array($w[2])){
                                $query->whereIn($w[0],$w[2]);
                            }
                            else{
                                $query->whereIn($w[0],json_decode($w[2]));
                            }
                        }
                        else if($w[1]=='notin'){
                            if(is_array($w[2])){
                                $query->whereNotIn($w[0],$w[2]);
                            }
                            else{
                                $query->whereNotIn($w[0],json_decode($w[2]));
                            }
                        }
                        else{
                            $query->where($w[0],$w[1],$w[2]);
                        }

                    }
                });
            }
        }
        return $modelBuilder;
    }

    /**
     * @ignore
     * Apply sorting to query
     * @param $modelBuilder \Illuminate\Database\Eloquent\Model
     * @return mixed
     */
    protected function applySort($modelBuilder)
    {
        $model = $this->model;
        $table = $model->getTable();
        $sortJson = Input::get('sort', '');
        if ($sortJson) {
            $sorts = json_decode($sortJson, true);
            foreach ($sorts as $sort) {
                if(isset($sort['property']) && $sort['property']){
                    $field = \Helper::camelCaseToUnderScore($sort['property']);
                    $direction = isset($sort['direction'])?$sort['direction']:'ASC';
                    if($field){
                        if (strpos($sort['property'], '_') !== false) {
                            $entityDef = explode('_', $sort['property']);
                            $entity = strtolower($entityDef[0]);
                            $field = Helper::camelCaseToUnderScore($entityDef[1]);
                            $modelBuilder->orderBy(new Expression($entity.'.'.$field), $direction);
                        } else {
                            $modelBuilder->orderBy(new Expression($table.'.'.$field), $direction);
                        }
                    }
                }

            }
        }
        return $modelBuilder;
    }

    /**
     * Build up the query builder, such as where/order by, etc. for the repository before using methods to view data. The callback must return value of the query. Be sure to assign query value in each query methode clause.
     * In your controller you can specify the select condition like this:
     *
     *     public function index(){
     *          $this->repository->setWhere(function($query){
     *              $query=$query->where('id','=',1);
     *              $query=$query->orderBy('name','ASC');
     *              return $query
     *          });
     *          return parent::index();
     *     }
     *
     * @param callable $callback
     * @return $this
     */
    public function buildQuery(\Closure $callback){
        $query = $this->model;
        $query = call_user_func($callback, $query);
        $this->modelAfterQueryCustomBuilt = $query;
        return $this;
    }

    /**
     * Include scope that you need in all queries. You override this method in your repository file to include the scope by default in all the queries.
     *
     * For example
     *
     *     protected function defaultScope($model){
     *          return $model->with('countries');
     *     }
     *
     * @param $model \Illuminate\Database\Eloquent\Model
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function defaultScope($model){
        return $model;
    }

    /**
     * Set the scope for the query.
     *
     * You can use it in your controller like this:
     *
     *     public function index(){
     *          $this->repository->setScopes(array('country'))->setWhere(function($query){
     *              $query=$query->where('id','=',1);
     *              return $query
     *          });
     *          return parent::index();
     *     }
     * @param array $scopes
     * @return $this
     */
    public function setScopes(Array $scopes){
        $this->_setScopes = $scopes;
        return $this;
    }

    /**
     * Apply the view conditions to select query, including custom where, included scope, view permission, filter, sort, relationship. You can override this on your repository when needed.
     * @param $modelBuilder \Illuminate\Database\Eloquent\Model
     * @param $columns Array All columns must be in format of table.column
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function applyWidgetViewConditions($modelBuilder, $columns)
    {
        //Setting wheres
        if($this->modelAfterQueryCustomBuilt){
            $modelBuilder = $this->modelAfterQueryCustomBuilt;
        }
        //Setting scopes
        foreach($this->_setScopes as $scope){
            $modelBuilder = $modelBuilder->with($scope);
        }
        $modelBuilder = $this->applyViewPermissions($modelBuilder, $columns);
        $modelBuilder = $this->applyFilter($modelBuilder);
        $modelBuilder = $this->applySort($modelBuilder);
        $modelBuilder = $this->applyRelationshipDataInclusion($modelBuilder);
        return $modelBuilder;
    }



    /**
     * Apply the filter to updatable data. It will transform camelCase to underscore and remove fields that are not in fillable property of the Entity.
     *
     * It also add created_by, updated_by value according to the logged in user
     *
     * @param array $data
     * @param string $action
     * @return array
     */
    protected function applyUpdatableData(array $data, $action='add'){
        $model = $this->model;
        $columns = array_keys($data);
        $widgetName = $model->widgetName;
        $rightsConfig = Helper::getRightsConfig($widgetName);
        $myAccount = app('Dingo\Api\Auth\Auth')->user();
        $myAccountId = $myAccount->id;
        if(count($data)==0){
            throw new BadRequestHttpException(ErrorDefinition::BAD_REQUEST_MESSAGE);
        }
        $editableFields = $this->getEditableFields($rightsConfig);
        if($editableFields!=array('*')){
            foreach($columns as $column){
                if(!in_array(\Helper::camelCaseToUnderscore($column),$editableFields)){
                    unset($data[$column]);
                }
            }
        }

        //Transform data to underscore to update
        $postObj = [];
        foreach($data as $key=>$value){
            if(strpos($key,'_')!==false){
                //Convert relationship in case of sending 0 instead of null
                if($value==0){
                    $value = null;
                }
            }
            $newKey = \Helper::camelCaseToUnderscore($key);
            if(in_array($newKey,$model['fillable'])){
                $postObj[$newKey] = $value;
            }
        }
        $data = $postObj;

        if(count($data)>0){
            if($action=='add'){
                $data['created_by']=$myAccountId;
                $data['updated_by']=$myAccountId;
            }
            else if($action == 'edit'){
                $data['updated_by']=$myAccountId;
            }
        }
        return $data;
    }

    /**
     * Get all accessible data with given columns. All columns must be in format of table.column
     *
     * @param array $columns
     * @return array
     */
    public function all($columns = array('*'))
    {
        $this->applyCriteria();
        $model = $this->model;
        $model = $this->defaultScope($model);
        $model = $this->applyWidgetViewConditions($model,$columns);
        return $model->get($columns);
    }
    /**
     * List $key=>$value array of accessible data. All columns must be in format of table.column
     *
     * @param  string $value
     * @param  string $key
     * @return array
     */
    public function lists($value, $key = null) {
        $this->applyCriteria();
        $model = $this->model;
        $widgetName = $model->widgetName;
        $model = $this->defaultScope($model);
        $columns = array($value);
        if($key){
            array_push($columns, $key);
        }
        $rightsConfig = Helper::getRightsConfig($widgetName);
        $viewableFields = $this->getViewableFields($rightsConfig);
        if($viewableFields!=array('*') && (!in_array($value,$viewableFields) || ($key && !in_array($key,$viewableFields)))){
            throw new UnauthorizedHttpException(ErrorDefinition::UNAUTHORIZED_MESSAGE);
        }
        $model = $this->applyWidgetViewConditions($model,$columns);
        $lists = $model->lists($value,$key);
        if(is_array($lists)) {
            return $lists;
        }
        return $lists->all();
    }

    /**
     * Paginate the accessible data. All columns must be in format of table.column
     *
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 10, $columns = array('*'))
    {
        $this->applyCriteria();
        $model = $this->model;
        $model = $this->defaultScope($model);
        $model = $this->applyWidgetViewConditions($model,$columns);
        //var_dump($model->toSql());die();
        //var_dump($model->getBindings());die();
        return $model->paginate(Input::get('limit', $perPage));
    }

    /**
     * Process the relationship data inclusion for defaultEagerLoads in entity
     *
     * @param $modelBuilder \Illuminate\Database\Eloquent\Model
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function applyRelationshipDataInclusion($modelBuilder){
        $model = $this->model;
        $widgetName = $model->widgetName;
        $defaultEagerLoads = [];
        if(isset($model->defaultEagerLoads)){
            $defaultEagerLoads = $model->defaultEagerLoads;
        }
        //var_dump($eagerLoads);die();
        //Detect whether to get with relationship.
        $rwrel = Input::get('rwrel');
        if($rwrel==1){
            foreach($defaultEagerLoads as $relationship){
                //Add actual joins
                $modelBuilder = $modelBuilder->modelJoin($relationship,$this->getViewableFields(Helper::getRightsConfig($widgetName)));
            }
        }
        return $modelBuilder;
    }
    /**
     * Find a record by ID
     * @param $id
     * @param array $columns
     * @return array
     */
    public function find($id, $columns = array('*')){
        $this->applyCriteria();
        $model = $this->model;
        $model = $this->defaultScope($model);
        $model = $this->applyWidgetViewConditions($model,$columns);
        return $model->find($id, $columns);
    }

    /**
     * Find the first record by attribute
     * @param string $attribute
     * @param mixed $value
     * @param array $columns
     * @return array
     */
    public function findBy($attribute, $value, $columns = array('*')) {
        $this->applyCriteria();
        $model = $this->model;
        $model = $this->defaultScope($model);
        $model = $this->applyWidgetViewConditions($model,$columns);
        return $model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * Find all records by attribute
     * @param string $attribute
     * @param mixed $value
     * @param array $columns
     * @return array
     */
    public function findAllBy($attribute, $value, $columns = array('*')) {
        $this->applyCriteria();
        $model = $this->model;
        $model = $this->defaultScope($model);
        $model = $this->applyWidgetViewConditions($model,$columns);
        return $model->where($attribute, '=', $value)->get($columns);
    }
    /**
     * Find records by conditions
     *
     *     $where = array('id'=>array('id','>',1))
     *
     * @param array $where
     * @param array $columns
     * @param bool $or Default is AND
     * @return array
     */
    public function findWhere($where, $columns = ['*'], $or = false){
        $this->applyCriteria();
        $model = $this->model;
        $model = $this->defaultScope($model);
        $model = $this->applyWidgetViewConditions($model,$columns);
        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $model = (! $or)
                    ? $model->where($value)
                    : $model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) === 3) {
                    list($field, $operator, $search) = $value;
                    $model = (! $or)
                        ? $model->where($field, $operator, $search)
                        : $model->orWhere($field, $operator, $search);
                } elseif (count($value) === 2) {
                    list($field, $search) = $value;
                    $model = (! $or)
                        ? $model->where($field, '=', $search)
                        : $model->orWhere($field, '=', $search);
                }
            } else {
                $model = (! $or)
                    ? $model->where($field, '=', $value)
                    : $model->orWhere($field, '=', $value);
            }
        }
        return $model->get($columns);
    }

    /**
     * Create new record
     * @param array $data
     * @return mixed Newly created record
     */
    public function create(array $data) {
        $model = $this->model;
        $widgetName = $model->widgetName;
        $rightsConfig = Helper::getRightsConfig($widgetName);
        $data = $this->applyUpdatableData($data);

        if(Helper::isSuperAdmin()===true){
            $newRec = $model->create($data);
            return $this->find($newRec->id);
        }
        else{
            $newRec = $model->create($data);
            return $this->find($newRec->id);
        }
    }

    /**
     * Update data to record by attribute
     * @param array $data
     * @param $value
     * @param string $attribute
     * @return mixed Newly updated record
     */
    public function update(array $data, $value, $attribute="id") {
        //Check by permission then update
        $model = $this->model;
        //Need to add select because model needs to be converted to builder
        $model=$this->applySelectColumns($model,'edit');
        $model = $model->updatePermissions();
        if(!($model->where($attribute, '=', $value)->get())){
            throw new NotFoundHttpException(ErrorDefinition::ITEM_NOT_FOUND_MESSAGE);
        }
        $data = $this->applyUpdatableData($data,'edit');
        if(count($data)>0){
            if(Helper::isSuperAdmin()===true){
                return $model->where($attribute, '=', $value)->update($data);
            }
            else{
                return $model->where($attribute, '=', $value)->update($data);
            }
        }
    }

    /**
     * Update data to record by ID
     * @param array $data
     * @param $id
     * @return mixed Newly updated record
     */
    public function updateRich(array $data, $id){
        //Check by permission then update
        $model = $this->model;
        //Need to add select because model needs to be converted to builder
        $model=$this->applySelectColumns($model,'edit');
        $model = $model->updatePermissions();
        if(!($model->find($id))){
            throw new NotFoundHttpException(ErrorDefinition::ITEM_NOT_FOUND_MESSAGE);
        }
        $data = $this->applyUpdatableData($data,'edit');
        if(count($data)>0){
            if(Helper::isSuperAdmin()===true){
                return $this->model->find($id)->fill($data)->save();
            }
            else{
                return $this->model->find($id)->fill($data)->save();
            }
        }
    }

    /**
     * Delete a record by id
     * @param $id
     * @return boolean
     */
    public function delete($id) {
        //Check by permission then delete
        $model = $this->model;
        $model = $model->select('*');
        $model = $model->deletePermissions();
        if(!($model->find($id))){
            throw new NotFoundHttpException(ErrorDefinition::ITEM_NOT_FOUND_MESSAGE);
        }
        return $model->delete($id);
    }

    /**
     * Export data to excel
     * @param AbstractExportTransformer $transformer The transformer. If not, raw data will be outputted
     * @param array $columns All columns must be in format of table.column
     * @param string $fileName
     */
    public function exportToExcel($transformer=null, $columns=array('*'), $fileName='my_file'){
        $model = $this->model;
        $model::$withoutAppends = true;
        $model = $this->defaultScope($model);
        $model=$this->applySelectColumns($model,'export',$columns);
        $model = $model->exportPermissions();
        $model = $this->applyFilter($model);
        $model = $this->applySort($model);
        $model = $this->applyRelationshipDataInclusion($model);
        header("Access-Control-Allow-Origin: *");
        $data = $model->get($columns);
        if($transformer){
            $data = $this->formatExportData($data, new $transformer);
        }
        //dd($data);
        Excel::create($fileName, function($excel) use ($data){
            $excel->sheet('Sheet1', function($sheet)  use ($data){;
                $sheet->fromArray($data);
            });
        })->export('xlsx');
    }

    /**
     * Export data to PDF
     * @param AbstractExportTransformer $transformer The transformer. If not, raw data will be outputted
     * @param array $columns All columns must be in format of table.column
     * @param string $fileName
     */
    public function exportToPdf($transformer=null, $columns=array('*'), $fileName='my_file'){
        $model = $this->model;
        $model::$withoutAppends = true;
        $model = $this->defaultScope($model);
        $model=$this->applySelectColumns($model,'export',$columns);
        $model = $model->exportPermissions();
        $model = $this->applyFilter($model);
        $model = $this->applySort($model);
        $model = $this->applyRelationshipDataInclusion($model);
        header("Access-Control-Allow-Origin: *");
        $data = $model->get($columns);
        if($transformer){
            $data = $this->formatExportData($data, new $transformer);
        }
        //dd($data);
        Excel::create($fileName, function($excel) use ($data){
            $excel->sheet('Sheet1', function($sheet)  use ($data){;
                $sheet->fromArray($data);
            });
        })->export('pdf');
    }

    /**
     * @ignore
     * Format data to output to export with the transformer.
     * @param $data
     * @param $transformer
     * @return mixed
     */
    protected function formatExportData($data, $transformer){
        $fractal = new Manager();
        $resource = new Collection($data, $transformer);
        $array = $fractal->createData($resource)->toArray();
        return $array['data'];
    }
}