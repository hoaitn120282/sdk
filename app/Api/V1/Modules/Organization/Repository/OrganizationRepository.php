<?php

namespace App\Api\V1\Modules\Organization\Repository;

use QSoftvn\Repositories\TreeBaseRepository;
use Schema;

class OrganizationRepository extends TreeBaseRepository
{

    /**
     * Configure the Models
     *
     **/
    public function model()
    {
        return 'App\Api\V1\Modules\Organization\Entity\Organization';
    }
}