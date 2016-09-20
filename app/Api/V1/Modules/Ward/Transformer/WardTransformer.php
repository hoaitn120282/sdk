<?php

namespace App\Api\V1\Modules\Ward\Transformer;

use QSoftvn\Transformer\AbstractWidgetTransformer;

class WardTransformer extends AbstractWidgetTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'latitude'=>(float) $node['latitude'],
            'longitude'=>(float) $node['longitude'],
            'district'=>array(
                    'id'                => $node['districts_id'],
                    'name'              => $node['districts_name'],
            )
        ];
    }
}