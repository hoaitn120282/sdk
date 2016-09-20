<?php

namespace App\Api\V1\Modules\Country\Transformer;

use QSoftvn\Transformer\AbstractWidgetTransformer;

class CountryTransformer extends AbstractWidgetTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'alpha2'=>$node['alpha2'],
            'alpha3'=>$node['alpha3'],
            'latitude'=>(float) $node['latitude'],
            'longitude'=>(float) $node['longitude']
        ];
    }
}