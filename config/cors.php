<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |

     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*') 
     | to accept any value, the allowed methods however have to be explicitly listed.
     |
     */
    'supportsCredentials' => false,
    'allowedOrigins' => ['*'],
    'allowedHeaders' => ['Content-Type', 'Accept', 'Authorization', 'Content-Language', 'X-Requested-With'],
    'allowedMethods' => ['GET', 'POST', 'PUT',  'DELETE','OPTIONS'],
    'exposedHeaders' => ['Content-Type', 'Accept', 'Authorization', 'Content-Language', 'X-Requested-With'],
    'maxAge' => 0,
    'hosts' => ['*'],
];

