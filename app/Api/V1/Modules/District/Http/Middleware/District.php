<?php

namespace App\Api\V1\Modules\District\Http\Middleware;

use Closure;

class District
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}