<?php

use limb\core\lmbEnv;

$conf = [

    'log_level' => 'debug',
    'logs' => [
        'error' => 'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/error.log',
        'db' => 'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/db.log',
        'debug' => 'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/debug.log',
    ]

];
