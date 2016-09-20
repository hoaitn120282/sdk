<?php

namespace App\Api\V1\Modules\Ward\Http\Middleware;

use Closure;

class Ward
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}