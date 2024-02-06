<?php

use limb\core\src\lmbEnv;
use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\cms\src\toolkit\lmbCmsTools;

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/common.inc.php');

$LIMB_CONF_INCLUDE_PATH = lmbEnv::get('LIMB_CONF_INCLUDE_PATH');
lmbEnv::set('LIMB_CONF_INCLUDE_PATH', $LIMB_CONF_INCLUDE_PATH . ';' . dirname(__FILE__) . '/settings;' . dirname(__FILE__) . '/../../*/cases/settings');

lmbToolkit::merge(new lmbWebAppTools());
lmbToolkit::merge(new lmbCmsTools());

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/cms');

lmb_tests_init_db_dsn();

lmb_tests_setup_db(dirname(__FILE__) . '/fixture/init_tests.');
