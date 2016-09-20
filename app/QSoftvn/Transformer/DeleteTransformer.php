<?php
namespace QSoftvn\Transformer;

use League\Fractal;

/**
 * This class provide data transformer for delete response method
 * @package QSoftvn\Transformer
 */
class DeleteTransformer extends Fractal\TransformerAbstract
{
    public function transform($node){
        $item = [
            //'success'           => $node->success
        ];
        return $item;
    }
}