<?php
namespace QSoftvn\Transformer;

use League\Fractal;

/**
 * This class provide abstraction for data transformer
 * @package QSoftvn\Transformer
 */
abstract class AbstractWidgetTransformer extends Fractal\TransformerAbstract
{
    /**
     * Override this method to provide transformation definition
     * @param $node
     * @return mixed
     */
    public abstract function formatData($node);

    /**
     * This method provide common data transformation such as createdAt, createdBy, etc. so you do not have to re-declare it on every transformer file
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
            'editableFields'    => explode(',',$node->editablefields),
            'deletable'         => $node->deletable,
            'exportable'        => $node->exportable
        ];
        $item = array_merge($this->formatData($node), $defaultData);
        return $item;
    }
}