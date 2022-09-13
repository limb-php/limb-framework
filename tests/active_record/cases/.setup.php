<?php
require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');

require_once('tests/core/cases/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/active_record');

require_once('tests/dbal/cases/init.inc.php');

lmb_tests_init_db_dsn();

lmb_tests_setup_db(dirname(__FILE__) . '/.fixture/init_tests.');
