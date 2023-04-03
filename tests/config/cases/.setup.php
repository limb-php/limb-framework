<?php

use limb\core\src\lmbEnv;

lmbEnv::setor('LIMB_CONF_INCLUDE_PATH', 'settings;limb/*/settings;limb/config/tests/cases/settings');

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__).'/../../../var/config');
