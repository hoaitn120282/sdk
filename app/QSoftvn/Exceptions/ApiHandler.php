<?php

namespace QSoftvn\Exceptions;

use App\Exceptions\ErrorDefinition;
use Dingo\Api\Exception\Handler as ExceptionHandler;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * This class handle the API exception
 * @package QSoftvn\Exceptions
 */
class ApiHandler extends ExceptionHandler
{
    /**
     * Render the error.
     *
     * If error is type of query exception, it will look for definition of error code via ErrorDefinition::sqlErrors() and display corresponding message
     *
     * @param \Dingo\Api\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //Add this header to show real error to satisfy CORS
        if(app('config')['api']['debug']==true){
            header("Access-Control-Allow-Origin: *");
        }
        switch ($exception) {
            case ($exception instanceof \Illuminate\Database\QueryException):
                $errorMessage = $this->outputSqlErrorMessage($exception);
                if($errorMessage!==false){
                    throw new HttpException(422,$errorMessage);
                }
                break;
            default:
                break;
        }
        return $this->handle($exception);
    }

    /**
     * Read the exception and return sql_state
     * @param $exception
     * @return bool
     */
    public function outputSqlErrorMessage($exception)
    {
        //$sql = $exception->getSql();
        $bindings = $exception->getBindings();

        // Process the query's SQL and parameters and create the exact query
        foreach ($bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else {
                if (is_string($binding)) {
                    $bindings[$i] = "'$binding'";
                }
            }
        }
        /*$query = str_replace(array('%', '?'), array('%%', '%s'), $sql);
        $query = vsprintf($query, $bindings);*/

        $errorInfo = $exception->errorInfo;
        /*$data = [
            'sql'        => $query,
            'message'    => isset($errorInfo[2]) ? $errorInfo[2] : '',
            'sql_state'  => $errorInfo[0],
            'error_code' => $errorInfo[1]
        ];*/

        $errorDefs = ErrorDefinition::sqlErrors();

        if(isset($errorDefs[$errorInfo[0]])){
            return $errorDefs[$errorInfo[0]];
        }
        else{
            return false;
        }
    }
}
