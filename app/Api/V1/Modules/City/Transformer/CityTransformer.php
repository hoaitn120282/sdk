<?php

namespace App\Api\V1\Modules\City\Transformer;

use QSoftvn\Transformer\AbstractWidgetTransformer;

class CityTransformer extends AbstractWidgetTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'latitude'=>(float) $node['latitude'],
            'longitude'=>(float) $node['longitude'],
            'region'=>array(
                'id'                => $node['region_id'],
                'name'              => $node['region_name'],
            ),
            'createdByUser'=>array(
                'id'                => $node['createdby_id'],
                'name'              => $node['createdbyuser_name'],
                'email'             => $node['createdbyuser_email'],
            ),
            'updatedByUser'=>array(
                'id'                => $node['updatedbyuser_id'],
                'name'              => $node['updatedbyuser_name'],
                'email'             => $node['updatedbyuser_email'],
            )
        ];
    }
}