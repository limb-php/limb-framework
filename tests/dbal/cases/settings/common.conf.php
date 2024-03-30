<?php

use limb\core\src\lmbEnv;

$conf = [

    'logs' => [
        'db' => 'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/db.log',
    ]

];
