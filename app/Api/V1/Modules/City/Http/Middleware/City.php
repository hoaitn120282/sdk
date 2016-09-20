<?php

namespace App\Api\V1\Modules\City\Http\Middleware;

use Closure;

class City
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}