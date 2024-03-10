<?php

use limb\core\src\lmbEnv;

return [
    'cache' => lmbEnv::get('LIMB_VAR_DIR') . '/compiled/twig/',

    'tmp_dirs' => lmbEnv::get('LIMB_TEMPLATES_INCLUDE_PATH', [dirname(__FILE__) . '/../template']),
    'auto_reload' => true,

    'debug' => true,
];
