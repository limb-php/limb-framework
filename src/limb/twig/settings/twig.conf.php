<?php

use limb\core\src\lmbEnv;

$conf = array(
  'cache' => lmbEnv::get('LIMB_VAR_DIR') . '/compiled/twig/',

  'tmp_dirs' => lmbEnv::get('LIMB_TEMPLATES_INCLUDE_PATH', array('../template')),
  'auto_reload' => false,

  'debug' => false,
);