<?php

namespace App\Api\V1\Modules\Country\Http\Controllers;

use App\Api\V1\Modules\Country\Repository\CountryRepository;
use QSoftvn\Controllers\WidgetBaseController;
use Illuminate\Http\Request;

class CountryController extends WidgetBaseController
{
    public $repository;
    protected $transformer = \App\Api\V1\Modules\Country\Transformer\CountryTransformer::class;
    

    function __construct(CountryRepository $repo)
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
    