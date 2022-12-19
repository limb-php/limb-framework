<?php
require_once(dirname(__FILE__) . '/common.inc.php');
require(dirname(__FILE__) . '/../../dbal/cases/init.inc.php');

use limb\core\src\lmbEnv;
use limb\dbal\src\lmbDbDump;
use limb\toolkit\src\lmbToolkit;

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/cron');

$_ENV['LIMB_CONTROLLERS_INCLUDE_PATH'] = 'bit-cms/*/tests/src/controller;bit-cms/*/src/controller;limb/*/src/controller';
$_ENV['LIMB_CONF_INCLUDE_PATH'] = 'tests/*/settings;bit-cms/*/settings;src/limb/*/settings';

$_ENV['BASE_PROJECT_HOST'] = 'project_for_test.com';
$_ENV['MAX_ID_VALUE'] = 2147483648;

$_SERVER['REQUEST_URI'] = '/test.php';

lmb_tests_init_db_dsn();

lmb_tests_setup_db(dirname(__FILE__) . '/.fixture/init_tests.');

/*$type = lmbToolkit::instance()->getDefaultDbConnection()->getType();

$dump = new lmbDbDump(dirname(__FILE__) . '/../fixture/init_tests.' . $type);
$dump->load();
*/
