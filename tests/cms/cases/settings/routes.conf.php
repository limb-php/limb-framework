<?php

return [

    'ControllerActionId' => [
        'path' => '/:controller/:action/:id',
        'defaults' => array(
            'namespace' => 'tests\cms\cases\src\Controllers',
            'action' => 'display'
        ),
    ],

    'ControllerAction' => [
        'path' => '/:controller/:action',
        'defaults' => array(
            'namespace' => 'tests\cms\cases\src\Controllers',
            'action' => 'display'
        ),
    ],

    'Controller' => [
        'path' => '/:controller',
        'defaults' => array(
            'namespace' => 'tests\cms\cases\src\Controllers'
        ),
    ]

];
