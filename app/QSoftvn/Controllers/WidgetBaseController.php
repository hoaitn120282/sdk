<?php
/**
 * The widget base controller class
 */
namespace QSoftvn\Controllers;

use App\Exceptions\ErrorDefinition;
use Dingo\Api\Auth\Auth;
use QSoftvn\Helper\Helper;
use Illuminate\Http\Request;
use QSoftvn\Transformer\DeleteTransformer;

/**
 * This class provides abstraction for the controller of a widget
 * @package QSoftvn\Controllers
 */
abstract class WidgetBaseController extends BaseController
{
    /**
     * The default transformer
     * @var
     */
    protected $transformer = \QSoftvn\Transformer\AbstractWidgetTransformer::class;

    /**
     * Paginate the data for list view
     * @return \Dingo\Api\Http\Response
     */
    public function index(){
        $data = $this->repository->paginate(10);
        //return $this->response->array($data);
        return $this->response->paginator($data, new $this->transformer)->setMeta($this->getWidgetMeta());
    }

    /**
     * List all data for list view
     * @return \Dingo\Api\Http\Response
     */
    public function all(){
        $data = $this->repository->all();
        return $this->response->collection($data, new $this->transformer)->setMeta($this->getWidgetMeta());
    }

    /**
     * List id and name only for list view
     * @return mixed
     */
    public function lists(){
        $tableName = $this->repository->getModel()->getTable();
        $data = $this->repository->all(array($tableName.'.id',$tableName.'.name'));
        return $this->response->array(array('data'=>$data));
    }

    /**
     * Retrieve data item by ID
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function show($id){
        $item = $this->repository->find($id);
        if(!$item){
            $this->response->errorNotFound(ErrorDefinition::ITEM_NOT_FOUND_MESSAGE);
        }
        else{
            //return $this->response->array($item);
            return $this->response->item($item, new $this->transformer)->setMeta($this->getWidgetMeta());
        }
    }

    /**
     * Insert new item to database
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request){
        $rawData = $data = $request->all();
        $data = $this->validateData($data,$request,'add');
        $data = $this->beforeAddItem($data);
        $result = $this->repository->create($data);
        $item = $this->afterAddItem($result, $rawData);
        if($item === false){
            $item = $this->repository->find($result->id);
        }
        return $this->response->item($item, new $this->transformer)->setStatusCode(201)->setMeta($this->getWidgetMeta());
    }

    /**
     * Update data items to database
     * @param $id
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function update($id, Request $request){
        $rawData = $allRequest = $request->all();
        $tableName = $this->repository->getModel()->getTable();
        //Process multiple updates
        if(isset($allRequest[0])){
            $ids = [];
            foreach($allRequest as $myRequest){
                if(isset($myRequest['id'])){
                    $data = $this->validateData($myRequest, $request, 'edit');
                    $data = $this->beforeUpdateItem($data);
                    $this->repository->updateRich($data, $myRequest['id']);
                    array_push($ids,$myRequest['id']);
                }
            }
            $items = $this->repository->findWhere(array($tableName.'.id'=>function($query) use ($ids,$tableName){
                return $query->whereIn($tableName.'.id', $ids);
            }));
            $items = $this->afterUpdateItem($items, $rawData);
            if($items === false){
                $items = $this->repository->findWhere(array($tableName.'.id'=>function($query) use ($ids, $tableName){
                    return $query->whereIn($tableName.'.id', $ids);
                }));
            }
            return $this->response->collection($items, new $this->transformer)->setMeta($this->getWidgetMeta());
        }
        else{
            $data = $this->validateData($allRequest, $request, 'edit');
            $data = $this->beforeUpdateItem($data);
            $this->repository->updateRich($data, $id);
            $item = $this->repository->find($id);
            $item = $this->afterUpdateItem($item, $rawData);
            if($item === false){
                $item = $this->repository->find($id);
            }
            return $this->response->item($item, new $this->transformer)->setMeta($this->getWidgetMeta());
        }

    }

    /**
     * Destroy one or more items from database
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function destroy($id, Request $request){
        $success = false;
        $allRequest = $request->all();
        //Process multiple updates
        if(isset($allRequest[0])){
            foreach($allRequest as $myRequest){
                if(isset($myRequest['id'])){
                    $success = $this->repository->delete($myRequest['id']);
                }
            }
        }
        else{
            $success = $this->repository->delete($id);
        }
        return $this->response->item((object)array('success'=>$success), new DeleteTransformer())->setMeta($this->getWidgetMeta());
    }

    /**
     * Export data to excel
     * @return mixed
     */
    public function export(){
        return $this->repository->exportToExcel();
    }

    /**
     * Get the widget meta to for setMeta in response.
     * @return array
     */
    protected function getWidgetMeta(){
        $r = [];
        $model = $this->repository->getModel();
        $widgetName = $model->widgetName;
        $accessibleRoutes = Helper::getAccessibleRoutesForMe();
        $routes = Helper::getWidgetRoutes($widgetName);
        foreach($routes as $route=>$cfg){
            if(in_array($route,$accessibleRoutes)){
                array_push($r,$cfg['name']);
            }
        }
        return array('routes'=>$r);
    }
}