<?php

namespace QSoftvn\Middlewares;

use Closure;
use QSoftvn\Helper\Helper;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * This class is the middleware to check for authorized HTTP methods and routes before QSDK process request
 * @package QSoftvn\Middlewares
 */
class Permission
{
    /**
     * Override the handle method for the middleware
     * @param $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Helper::isSuperAdmin()===true){
            return $next($request);
        }
        $myAccessibleRoutes = Helper::getAccessibleRoutesForMe();
        $controller = str_replace('\\','__',$request->route()->getAction()['controller']);
        $methods = $request->route()->getMethods();
        $allRoutes = Helper::getAllRoutes();
        if(in_array($controller,$myAccessibleRoutes)){
            $validMethod = $allRoutes[$controller]['method'];
            if(in_array($validMethod,$methods)){
                return $next($request);
            }
            else{
                throw new UnauthorizedHttpException('Not allowed!');
            }
        }
        else{
            throw new UnauthorizedHttpException('Not allowed!');
        }
    }
}