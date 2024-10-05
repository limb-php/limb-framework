<?php

use limb\toolkit\lmbToolkit;

lmbToolkit::instance()->setDefaultDbDSN(lmbToolkit::instance()->getDbDSNByName('mysql_dsn'));

lmb_tests_setup_db(dirname(__FILE__) . '/.fixture/init_tests.');
