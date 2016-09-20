<?php

namespace App\Api\V1\Modules\Organization\Http\Middleware;

use Closure;

class Organization
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}