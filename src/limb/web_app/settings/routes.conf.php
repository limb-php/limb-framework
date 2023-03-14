<?php

if (empty($conf))
   $conf = array();

$conf = [

    'ControllerActionId' => [
        'path' => '/:controller/:action/:id',
        'defaults' => array('namespace' => 'limb\web_app\src\controller', 'action' => 'display'),
    ],

    'ControllerAction' => [
        'path' => '/:controller/:action',
        'defaults' => array('namespace' => 'limb\web_app\src\controller', 'action' => 'display'),
    ],

    'Controller' => [
        'path' => '/:controller',
        'defaults' => array('namespace' => 'limb\web_app\src\controller'),
    ]

];
