<?php

namespace App\Api\V1\Modules\Customer\Transformer;

use QSoftvn\Transformer\AbstractWidgetTransformer;

class CustomerTransformer extends AbstractWidgetTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'shortName'=>$node['short_name'],
            'regNumber'=>$node['reg_number'],
            'regDate'=>$node['reg_date'],
            'taxNumber'=>$node['tax_number'],
            'regAddress'=>$node['reg_address'],
            'email'=>$node['email'],
            'phone'=>$node['phone'],
            'fax'=>$node['fax'],
            'website'=>$node['website'],
            'logo'=>$node['logo'],
            'billingTo'=>$node['billing_to'],
            'billingAddress'=>$node['billing_address'],
            'billingEmail'=>$node['billing_email'],
            'country'=>array(
                    'id'                => $node['country_id'],
                    'name'              => $node['country_name'],
            ),
            'customerSource'=>array(
                    'id'                => $node['customer_source_id'],
                    'name'              => $node['customer_source_name'],
            ),
            'businessDomain'=>array(
                    'id'                => $node['business_domain_id'],
                    'name'              => $node['business_domain_name'],
            ),
            'verified'=>(bool) $node['verified'],
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