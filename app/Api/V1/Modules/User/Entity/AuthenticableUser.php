<?php

namespace  App\Api\V1\Modules\User\Entity;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AuthenticableUser extends Authenticatable
{
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email','password','avatar','note','reset_token','reset_date','secret_key','active','created_by','updated_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'reset_token', 'reset_date', 'secret_key'
    ];
}
