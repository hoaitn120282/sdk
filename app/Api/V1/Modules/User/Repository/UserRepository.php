<?php

namespace App\Api\V1\Modules\User\Repository;

use QSoftvn\Helper\Helper;
use Illuminate\Support\Facades\Hash;
use QSoftvn\Repositories\WidgetBaseRepository;
use Schema;

class UserRepository extends WidgetBaseRepository
{

    /**
     * Configure the Models
     *
     **/
    public function model()
    {
        return 'App\Api\V1\Modules\User\Entity\User';
    }
}