<?php

namespace App\Api\V1\Modules\City\Repository;

use QSoftvn\Repositories\WidgetBaseRepository;
use Schema;

class CityRepository extends WidgetBaseRepository
{

    /**
     * Configure the Models
     *
     **/
    public function model()
    {
        return 'App\Api\V1\Modules\City\Entity\City';
    }
}