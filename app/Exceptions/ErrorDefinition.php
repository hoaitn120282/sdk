<?php
namespace App\Exceptions;
class ErrorDefinition{
    const UNAUTHORIZED_MESSAGE = 'Access to resource is not permitted';
    const ITEM_NOT_FOUND_MESSAGE = 'Requested data not found';
    const PARENT_ITEM_NOT_FOUND_MESSAGE = 'Parent node not found';
    const BAD_REQUEST_MESSAGE = 'Bad request';

    /** FOR DATA VALIDATION */
    const DATA_REQUIRED_VALIDATION_MESSAGE = '":attribute" is not allowed to be blank';

    /**
     * Define the generic error message for sql error.
     * @return array
     */
    public static function sqlErrors(){
        return array(
            //'42P01'=>'Tuyen beo'
        );
    }
}