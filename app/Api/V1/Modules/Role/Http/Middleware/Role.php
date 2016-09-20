<?php

namespace App\Api\V1\Modules\Role\Http\Middleware;

use Closure;

class Role
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}