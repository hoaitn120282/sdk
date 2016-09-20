<?php

namespace App\Api\V1\Modules\Customer\Http\Controllers;

use App\Api\V1\Modules\Customer\Repository\CustomerRepository;
use QSoftvn\Controllers\WidgetBaseController;
use Illuminate\Http\Request;

class CustomerController extends WidgetBaseController
{
    public $repository;
    protected $transformer = \App\Api\V1\Modules\Customer\Transformer\CustomerTransformer::class;
    

    function __construct(CustomerRepository $repo)
    {
        $this->repository = $repo;
    }
    public function index()
    {
        return parent::index();
    }
    public function all(){
        return parent::all();
    }
    public function lists(){
        return parent::lists();
    }
    public function show($id)
    {
        return parent::show($id);
    }
    public function store(Request $request)
    {
        return parent::store($request);
    }
    public function update($id, Request $request)
    {
        return parent::update($id, $request);
    }
    public function destroy($id,Request $request)
    {
        return parent::destroy($id,$request);
    }
    public function export(){
        return parent::export();
    }
    /**
     * Manipulate data before inserting
     * @param $data
     * @return mixed
     */
    public function beforeAddItem($data){
        return $data;
    }

    /**
     * Manipulate result before responding
     * @param $item
     * @param $data
     * @return mixed
     */
    public function afterAddItem($item, $data){
        return $item;
    }

    /**
     * Manipuldate data before updating
     * @param $data
     * @return mixed
     */
    public function beforeUpdateItem($data){
        return $data;
    }

    /**
     * Manipulate result before responding
     * @param $item
     * @param $data
     * @return mixed
     */
    public function afterUpdateItem($item, $data){
        return $item;
    }
}
    