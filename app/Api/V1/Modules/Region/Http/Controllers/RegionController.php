<?php

namespace App\Api\V1\Modules\Region\Http\Controllers;

use App\Api\V1\Modules\Region\Repository\RegionRepository;
use QSoftvn\Controllers\WidgetBaseController;
use Illuminate\Http\Request;

class RegionController extends WidgetBaseController
{
    public $repository;
    protected $transformer = \App\Api\V1\Modules\Region\Transformer\RegionTransformer::class;
    protected $exportTransformer = \App\Api\V1\Modules\Region\Transformer\RegionExportTransformer::class;


    function __construct(RegionRepository $repo)
    {
        $this->repository = $repo;
    }
    public function index()
    {
        return parent::index();
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
    public function export(){
        return $this->repository->exportToExcel($this->exportTransformer, array('regions.name','countries.name'));
    }
}
    