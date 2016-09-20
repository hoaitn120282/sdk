<?php
namespace App\Providers;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Route;
use Dingo\Api\Auth\Provider\Authorization;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CustomAuthServiceProvider extends Authorization
{
    public function authenticate(Request $request, Route $route)
    {
        $this->validateAuthorizationHeader($request);
        // If the authorization header passed validation we can continue to authenticate.
        // If authentication then fails we must throw the UnauthorizedHttpException.
    }
    //Default is bearer. No need return anything.
    public function getAuthorizationMethod()
    {
        return '';
    }
}