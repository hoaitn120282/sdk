<?php
namespace QSoftvn\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Symfony\Component\Console\Exception\LogicException;

/**
 *
 * This class is the base for QSDK Laravel model. Model is a wrapper for a table.
 *
 * @package QSoftvn\Models
 */
abstract class BaseModel extends Model
{
    /**
     * By default, using with will not output join statement so we cannot perform the filter basing on relationship. We need this scope to add actual join to a model.
     *
     * Usage
     *
     *     $model->modelJoin('name');
     *
     * @param $query
     * @param $relation_name
     * @param $viewableFields
     * @param string $operator
     * @param string $type
     * @param bool $where
     * @return mixed
     */
    public function scopeModelJoin($query, $relation_name, $viewableFields=array('*'), $operator = '=', $type = 'left', $where = false) {
        $relation = $this->$relation_name();
        $prefix = strtolower($relation_name);
        $relationEntity = $relation->getRelated();
        $table = $relationEntity->getTable();
        //One to many relationship
        if(method_exists($relation, 'getQualifiedForeignKey')){ //BelongTo
            $one = $relation->getQualifiedForeignKey();
            $two = str_replace($table,$prefix,$relation->getQualifiedOtherKeyName());
            //If viewable fields is not allowed the relationship
            if($viewableFields!=array('*')){
                if(!in_array($one,$viewableFields)){
                    return $query;
                }
            }
            foreach (\Schema::getColumnListing($table) as $related_column) {
                if(isset($relationEntity->hidden)){
                    if(!in_array($related_column, $relationEntity->hidden)){
                        $query->addSelect(new Expression("$prefix.$related_column AS $prefix".'_'."$related_column"));
                    }
                }
                else{
                    $query->addSelect(new Expression("$prefix.$related_column AS $prefix".'_'."$related_column"));
                }

            }
            return $query->join(new Expression($table." as ".$prefix), $one, $operator, new Expression($two), $type, $where);
        }
        else if(method_exists($relation, 'getParentKey')){ //hasMany
            return $query->with([$relation_name=>function($q) use ($where, $relationEntity, $table, $prefix){
                foreach (\Schema::getColumnListing($table) as $related_column) {
                    if(isset($relationEntity->hidden)){
                        if(!in_array($related_column, $relationEntity->hidden)){
                            //$q->addSelect(new Expression("$prefix.$related_column AS $prefix".'_'."$related_column"));
                            $q->addSelect(new Expression($related_column));
                        }
                    }
                    else{
                        //$q->addSelect(new Expression("$prefix.$related_column AS $prefix".'_'."$related_column"));
                        $q->addSelect(new Expression($related_column));
                    }

                }
                if($where){
                    $q->where($where);
                }
            }]);
        }
        else { //belongToMany
            return $query->with([$relation_name=>function($q) use ($where, $relationEntity, $table, $prefix){
                foreach (\Schema::getColumnListing($table) as $related_column) {
                    if(isset($relationEntity->hidden)){
                        if(!in_array($related_column, $relationEntity->hidden)){
                            $q->addSelect(new Expression("$prefix.$related_column AS $prefix".'_'."$related_column"));
                            //$q->addSelect(new Expression($related_column));
                        }
                    }
                    else{
                        $q->addSelect(new Expression("$prefix.$related_column AS $prefix".'_'."$related_column"));
                        //$q->addSelect(new Expression($related_column));
                    }

                }
                if($where){
                    $q->where($where);
                }
            }]);
        }


    }

    /**
     * Before insert/update new data, you may convert the data. You can override this method and return the data after converted.
     * @param $data
     * @param string $action
     * @return mixed
     */
    public function convertUpdatableData($data, $action='add'){
        return $data;
    }
}