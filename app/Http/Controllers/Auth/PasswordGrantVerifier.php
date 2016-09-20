<?php
namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * This class implement the password verification.
 * @package App\Http\Controllers\Auth
 */
class PasswordGrantVerifier
{
    private $_params;

    function __construct(Request $request)
    {
        $this->_params = $request->all();
    }
    public function verify($username, $password)
    {
        $credentials = [
            'email'    => $username,
            'password' => $password,
            'active'=>true
        ];

        if (Auth::once($credentials)) {
            return Auth::user()->id;
        }
        else{
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('Invalid credential');
        }
        return false;
    }
}