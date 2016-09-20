<?php

namespace App\Api\V1\Modules\Customer\Repository;

use QSoftvn\Repositories\WidgetBaseRepository;
use Schema;

class CustomerRepository extends WidgetBaseRepository
{

    /**
     * Configure the Models
     *
     **/
    public function model()
    {
        return 'App\Api\V1\Modules\Customer\Entity\Customer';
    }
}