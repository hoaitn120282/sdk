<?php

namespace App\Api\V1\Modules\Role\Http\Controllers;

use App\Api\V1\Modules\Role\Repository\RoleRepository;
use QSoftvn\Controllers\PermissionTrait;
use QSoftvn\Controllers\TreeBaseController;
use Illuminate\Http\Request;

class RoleController extends TreeBaseController
{
    //To handle permissions
    use PermissionTrait;

    public $repository;
    protected $transformer = \App\Api\V1\Modules\Role\Transformer\RoleTransformer::class;
    protected $flatTransformer = \App\Api\V1\Modules\Role\Transformer\RoleFlatTransformer::class;

    function __construct(RoleRepository $repo)
    {
        $this->repository = $repo;
    }
    public function index()
    {
        return parent::index();
    }
    public function store(Request $request){
        $result = $this->repository->create($request->all());
        return $this->response->item($result, new $this->flatTransformer)->setStatusCode(201);

    }
    public function update($id, Request $request){
        $allRequest = $request->all();
        //Process multiple updates
        if(isset($allRequest[0])){
            foreach($allRequest as $myRequest){
                if(isset($myRequest['id'])){
                    $this->validateData($myRequest['id'],$request);
                    $this->repository->updateRich($myRequest, $myRequest['id']);
                }
            }
        }
        else{
            $this->validateData($allRequest,$request);
            $this->repository->updateRich($allRequest, $id);
        }
        //Customize response to grid based not tree based
        return $this->response->item($this->repository->find($id), new $this->flatTransformer)->setStatusCode(200);
    }

    public function show($id)
    {
        return parent::show($id);
    }

    public function destroy($id,Request $request)
    {
        return parent::destroy($id,$request);
    }
}
    