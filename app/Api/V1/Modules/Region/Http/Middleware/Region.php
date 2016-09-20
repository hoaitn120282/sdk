<?php

namespace App\Api\V1\Modules\Region\Http\Middleware;

use Closure;

class Region
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}