<?php
/**
 * The login controller
 */
namespace App\Http\Controllers\Auth;

use QSoftvn\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use DB;

/**
 * This controller provide the convenient way to process the login, logout method.
 * @package App\Http\Controllers\Auth
 */
class LoginController extends BaseController
{
    /**
     * Login action
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        $auth = \Authorizer::issueAccessToken();
        $result = [];
        $input = $request->all();
        if(isset($input['remember_me']) && $input['remember_me'] == true){
            $remember_token = config('app.remember_token');
            $auth['expires_in'] = $remember_token*(60*60*24);
            DB::table('oauth_access_tokens')->where('id', $auth['access_token'])
                ->update(['expire_time' => ($auth['expires_in'] + time())]);
        }
        //Get user info
        $userInfo = [
            'account_id' => \Auth::user()->id,
            'fullname' => \Auth::user()->name,
            'username'=>\Auth::user()->email,
            'access_token' => $auth['access_token'],
            'token_type' => $auth['token_type'],
            'expires_in' => $auth['expires_in']
        ];
        $result['data'] = $userInfo;
        $result['success']=true;
        $result['message']='Login Successfully';

        return response()->json($result);
    }
}