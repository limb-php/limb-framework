<?php
require(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');

require(dirname(__FILE__) . '/../../core/cases/init.inc.php');
require(dirname(__FILE__) . '/../../dbal/cases/init.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\toolkit\lmbWebAppTools;

lmbToolkit::merge(new lmbWebAppTools());

lmb_tests_init_var_dir(dirname(__FILE__).'/../../../var/cms');

lmb_tests_init_db_dsn();

lmb_tests_setup_db(dirname(__FILE__) . '/fixture/init_tests.');
