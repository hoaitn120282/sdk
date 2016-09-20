<?php

namespace App\Api\V1\Modules\Role\Transformer;

use QSoftvn\Transformer\AbstractWidgetTransformer;

class RoleFlatTransformer extends AbstractWidgetTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'description'=>$node['description'],
            'isActive'=>(bool) $node['is_active'],
            'type'=>$node['type'],
            'createdByUser'=>array(
                'id'                => $node['createdbyuser_id'],
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