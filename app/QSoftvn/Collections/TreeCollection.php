<?php
namespace QSoftvn\Collections;

use Kalnoy\Nestedset\Collection;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

/**
 * This class is the improved tree collection which override Dingo API collection
 * @package QSoftvn\Collections
 */
class TreeCollection extends Collection
{
    /**
     * Add this method for populating all nodes within query range, not just valid tree root
     * @param bool $root
     * @return static
     */
    public function toHierarchy($root = false)
    {
        if ($this->isEmpty()) {
            return new static;
        }

        $this->linkSubNodes();
        return new static($this->items);
    }

    /**
     * Rewrite the linkNodes method for unsetting the children item
     * @return $this
     */
    public function linkSubNodes()
    {
        if ($this->isEmpty()) return $this;

        $groupedNodes = $this->groupBy($this->first()->getParentIdName());

        foreach ($this->items as $node) {
            if ( ! $node->getParentId()) {
                $node->setRelation('parent', null);
            }

            $children = $groupedNodes->get($node->getKey(), [ ]);

            foreach ($children as $child) {
                $child->setRelation('parent', $node);
            }

            $node->setRelation('children', BaseCollection::make($children));
            if(is_object($children)){
                foreach($children as $child){
                    $k = array_search($child, $this->items,true);
                    if($k!==false){
                        unset($this->items[$k]);
                    }
                }
            }
        }

        return $this;
    }
}