<?php
/**
 * The base controller class
 */
namespace QSoftvn\Controllers;

use App\Exceptions\ErrorDefinition;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as SystemBaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use QSoftvn\Helper\Helper;
use Dingo\Api\Exception\StoreResourceFailedException;

/**
 * This class is the base controller for all QSDK Laravel's controller
 * @package QSoftvn\Controllers
 */
abstract class BaseController extends SystemBaseController
{
    use Helpers, AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $transformer = \QSoftvn\Transformer\AbstractWidgetTransformer::class;

    /**
     * Paginate the data for list view
     * @return \Dingo\Api\Http\Response
     */
    public function index(){
        $data = $this->repository->paginate(10);
        return $this->response->paginator($data, new $this->transformer);
    }

    /**
     * List all data for list view
     * @return \Dingo\Api\Http\Response
     */
    public function all(){
        $data = $this->repository->all();
        return $this->response->collection($data, new $this->transformer);
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
            return $this->response->item($item, new $this->transformer);
        }
    }

    /**
     * Insert new item to database
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request){
        $rawData = $data = $request->all();
        $data = $this->validateData($data, $request, 'add');
        $data = $this->beforeAddItem($data);
        $result = $this->repository->create($data);
        $item = $this->afterAddItem($result, $rawData);
        if($item===false){
            $item = $this->repository->find($result->id);
        }
        return $this->response->item($item, new $this->transformer)->setStatusCode(201);
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
            $items = $this->repository->findWhere(array($tableName.'.id'=>function($query) use ($ids, $tableName){
                return $query->whereIn($tableName.'.id', $ids);
            }));
            $items = $this->afterUpdateItem($items, $rawData);
            if($items === false){
                $items = $this->repository->findWhere(array($tableName.'.id'=>function($query) use ($ids, $tableName){
                    return $query->whereIn($tableName.'.id', $ids);
                }));
            }
            return $this->response->collection($items, new $this->transformer);
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
            return $this->response->item($item, new $this->transformer);
        }
    }

    /**
     * Destroy one or more items from database
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function destroy($id,Request $request){
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
        return $this->response->array(array('success'=>$success));
    }

    /**
     * Export data to excel
     * @return mixed
     */
    public function export(){
        return $this->repository->exportToExcel();
    }

    /**
     * Validate the submitted values against the model's rules.
     * @param $data
     * @param $request
     * @param string $action
     * @return mixed
     */
    protected function validateData($data, $request, $action='add'){
        $rules = $this->repository->getModel()->rules;
        $data = $this->repository->getModel()->convertUpdatableData($data,$action);
        if(is_array($data)){
            if(strtoupper($request->method())=='PUT'){
                $fields = array_keys($data);
                foreach($rules as $f=>$r){
                    if(!in_array($f,$fields)){
                        unset($rules[$f]);
                    }
                }
            }
            //Validate data
            $validator = Validator::make($data, $rules, $this->repository->getModel()->errorMessages);
            if($validator->fails()){
                $errors = $validator->errors()->all();
                throw new StoreResourceFailedException(implode(', ',$errors), $validator->errors());
            }
        }
        return $data;
    }

    /**
     * Manipulate data before inserting. Override this in your controller
     * @param $data
     * @return mixed
     */
    protected function beforeAddItem($data){
        return $data;
    }

    /**
     * Manipulate result before responding. Override this in your controller. Return false to force retrieving item again from database for responding; Return item if you want to return it to client.
     * @param $item
     * @param $data
     * @return mixed
     */
    protected function afterAddItem($item, $data){
        return $item;
    }

    /**
     * Manipuldate data before updating. Override this in your controller
     * @param $data
     * @return mixed
     */
    protected function beforeUpdateItem($data){
        return $data;
    }

    /**
     * Manipulate result before responding. Override this in your controller. Return false to force retrieving item again from database for responding; Return item if you want to return it to client.
     * @param $item
     * @param $data
     * @return mixed
     */
    protected function afterUpdateItem($item, $data){
        return $item;
    }

    /**
     * Process the download of a file from server. You can create an action and call this method to download a file.
     * @param $downloadName Name of file to be downloaded
     * @param $filePath Full file path to download
     * @param $contentType The content type of the file
     * @param array $headers Extra headers
     * @throws LaravelExcelException
     */
    public function _download($downloadName, $filePath, $contentType, Array $headers = array())
    {
        $this->_setHeaders(
            $headers,
            array(
                'Content-Type'        => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Expires'             => 'Mon, 26 Jul 1997 05:00:00 GMT', // Date in the past
                'Last-Modified'       => date('Y-m-d H:i:s'),
                'Cache-Control'       => 'cache, must-revalidate',
                'Pragma'              => 'public',
                'Access-Control-Allow-Origin'=>'*'
            )
        );
        // Download
        readfile($filePath);
        exit;
    }

    /**
     * @ignore
     * Set the header for the downloading
     * @param array $headers
     * @param array $default
     * @throws LaravelExcelException
     */
    protected function _setHeaders(Array $headers = array(), Array $default)
    {
        if (headers_sent()) throw new LaravelExcelException('[ERROR]: Headers already sent');

        // Merge the default headers with the overruled headers
        $headers = array_merge($default, $headers);

        foreach ($headers as $header => $value)
        {
            header($header . ': ' . $value);
        }
    }
}