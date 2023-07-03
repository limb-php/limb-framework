<?php

use limb\core\src\lmbEnv;
use limb\dbal\src\toolkit\lmbDbTools;
use limb\toolkit\src\lmbToolkit;

lmbEnv::set('LIMB_CONF_INCLUDE_PATH', 'search/*/settings;*/settings;bit-cms/*/settings;limb/*/settings');

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/search/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/common.inc.php');

lmbToolkit::merge(new lmbDbTools());

lmb_tests_init_var_dir(dirname(__FILE__).'/../../../var/search');

lmb_tests_init_db_dsn();

lmb_tests_setup_db(dirname(__FILE__) . '/fixture/init_tests.');
