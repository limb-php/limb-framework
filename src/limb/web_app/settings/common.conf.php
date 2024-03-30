<?php

use limb\core\src\lmbEnv;

$conf = [

    'logs' => [
        'error' => 'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/error.log',
        'db' => 'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/db.log',
    ]

];
