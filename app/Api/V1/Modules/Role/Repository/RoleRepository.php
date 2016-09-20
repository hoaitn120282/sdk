<?php

namespace App\Api\V1\Modules\Role\Repository;

use QSoftvn\Repositories\TreeBaseRepository;
use Schema;

class RoleRepository extends TreeBaseRepository
{

    /**
     * Configure the Models
     *
     **/
    public function model()
    {
        return 'App\Api\V1\Modules\Role\Entity\Role';
    }
}