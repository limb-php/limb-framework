<?php
require(dirname(__FILE__) . '/../../common.inc.php');

require('limb/core/tests/cases/init.inc.php');
lmbEnv::set('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var/cms');

require('limb/dbal/tests/cases/init.inc.php');
lmb_tests_init_db_dsn();

lmb_tests_setup_db(dirname(__FILE__) . '/../../init/db.');
lmb_tests_setup_db(dirname(__FILE__) . '/fixture/init_tests.');
