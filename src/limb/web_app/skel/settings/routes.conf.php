<?php

$conf = [

    'Main' => [
        'path' => '/',
        'defaults' => [
            'controller' => 'MainPageController',
        ]
    ],

    // Common routes, should be included AFTER yours
    'ControllerActionId' => [
        'path' => '/:controller/:action/:id',
        'defaults' => [
            'namespace' => 'limb\web_app\src\controller',
            'action' => 'display'
        ]
    ],

    'ControllerAction' => [
        'path' => '/:controller/:action',
        'defaults' => [
            'namespace' => 'limb\web_app\src\controller',
            'action' => 'display'
        ]
    ],

    'Controller' => [
        'path' => '/:controller',
        'defaults' => [
            'namespace' => 'limb\web_app\src\controller'
        ]
    ]

];
