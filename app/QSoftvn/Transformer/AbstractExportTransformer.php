<?php
namespace QSoftvn\Transformer;

use League\Fractal;

/**
 * This class provide abstraction for data exporting transformer
 * @package QSoftvn\Transformer
 */
abstract class AbstractExportTransformer extends Fractal\TransformerAbstract
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
        $defaultData = [];
        $item = array_merge($this->formatData($node), $defaultData);
        return $item;
    }
}