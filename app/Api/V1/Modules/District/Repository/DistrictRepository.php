<?php

namespace App\Api\V1\Modules\District\Repository;

use QSoftvn\Repositories\WidgetBaseRepository;
use Schema;

class DistrictRepository extends WidgetBaseRepository
{

    /**
     * Configure the Models
     *
     **/
    public function model()
    {
        return 'App\Api\V1\Modules\District\Entity\District';
    }
}