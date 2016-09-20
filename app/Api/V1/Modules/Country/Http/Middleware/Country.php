<?php

namespace App\Api\V1\Modules\Country\Http\Middleware;

use Closure;

class Country
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}