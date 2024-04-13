<?php

use limb\active_record\src\toolkit\lmbARTools;
use limb\toolkit\src\lmbToolkit;

lmbToolkit::merge(new lmbARTools());

$dsn = 'mysql_dsn';

lmbToolkit::instance()->setDefaultDbDSN(lmbToolkit::instance()->getDbDSNByName($dsn));

lmb_tests_init_db_dsn($dsn);

lmb_tests_setup_db(dirname(__FILE__) . '/.fixture/init_tests.');
