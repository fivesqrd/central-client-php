<?php

return [

    'central' => [
        'namespace' => env('CENTRAL_NAMESPACE'),
        'adapter'   => env('CENTRAL_ADAPTER', 'Aws'), // Aws|Bego
        'options'   => [ // Adapter specific options
            'table'     => env('CENTRAL_TABLE'), // Required by both Bego and Aws
            'client'    => null,
            'marshaler' => null
        ],
        'aws'       => [
            'version' => env('AWS_VERSION', '2012-08-10'),
            'region'  => env('AWS_REGION', 'eu-west-1'),
            'credentials' => [
                'key'    => env('AWS_KEY'),
                'secret' => env('AWS_SECRET'),
            ],
            'endpoint' => env('AWS_ENDPOINT') ?: null
        ], 
    ],

];