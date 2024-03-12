<?php

use limb\toolkit\src\lmbToolkit;

$dsn = 'mysql_dsn';

try {
    lmbToolkit::instance()->setDefaultDbDSN(lmbToolkit::instance()->getDbDSNByName($dsn));

    lmb_tests_init_db_dsn($dsn);
} catch (Exception $e) {

}
