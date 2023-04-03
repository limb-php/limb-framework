<?php
require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/session/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/session/common.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/session/');

lmb_tests_init_db_dsn();
lmb_tests_setup_db(dirname(__FILE__) . '/.fixture/init_tests.');