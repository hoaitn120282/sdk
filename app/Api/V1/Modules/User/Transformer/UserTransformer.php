<?php

namespace App\Api\V1\Modules\User\Transformer;

use QSoftvn\Transformer\AbstractWidgetTransformer;

class UserTransformer extends AbstractWidgetTransformer{
    public function formatData($node){
        return [
            'id'=>(int) $node['id'],
            'name'=>$node['name'],
            'email'=>$node['email'],
            'password'=>$node['password'],
            'avatar'=>$node['avatar'],
            'note'=>$node['note'],
            'active'=>(bool) $node['active'],
            'permissions'=>$node['permissions'],
            'createdByUser'=>array(
                'id'                => $node['createdby_id'],
                'name'              => $node['createdbyuser_name'],
                'email'             => $node['createdbyuser_email'],
            ),
            'updatedByUser'=>array(
                'id'                => $node['updatedbyuser_id'],
                'name'              => $node['updatedbyuser_name'],
                'email'             => $node['updatedbyuser_email'],
            ),
            'roles' =>              $this->mergedRoles($node['roles']),
            'roleNames' =>              $this->mergedRoleNames($node['roles'])
        ];
    }
    protected function mergedRoles($roles){
        $r = [];
        foreach($roles as $role){
            array_push($r, $role['roles_id']);
        }
        return $r;
    }
    protected function mergedRoleNames($roles){
        $r = [];
        foreach($roles as $role){
            array_push($r, $role['roles_name']);
        }
        return $r;
    }
}