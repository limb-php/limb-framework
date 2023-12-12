<?php

use limb\core\src\lmbEnv;

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');

$LIMB_CONF_INCLUDE_PATH = lmbEnv::get('LIMB_CONF_INCLUDE_PATH');
lmbEnv::set('LIMB_CONF_INCLUDE_PATH', $LIMB_CONF_INCLUDE_PATH . ';' . __DIR__ . '/settings;' . __DIR__ . '/../../*/settings');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/config');
