<?php

namespace App\Api\V1\Modules\Ward\Http\Controllers;

use App\Api\V1\Modules\Ward\Repository\WardRepository;
use QSoftvn\Controllers\WidgetBaseController;
use Illuminate\Http\Request;

class WardController extends WidgetBaseController
{
    public $repository;
    protected $transformer = \App\Api\V1\Modules\Ward\Transformer\WardTransformer::class;
    

    function __construct(WardRepository $repo)
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
    