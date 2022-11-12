<?php
define('LIMB_TEST_DB_DSN', 'mysql://root:test@localhost/limb_test?charset=utf8');

require(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require(dirname(__FILE__) . '/../../core/cases/init.inc.php');
require(dirname(__FILE__) . '/../../dbal/cases/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__).'/../../../var/active_record');

lmb_tests_init_db_dsn();

return lmb_tests_db_dump_does_not_exist(dirname(__FILE__) . '/.fixture/init_tests.', 'ACTIVE_RECORD');