<?php

namespace App\Api\V1\Modules\Organization\Transformer;

use QSoftvn\Transformer\AbstractWidgetTransformer;

class OrganizationFlatTransformer extends AbstractWidgetTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'description'=>$node['description'],
            'isActive'=>(bool) $node['is_active'],
            'type'=>$node['type']
        ];
    }
}