<?php namespace QSoftvn\Repositories;

use App\Exceptions\ErrorDefinition;
use QSoftvn\Helper\Helper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Process\Exception\LogicException;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class TreeBaseRepository
 *
 * This class is created specially for Tree/Hierarchical Data Widget.
 *
 * {@inheritDoc}
 *
 * @package QSoftvn\Repositories
 */
abstract class TreeBaseRepository extends WidgetBaseRepository {

    /**
     * List data in tree format
     * @param array $columns All columns must be in format of table.column
     * @param int $id The id of the root node if you want to retrieve sub tree
     * @return array
     */
    public function listTree($columns=array('*'), $id=null){
        $this->applyCriteria();
        $model = $this->model;
        $model = $this->defaultScope($model);
        if($model->isBroken()){
            $model->fixTree();
        }
        $model = $this->applyWidgetViewConditions($model,$columns);
        if($id){
            return $model->defaultOrder()->ancestorsOf($id)->toHierarchy();
        }
        else{
            return $model->defaultOrder()->get()->toHierarchy();
        }
    }

    /**
     * Include the default scope by using the treeScopeField and treeScopeValue declared in the entity. You need to override this function when you want to include more scope. Besure to put following when you override:
     *
     *     if($model->treeScopeField && $model->defaultTreeScopeValue){
     *          $model = $model->scoped([$model->treeScopeField=>$model->defaultTreeScopeValue]);
     *     }
     *     return $model;
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function defaultScope($model){
        if($model->treeScopeField && $model->defaultTreeScopeValue){
            $model = $model->scoped([$model->treeScopeField=>$model->defaultTreeScopeValue]);
        }
        return $model;
    }

    /**
     * This override the parent class for special purpose of tree that need to include parent_id, _drop_position and _target_node into the data. Otherwise they will be removed when filter applied
     * @param array $data
     * @param string $action
     * @return array
     * @throws BadRequestHttpException
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
        $editableFields = array_merge($editableFields);

        /*if($editableFields!=array('*')){
            foreach($columns as $column){
                if(!in_array(\Helper::camelCaseToUnderscore($column),$editableFields)){
                    unset($data[$column]);
                }
            }
        }*/
        //Transform data to underscore to update
        $postObj = [];
        foreach($data as $key=>$value){
            if(strpos($key,'_')!==false && $key!='_dropPosition' && $key!='_targetNode'){
                //Convert relationship in case of sending 0 instead of null
                if($value==0){
                    $value = null;
                }
            }
            $newKey = \Helper::camelCaseToUnderscore($key);
            if((in_array($newKey,$model['fillable']) && (in_array($newKey,$editableFields) || $editableFields==array('*'))) || $newKey == 'parent_id' || $newKey == '_drop_position' || $newKey == '_target_node'){
                if($newKey=='parent_id'){
                    if($value == 'root'){
                        $value = null;
                    }
                }
                $postObj[$newKey] = $value;
            }
        }
        $data = $postObj;
        if(count($data)){
            if($action=='add'){
                $data['created_by']=$myAccountId;
                $data['updated_by']=$myAccountId;
                if($model->treeScopeField && $model->defaultTreeScopeValue){
                    $data[$model->treeScopeField] = $model->defaultTreeScopeValue;
                }
            }
            else if($action == 'edit'){
                $data['updated_by']=$myAccountId;
            }
        }
        return $data;
    }

    /**
     * Override the parent class to create a tree node
     * @param array $data
     * @return mixed
     */
    public function create(array $data) {
        $model = $this->model;
        $widgetName = $model->widgetName;
        //$model = $this->defaultScope($model);
        $rightsConfig = Helper::getRightsConfig($widgetName);
        $data = $this->applyUpdatableData($data);

        if(Helper::isSuperAdmin()===true){
            return $this->processCreate($model,$data);
        }
        else{
            return $this->processCreate($model,$data);
        }
    }

    /**
     * Process create data. It will create a child node or root node depending on parent_id sent from the request
     * @param $model
     * @param array $data
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function processCreate($model, array $data){
        if(isset($data['parent_id']) && $data['parent_id']!==null){
            $parentNode = $model->find(intval($data['parent_id']));
            if($parentNode){
                try{
                    $newRec = $parentNode->children()->create($data);
                    return $this->find($newRec->id);
                }
                catch (\Exception $e){
                    throw new LogicException($e->getMessage());
                }
            }
            else{
                throw new NotFoundHttpException(ErrorDefinition::PARENT_ITEM_NOT_FOUND_MESSAGE);
            }
        }
        else{
            try{
                $newRec = $model->create($data);
                return $this->find($newRec->id);
            }
            catch (\Exception $e){
                throw new LogicException($e->getMessage());
            }
        }
    }

    /**
     * Update data to record by ID
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return array The updated node
     */
    public function update(array $data, $id, $attribute="id") {
        $targetNodeId = null;
        $targetPos = null;
        //Check by permission then update
        $model = $this->model;
        $model = $this->defaultScope($model);
        //Need to add select because model needs to be converted to builder
        $model=$this->applySelectColumns($model,'edit');
        $model = $model->updatePermissions();
        if(!($model->find($id))){
            throw new NotFoundHttpException(ErrorDefinition::ITEM_NOT_FOUND_MESSAGE);
        }
        $data = $this->applyUpdatableData($data,'edit');
        //Prepare data for move node
        if(isset($data['_drop_position'])){
            $targetNodeId = $data['_target_node'];
            $targetPos = $data['_drop_position'];
            unset($data['_drop_position']);
            unset($data['_target_node']);
            unset($data['parent_id']);
        }
        if(count($data)>0){
            try{
                if(Helper::isSuperAdmin()===true){
                    $model->where($attribute, '=', $id)->update($data);
                }
                else{
                    $model->where($attribute, '=', $id)->update($data);
                }
            }
            catch (\Exception $e){
                $myModel = $this->defaultScope($this->model);
                $myModel->fixTree();
                throw new LogicException($e->getMessage());
            }
        }
        //Process move node
        if($targetNodeId!==null){
            $targetNode = $this->model->find($targetNodeId);
            $node = $this->model->find($id);
            if(!$targetNode){
                throw new NotFoundHttpException(ErrorDefinition::ITEM_NOT_FOUND_MESSAGE);
            }
            else{
                try{
                    if($targetPos=='before'){
                        $node->beforeNode($targetNode)->save();
                    }
                    else if($targetPos=='after'){
                        $node->afterNode($targetNode)->save();
                    }
                    else if($targetPos=='append'){
                        $node->parent()->associate($targetNode)->save();
                    }
                }
                catch (\Exception $e){
                    $myModel = $this->model;
                    $myModel = $this->defaultScope($myModel);
                    $myModel->fixTree();
                    throw new LogicException($e->getMessage());
                }

            }
        }
        return $this->listTree(array('*'),$id); //Return tree structure for sync purpose
    }

    /**
     * Update data to record by ID
     * @param array $data
     * @param $id
     * @return array The updated node
     */
    public function updateRich(array $data, $id){
        $targetNodeId = null;
        $targetPos = null;
        //Check by permission then update
        $model = $this->model;
        $model = $this->defaultScope($model);
        //Need to add select because model needs to be converted to builder
        $model=$this->applySelectColumns($model,'edit');
        $model = $model->updatePermissions();
        if(!($model->find($id))){
            throw new NotFoundHttpException(ErrorDefinition::ITEM_NOT_FOUND_MESSAGE);
        }
        $data = $this->applyUpdatableData($data,'edit');
        //Prepare data for move node
        if(isset($data['_drop_position'])){
            $targetNodeId = $data['_target_node'];
            $targetPos = $data['_drop_position'];
            unset($data['_drop_position']);
            unset($data['_target_node']);
            unset($data['parent_id']);
        }
        if(count($data)>0){
            try{
                if(Helper::isSuperAdmin()===true){
                    $this->model->find($id)->fill($data)->save();
                }
                else{
                    $this->model->find($id)->fill($data)->save();
                }
            }
            catch (\Exception $e){
                $myModel = $this->defaultScope($this->model);
                $myModel->fixTree();
                throw new LogicException($e->getMessage());
            }
        }
        //Process move node
        if($targetNodeId!==null){
            $targetNode = $this->model->find($targetNodeId);
            $node = $this->model->find($id);
            //Helper::d($node->id);
            if(!$targetNode){
                throw new NotFoundHttpException(ErrorDefinition::ITEM_NOT_FOUND_MESSAGE);
            }
            else{
                try{
                    if($targetPos=='before'){
                        $node->beforeNode($targetNode)->save();
                    }
                    else if($targetPos=='after'){
                        $node->afterNode($targetNode)->save();
                    }
                    else if($targetPos=='append'){
                        $node->parent()->associate($targetNode)->save();
                    }
                }
                catch (\Exception $e){
                    $myModel = $this->model;
                    $myModel = $this->defaultScope($myModel);
                    $myModel->fixTree();
                    throw new LogicException($e->getMessage());
                }
            }
        }
        return $this->listTree(array('*'),$id); //Return tree structure for sync purpose
    }

    /**
     * Delete a record by id
     * @param $id
     * @return boolean
     */
    public function delete($id) {
        //Check by permission then delete
        $model = $this->model;
        $model = $this->defaultScope($model);
        $model = $model->select('*');
        $model = $model->deletePermissions();
        if($node = $model->find($id)){
            try{
                return $node->delete(); //Use this for rebuilding tree
            }
            catch (\Exception $e){
                throw new LogicException($e->getMessage());
            }
        }
    }

    /**
     * Handle indented data for exporting
     * @param array $columns
     * @return array
     */
    public function outputForExport($columns = array('*')){
        $this->applyCriteria();
        $model = $this->model;
        $model::$withoutAppends = true;
        $indentedColumnName = $model->indentedColumnName;
        $model = $this->defaultScope($model);
        if($model->isBroken()){
            $model->fixTree();
        }

        $model=$this->applySelectColumns($model,'export',$columns);
        $model = $model->exportPermissions();
        $model = $this->applyFilter($model);
        $model = $this->applySort($model);
        $data = $model->defaultOrder()->withDepth()->get()->toArray();
        $indentedData = [];
        foreach($data as $row){
            $row[$indentedColumnName] = str_repeat('    ', $row['depth']).$row[$indentedColumnName];
            array_push($indentedData,$row);
        }
        return $indentedData;
    }
    /**
     * Export data to excel
     * @param AbstractExportTransformer $transformer The transformer. If not, raw data will be outputted
     * @param array $columns All columns must be in format of table.column
     * @param string $fileName
     */
    public function exportToExcel($transformer=null, $columns=array('*'), $fileName='my_file'){
        $data = $this->outputForExport($columns);
        if($transformer){
            $data = $this->formatExportData($data, new $transformer);
        }
        //dd($data);
        header("Access-Control-Allow-Origin: *");
        Excel::create($fileName, function($excel) use ($data){
            $excel->sheet('Sheet1', function($sheet)  use ($data){;
                $sheet->fromArray($data);
            });
        })->export('xlsx');
    }
}