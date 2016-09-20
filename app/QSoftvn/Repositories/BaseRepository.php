<?php namespace QSoftvn\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;

/**
 * Class BaseRepository
 *
 * This class is the base class for all repository in QSDK Laravel
 *
 * It extends Bosnadev\Repositories\Eloquent\Repository
 *
 * @package QSoftvn\Repositories
 */
abstract class BaseRepository extends Repository{
    /**
     * Get the model of the repository
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel(){
        return $this->model;
    }
}
