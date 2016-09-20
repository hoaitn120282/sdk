<?php

namespace App\Api\V1\Modules\Ward\Repository;

use QSoftvn\Repositories\WidgetBaseRepository;
use Schema;

class WardRepository extends WidgetBaseRepository
{

    /**
     * Configure the Models
     *
     **/
    public function model()
    {
        return 'App\Api\V1\Modules\Ward\Entity\Ward';
    }
}