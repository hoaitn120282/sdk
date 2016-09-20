<?php

namespace App\Api\V1\Modules\Organization\Http\Controllers;

use App\Api\V1\Modules\Organization\Repository\OrganizationRepository;
use QSoftvn\Controllers\TreeBaseController;
use Illuminate\Http\Request;

class OrganizationController extends TreeBaseController
{
    public $repository;
    protected $transformer = \App\Api\V1\Modules\Organization\Transformer\OrganizationTransformer::class;
    protected $flatTransformer = \App\Api\V1\Modules\Organization\Transformer\OrganizationFlatTransformer::class;

    function __construct(OrganizationRepository $repo)
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
    public function store(Request $request)
    {
        return parent::store($request);
    }

    public function update($id, Request $request)
    {
        return parent::update($id, $request);
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
    