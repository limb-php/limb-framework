<?php

use limb\core\lmbEnv;

$_SERVER['REQUEST_URI'] = '/test.php';

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/net/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/dbal/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/session/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/common.inc.php');

lmbEnv::set('LIMB_CONTROLLERS_INCLUDE_PATH', 'bit-cms/*/tests/src/controller;bit-cms/*/src/controller;limb/*/src/controller');

lmbEnv::set('BASE_PROJECT_HOST', 'project_for_test.com');
lmbEnv::set('MAX_ID_VALUE', 2147483648);

$LIMB_CONF_INCLUDE_PATH = lmbEnv::get('LIMB_CONF_INCLUDE_PATH');
lmbEnv::set('LIMB_CONF_INCLUDE_PATH', dirname(__FILE__) . '/settings;' . $LIMB_CONF_INCLUDE_PATH);

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/cron');

lmb_tests_init_db_dsn();
lmb_tests_setup_db(dirname(__FILE__) . '/../.fixture/init_tests.');
