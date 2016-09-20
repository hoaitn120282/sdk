<?php

namespace App\Api\V1\Modules\Country\Repository;

use QSoftvn\Repositories\WidgetBaseRepository;
use Schema;

class CountryRepository extends WidgetBaseRepository
{

    /**
     * Configure the Models
     *
     **/
    public function model()
    {
        return 'App\Api\V1\Modules\Country\Entity\Country';
    }
}