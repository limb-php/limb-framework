<?php

use limb\core\src\lmbEnv;

lmbEnv::set('LIMB_CONF_INCLUDE_PATH', 'cache2/*/settings;*/settings;bit-cms/*/settings;limb/*/settings');

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/session/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/cache2/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/common.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/cache2');

lmb_tests_init_db_dsn();