<?php

$conf = [

    'TestsControllerActionId' => [
        'path' => '/tests/:controller/:action/:id',
        'defaults' => [
            'namespace' => 'tests\web_app\cases\plain\src\Controllers',
            'action' => 'display'
        ]
    ],

    'TestsControllerAction' => [
        'path' => '/tests/:controller/:action',
        'defaults' => [
            'namespace' => 'tests\web_app\cases\plain\src\Controllers',
            'action' => 'display'
        ]
    ],

    'TestsController' => [
        'path' => '/tests/:controller',
        'defaults' => [
            'namespace' => 'tests\web_app\cases\plain\src\Controllers'
        ]
    ],

    'ControllerActionId' => [
        'path' => '/:controller/:action/:id',
        'defaults' => [
            'namespace' => 'limb\web_app\src\Controllers',
            'action' => 'display'
        ]
    ],

    'ControllerAction' => [
        'path' => '/:controller/:action',
        'defaults' => [
            'namespace' => 'limb\web_app\src\Controllers',
            'action' => 'display'
        ]
    ],

    'Controller' => [
        'path' => '/:controller',
        'defaults' => [
            'namespace' => 'limb\web_app\src\Controllers'
        ]
    ]

];
