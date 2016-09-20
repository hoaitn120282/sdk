<?php
namespace QSoftvn\Transformer;

use League\Fractal;

/**
 * This class provide abstraction for a tree transformer.
 *
 * To provide transformation definition just override the method formatData
 *
 * @package QSoftvn\Transformer
 */
abstract class AbstractTreeTransformer extends Fractal\TransformerAbstract{

    /**
     * Override this for transformation definition
     * @param $node
     * @return mixed
     */
    public abstract function formatData($node);

    /**
     * Handle necessary properties such as createdAt, createdBy, etc. for the transformation so you do not have to do it again and again in every transformer file.
     * @param $node
     * @return array
     */
    public function transform($node){
        $defaultData = [
            'createdAt'         => $node->created_at,
            'updatedAt'         => $node->updated_at,
            'createdBy'         => $node->created_by,
            'updatedBy'         => $node->updated_by,
            'editable'          => $node->editable,
            'deletable'         => $node->deletable,
            'exportable'        => $node->exportable,
            'childrenCount'     => count($node['children']),
            'leaf'              => count($node['children'])>0?false:true
        ];
        $defaultParentData = [
            'expanded'          => true
        ];
        $item = array_merge($this->formatData($node), $defaultData);
        if (count($node['children'])>0) {
            $item = array_merge($this->formatData($node), $defaultData, $defaultParentData);
            if($node->parentNodeIconCls){
                $item['iconCls'] = $node->parentNodeIconCls;
            }
            foreach ($node->children as $key => $value) {
                $item['children'][] = $this->transform($value);
            }
            return $item;
        } else {
            if($node->nodeIconCls){
                $item['iconCls'] = $node->nodeIconCls;
            }
            return $item;
        }
    }
}