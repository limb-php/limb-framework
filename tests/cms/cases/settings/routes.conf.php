<?php

if (empty($conf))
    $conf = array();

$conf = [

    'ControllerActionId' => [
        'path' => '/:controller/:action/:id',
        'defaults' => array('namespace' => 'tests\cms\cases\Controllers', 'action' => 'display'),
    ],

    'ControllerAction' => [
        'path' => '/:controller/:action',
        'defaults' => array('namespace' => 'tests\cms\cases\Controllers', 'action' => 'display'),
    ],

    'Controller' => [
        'path' => '/:controller',
        'defaults' => array('namespace' => 'tests\cms\cases\Controllers'),
    ]

];
