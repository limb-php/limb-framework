<?php

if (empty($conf))
   $conf = array();

$conf = [

    'ControllerActionId' => [
        'path' => '/:controller/:action/:id',
        'defaults' => array('namespace' => 'tests\cms\cases\controller', 'action' => 'display'),
    ],

    'ControllerAction' => [
        'path' => '/:controller/:action',
        'defaults' => array('namespace' => 'tests\cms\cases\controller', 'action' => 'display'),
    ],

    'Controller' => [
        'path' => '/:controller',
        'defaults' => array('namespace' => 'tests\cms\cases\controller'),
    ]

];
