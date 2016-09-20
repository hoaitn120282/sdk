<?php

namespace App\Api\V1\Modules\Organization\Transformer;

use QSoftvn\Transformer\AbstractTreeTransformer;

class OrganizationTransformer extends AbstractTreeTransformer{
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