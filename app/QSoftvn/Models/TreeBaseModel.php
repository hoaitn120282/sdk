<?php
namespace QSoftvn\Models;
use QSoftvn\Collections\TreeCollection;
use Kalnoy\Nestedset\NodeTrait;

/**
 * This class is the base for tree type widget model
 *
 * This class use \Kalnoy\Nestedset\NodeTrait
 *
 * @package QSoftvn\Models
 */
abstract class TreeBaseModel extends WidgetBaseModel
{
    use NodeTrait;
    /**
     * The field that distinct trees when storing multiple trees in the same table
     * @var null
     */
    public $treeScopeField = null;

    /**
     * The default value for the scopeField that the tree should be filtered by default.
     * @var null
     */
    public $defaultTreeScopeValue = null;

    /**
     * The name of the iconCls for the leaf node
     * @var string
     */
    public $nodeIconCls = '';

    /**
     * The name of the iconCls for the parent node
     * @var string
     */
    public $parentNodeIconCls = '';

    /**
     * The name of indented column used in exporting to excel
     * @var string
     */
    public $indentedColumnName = 'name';

    /**
     * Redefine lft column
     * @return string
     */
    public function getLftName(){
        return 'lft';
    }

    /**
     * Redefine rgt column
     * @return string
     */
    public function getRgtName(){
        return 'rgt';
    }

    /**
     * Setup a new collection with toHierarchy() method.
     * @param array $models
     * @return TreeCollection
     */
    public function newCollection(array $models = array()){
        return new TreeCollection($models);
    }

    /**
     * Setup a custom query builder for nested set.
     * @param $query
     * @return QueryBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new TreeQueryBuilder($query);
    }

    /**
     * The scope that data should be filtered for when storing multiple trees in the same data table.
     * @return array
     */
    protected function getScopeAttributes()
    {
        if($this->treeScopeField){
            return [$this->treeScopeField];
        }
        else{
            return [];
        }
    }
}