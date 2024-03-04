<?php

use limb\core\src\lmbEnv;

$conf = array(
    'debug_enabled' => true,
    'profile_enabled' => false,

    'temp_file_storage' => array(
        'max_file_size' => 5000000, //-- 5Mb
        'file_ttl' => 3, // 3 seconds
        'store_rules' => array(
            'path' => lmbEnv::get('LIMB_VAR_DIR') . 'temp_storage/',
            'url' => '/temp_storage/',
        ),
    ),

    'static_files_version' => 1,
);
