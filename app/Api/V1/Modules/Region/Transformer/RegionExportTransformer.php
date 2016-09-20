<?php

namespace App\Api\V1\Modules\Region\Transformer;

use QSoftvn\Transformer\AbstractExportTransformer;

class RegionExportTransformer extends AbstractExportTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'latitude'=>(float) $node['latitude'],
            'longitude'=>(float) $node['longitude'],
            'country_name'=>$node['country_name']
        ];
    }
}