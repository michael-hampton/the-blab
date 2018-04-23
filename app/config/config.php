<?php

$settings = [
    'database' => [
        "development" => [
            'adapter' => 'Mysql',
            'host' => 'localhost',
            'username' => 'phalcon',
            'password' => 'Password123',
            'dbname' => 'facebook'
        ],
        "live" => [
            'adapter' => 'Mysql',
            'host' => 'localhost',
            'username' => 'blabx10h_mike',
            'password' => 'Landscaper1985',
            'dbname' => 'blabx10h_facebook'
        ]
    ],
    'app' => [
        'controllersDir' => '../app/controllers/',
        'modelsDir' => '../app/models/',
        'viewsDir' => '../app/views/'
    ],
    'mysetting' => 'the-value'
];
