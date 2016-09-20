<?php
/**
 * This class override the nestedset Query builder for being able to join data with other tables.
 */
namespace QSoftvn\Models;
use Kalnoy\Nestedset\NestedSet;
use Kalnoy\Nestedset\QueryBuilder;

/**
 * This class provide custom query builder for tree
 * @package QSoftvn\Models
 */
class TreeQueryBuilder extends QueryBuilder{

    /**
     * Override the Kalnoy\Nestedset\QueryBuilder method to provide better implementation of query builder that use the column format of table.column
     * @param mixed $id
     * @return $this
     */
    public function whereAncestorOf($id)
    {
        $keyName = $this->model->getTable().'.'.$this->model->getKeyName();

        if (NestedSet::isNode($id)) {
            $value = '?';

            $this->query->addBinding($id->getLft());

            $id = $id->getKey();
        } else {
            $valueQuery = $this->model
                ->newQuery()
                ->toBase()
                ->select("_.".$this->model->getLftName())
                ->from($this->model->getTable().' as _')
                ->where($keyName, '=', $id)
                ->limit(1);

            $this->query->mergeBindings($valueQuery);

            $value = '('.$valueQuery->toSql().')';
        }

        list($lft, $rgt) = $this->wrappedColumns();

        $this->query->whereRaw("{$value} between {$lft} and {$rgt}");

        // Exclude the node
        $this->where($keyName, '<>', $id);

        return $this;
    }
}