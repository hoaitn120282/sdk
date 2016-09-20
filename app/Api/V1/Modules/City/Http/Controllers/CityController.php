<?php

namespace App\Api\V1\Modules\City\Http\Controllers;

use App\Api\V1\Modules\City\Repository\CityRepository;
use QSoftvn\Controllers\WidgetBaseController;
use Illuminate\Http\Request;

class CityController extends WidgetBaseController
{
    public $repository;
    protected $transformer = \App\Api\V1\Modules\City\Transformer\CityTransformer::class;
    

    function __construct(CityRepository $repo)
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
}
    