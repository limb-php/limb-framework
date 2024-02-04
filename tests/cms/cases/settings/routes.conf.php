<?php

return [

    'ControllerActionId' => [
        'path' => '/:controller/:action/:id',
        'defaults' => array(
            'namespace' => 'Tests\cms\cases\Controllers',
            'action' => 'display'
        ),
    ],

    'ControllerAction' => [
        'path' => '/:controller/:action',
        'defaults' => array(
            'namespace' => 'Tests\cms\cases\Controllers',
            'action' => 'display'
        ),
    ],

    'Controller' => [
        'path' => '/:controller',
        'defaults' => array(
            'namespace' => 'Tests\cms\cases\Controllers'
        ),
    ]

];
