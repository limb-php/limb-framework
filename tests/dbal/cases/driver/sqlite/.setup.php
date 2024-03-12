<?php

use limb\toolkit\src\lmbToolkit;

$dsn = 'sqlite_dsn';

lmbToolkit::instance()->setDefaultDbDSN(lmbToolkit::instance()->getDbDSNByName($dsn));

lmb_tests_init_db_dsn($dsn);
