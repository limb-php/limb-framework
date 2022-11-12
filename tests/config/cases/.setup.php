<?php
require(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require(dirname(__FILE__) . '/../../core/cases/init.inc.php');

use limb\core\src\lmbEnv;

lmb_tests_init_var_dir(dirname(__FILE__).'/../../../var/config');

lmbEnv::setor('LIMB_CONF_INCLUDE_PATH', 'settings;limb/*/settings;limb/config/tests/cases/settings');
