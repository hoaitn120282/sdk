<?php

namespace App\Api\V1\Modules\Region\Transformer;

use QSoftvn\Transformer\AbstractWidgetTransformer;

class RegionTransformer extends AbstractWidgetTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'latitude'=>(float) $node['latitude'],
            'longitude'=>(float) $node['longitude'],
            'country'=>array(
                    'id'                => $node['countries_id'],
                    'name'                => $node['countries_name'],
            )
        ];
    }
}