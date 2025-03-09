<?php

use Limb\Core\lmbEnv;
use Psr\Log\LogLevel;

$conf = [

    'logs' => [
        'error' => [
            'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/error.log'
        ],

        'db' => [
            'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/db.log' => LogLevel::NOTICE
        ],
    ]

];
