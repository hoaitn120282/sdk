<?php

namespace App\Api\V1\Modules\Region\Repository;

use QSoftvn\Repositories\WidgetBaseRepository;
use Schema;

class RegionRepository extends WidgetBaseRepository
{

    /**
     * Configure the Models
     *
     **/
    public function model()
    {
        return 'App\Api\V1\Modules\Region\Entity\Region';
    }
}