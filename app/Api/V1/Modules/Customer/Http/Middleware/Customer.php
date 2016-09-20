<?php

namespace App\Api\V1\Modules\Customer\Http\Middleware;

use Closure;

class Customer
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}