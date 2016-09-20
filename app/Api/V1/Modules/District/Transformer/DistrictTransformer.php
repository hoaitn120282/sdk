<?php

namespace App\Api\V1\Modules\District\Transformer;

use QSoftvn\Transformer\AbstractWidgetTransformer;

class DistrictTransformer extends AbstractWidgetTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'latitude'=>(float) $node['latitude'],
            'longitude'=>(float) $node['longitude'],
            'region'=>array(
                    'id'                => $node['regions_id'],
                    'name'              => $node['regions_name'],
            )
        ];
    }
}